<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Models\{User, Article, Reply, File};
use Illuminate\Support\Facades\{File as FileInfo, Storage, DB, Auth};

class BoardController extends Controller
{
    public function list()
    {
        return view('list');
    }

    public function getArticles(Request $request)
    {
        $res = ['res' => false, 'msg' => "", 'articles' => []];
        if (!$request->isXmlHttpRequest()) {
            $res['msg'] = "잘못된 접근입니다.";
            goto sendRes;
        }

        $init = $request->boolean('init');
        $total = $request->input('total', 0);
        $sort = $request->input('sort', '-updated_at');
        $search = $request->input('search', '');

        $col = ltrim($sort, "-|+");
        $order = substr($sort, 0, 1) === '-' ? 'desc' : 'asc';

        $skip = $total;
        $take = 30;
        if ($init && $total > 0) {
            $skip = 0;
            $take = $total;
        }

        $article_ids = [];
        if ($search) {
            $tmp = User::join('article', 'user.id', 'article.user_id')
                ->where('user.name', 'LIKE', "%{$search}%")
                ->selectRaw("1 AS search_order, article.id, user.id AS user_id, article.views, article.likes, article.updated_at")
                ->unionAll(Article::where('subject', 'LIKE', "%{$search}%")
                    ->orWhere('content', 'LIKE', "%{$search}%")
                    ->selectRaw("2 AS search_order, id, user_id, views, likes, updated_at"))
                ->orderBy('search_order')
                ->orderBy($col, $order)
                ->get();

            $article_ids = $tmp->pluck('id')->toArray();
        }

        $collection = Article::with(['user:id,name', 'files:id,path'])
            ->when(!empty($article_ids), function ($q) use ($article_ids, $skip, $take) {
                $q->whereIn('id', $article_ids)
                    ->orderByRaw("FIELD(id, " . implode(",", $article_ids) . ")")
                    ->skip($skip)
                    ->take($take);
            }, function ($q) use ($col, $order, $skip, $take) {
                $q->orderBy($col, $order)
                    ->skip($skip)
                    ->take($take);
            })->get();

        foreach ($collection as $row) {
            $article = [
                'id' => $row->id,
                'author' => $row->user->name,
                'subject' => $row->subject,
                'content' => substr($row->content, 0, 256),
                'likes' => $row->likes,
                'views' => $row->views,
                'updated_at' => date('y/m/d', strtotime($row->updated_at)),
            ];

            $no_image = asset('storage/image/no_image.png');
            $article['profile'] = $row->user->file->get(0)->count() > 0 ? asset($row->user->file->get(0)->path) : $no_image;
            $article['thumbnail'] = $row->files->count() > 0 ? asset($row->files->get(0)->path) : $no_image;

            $res['articles'][] = $article;
        }

        $res['total'] = $total + $collection->count();
        $res['sort'] = $sort;
        $res['search'] = $search;
        sendRes:
        return response()->json($res);
    }

    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {
        $res = ['res' => false, 'msg' => ""];

        if (!$request->isXmlHttpRequest()) {
            $res['msg'] = "잘못된 접근입니다.";
            goto sendRes;
        }

        $subject = $request->post('subject');
        $content = $request->post('content', '');
        $uploaded_files = $request->post('uploaded_files', []);
        $embedded_files = $request->post('embedded_files', []);

        $embedded_files = array_filter($embedded_files, function($val) {
            return strlen($val) > 0;
        });

        array_walk_recursive($content, function(&$item, $key) {
            if ($key === 'insert') {
                $item = $item ?? "\n";
                $item = str_replace("\n", "\\n", $item);
            }
        });

        $article = new Article([
            'subject' => $subject,
            'content' => serialize($content)
        ]);

        // TODO article store find user
        if (User::find(7)->articles()->save($article)) {
            // error handling
        }

        /*
        if (Auth::user()->articles()->save($article)) {
            // error handling
        }
        */

        // fileable sync
        $article->files()->sync($embedded_files);

        // other file delete
        $delete_files = array_diff($uploaded_files, $embedded_files);
        File::whereIn('id', $delete_files)->delete();

        $content = unserialize($article->content);
        array_walk_recursive($content, function(&$item, $key) {
            if ($key === 'insert') {
//                $item = str_replace("\\n", "\n", $item);
                $item = stripslashes($item);
            }
        });

        $res['res'] = true;
        $res['content'] = $content;
        $res['article_id'] = $article->id;

        sendRes:
        return response()->json($res);
    }

    public function article(Request $request, $id)
    {
        $article = Article::with(["user:id,name", "files:id,path", 'replies' => function ($q) {
            $q->with(['user' => function ($q) {
                $q->with('file:id,path');
            }, 'file:id,path']);
        }])
            ->where('id', $id)
            ->first();

        $content = unserialize($article->content);
        array_walk_recursive($content, function(&$item, $key) {
            if ($key === 'insert') {
                $item = str_replace("\\n", "\n", $item);
            }
        });
        $content = json_encode($content);

        return view('article', compact('article', 'content'));
    }

    public function edit($id)
    {
        $article = [];
        return view('edit', compact('article'));
    }

    public function update(Request $request, $id)
    {
        $res = ['res' => false, 'msg' => ""];
        if (!$request->isXmlHttpRequest()) {
            $res['msg'] = "잘못된 접근입니다.";
            goto sendRes;
        }

        sendRes:
        return response()->json($res);
    }

    public function destroy(Request $request, $id)
    {
        $res = ['res' => false, 'msg' => ""];
        if (!$request->isXmlHttpRequest()) {
            $res['msg'] = "잘못된 접근입니다.";
            goto sendRes;
        }

        sendRes:
        return response()->json($res);
    }
}
