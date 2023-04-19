<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, Article, File};
use Illuminate\Support\Facades\{File as FileInfo};

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
                'likes' => $row->likes,
                'views' => $row->views,
                'updated_at' => date('y/m/d', strtotime($row->updated_at)),
            ];

            $no_image = asset('storage/image/no_image.png');
            $article['profile'] = $row->user->file->count() > 0 ? asset($row->user->file->get(0)->path) : $no_image;
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

        $embedded_files = array_filter($embedded_files, function ($val) {
            return strlen($val) > 0;
        });

        array_walk_recursive($content, function (&$item, $key) {
            if ($key === 'insert') {
                $item = $item ?? "\n";
                $item = str_replace("\n", "\\n", $item);
            }
        });

        $article = new Article([
            'subject' => $subject,
            'content' => serialize($content)
        ]);
        if (!auth()->user()->articles()->save($article)) {
            // error handling
        }


        // morph sync
        $article->files()->sync($embedded_files);

        // other uploaded file delete
        $delete_file_ids = array_diff($uploaded_files, $embedded_files);
        if (!empty($delete_file_ids)) {
            $delete_files = File::whereIn('id', $delete_file_ids)->get();
            $delete_files->map(function ($file) {
                FileInfo::delete($file->path);
                $file->delete();
            });
        }

        $res['res'] = true;
        $res['id'] = $article->id;

        sendRes:
        return response()->json($res);
    }

    public function article($id)
    {
            $article = Article::with(["user:id,name", "files:id,path", 'replies' => function ($q) {
                $q->with(['user' => function ($q) {
                    $q->with('file:id,path');
                }, 'file:id,path']);
            }])
                ->where('id', $id)
                ->first();

            if (!$article) {
                return view('alert', ['msg' => "게시글 정보를 찾을 수 없습니다.", 'to' => 'list']);
            }

            $article->update(['views' => $article->views + 1]);

            $content = unserialize($article->content);
            array_walk_recursive($content, function (&$item, $key) {
                if ($key === 'insert') {
                    $item = str_replace("\\n", "\n", $item);
                }
            });
            $content = json_encode($content);
            return view('article', compact('article', 'content'));
    }

    public function edit($id)
    {
        $article = Article::with("user:id,name")
            ->where('id', $id)
            ->first();

        $content = unserialize($article->content);
        array_walk_recursive($content, function (&$item, $key) {
            if ($key === 'insert') {
                $item = str_replace("\\n", "\n", $item);
            }
        });
        $content = json_encode($content);

        $collection = $article->files()->get(["id AS file_id", "path AS src"]);
        $collection->map(function ($file) {
            $file->src = asset($file->src);
        });
        $attach_files = $collection->toArray();


        return view('edit', compact('article', 'content', 'attach_files'));
    }

    public function update(Request $request, $id)
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

        $embedded_files = array_filter($embedded_files, function ($val) {
            return strlen($val) > 0;
        });

        array_walk_recursive($content, function (&$item, $key) {
            if ($key === 'insert') {
                $item = $item ?? "\n";
                $item = str_replace("\n", "\\n", $item);
            }
        });

        $article = Article::find($id);
        if (!$article->update([
            'subject' => $subject,
            'content' => serialize($content)
        ])) {
            $res['msg'] = "수정 중 문제가 발생하였습니다.";
            goto sendRes;
        }

        // morph sync
        $article->files()->sync($embedded_files);

        // other uploaded file delete
        $delete_file_ids = array_diff($uploaded_files, $embedded_files);
        if (!empty($delete_file_ids)) {
            $delete_files = File::whereIn('id', $delete_file_ids)->get();
            $delete_files->map(function ($file) {
                FileInfo::delete($file->path);
                $file->delete();
            });
        }

        $res['res'] = true;
        $res['msg'] = "수정되었습니다.";

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

        if (!$article = Article::find($id)) {
            $res['msg'] = "삭제할 대상을 찾을 수 없습니다.";
            goto sendRes;
        }

        $article->files()->delete();
        $article->delete();

        $res['res'] = true;
        $res['msg'] = "삭제되었습니다.";

        sendRes:
        return response()->json($res);
    }
}
