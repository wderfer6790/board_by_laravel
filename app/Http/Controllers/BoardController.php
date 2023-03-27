<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, Article, Reply, File};
use Illuminate\Support\Facades\{File as FileInfo, Storage};

class BoardController extends Controller
{
    public function list()
    {
        $dir = Storage::disk('public');

        $image = collect($dir->Files('image/sample'))->random();

        $article = Article::find(5);

        dd(
            $article,
            $article->files
        );



        return view('list');
    }

    public function getArticles(Request $request)
    {
        $res = ['res' => false, 'msg' => "", 'total' => 0, 'articles' => []];
        if (!$request->isXmlHttpRequest()) {
            $res['msg'] = "잘못된 접근입니다.";
            goto sendRes;
        }

        $filter = $request->input('filter', '');

        $sort = $request->input('sort', '-updated_at');
        $col = ltrim($sort, "-|+");
        $order = substr($sort, 0, 1) === '-' ? 'desc' : 'asc';

        $total = $request->input('total', 0);
        $item_per_request = 9;


        $collection = Article::with('user:id,name', 'file:id,files')
            ->orderBy($col, $order)
            ->skip($total)
            ->take($item_per_request)
            ->get();

        $no_thumbnail = [['path' => asset('storage/image/no_thumbnail.png')]];
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

            $files = $row->file ? json_decode($row->file->files) : $no_thumbnail;
            $article['thumbnail'] = array_pop($files)['path'];

            $res['articles'][] = $article;
        }

        $res['total'] = $total + $collection->count();

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

        sendRes:
        return response()->json($res);
    }

    public function article(Request $request, $id)
    {
        $article = Article::with("user:id,name", "file:id,files")
            ->where('id', $id)
            ->first();
        return view('article', compact('article'));
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
