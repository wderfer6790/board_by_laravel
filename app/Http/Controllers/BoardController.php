<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, Article, Reply, File};

class BoardController extends Controller
{
    public function list() {
        return view('list');
    }

    public function getArticles(Request $request) {
        $res = ['res' => false, 'msg' => ""];
        if (!$request->isXmlHttpRequest()) {
            $res['msg'] = "잘못된 접근입니다.";
            goto sendRes;
        }

        sendRes:
        return response()->json($res);
    }

    public function create() {
        return view('create');
    }

    public function store(Request $request) {
        $res = ['res' => false, 'msg' => ""];
        if (!$request->isXmlHttpRequest()) {
            $res['msg'] = "잘못된 접근입니다.";
            goto sendRes;
        }

        sendRes:
        return response()->json($res);
    }

    public function article(Request $request, $id) {
        $subject = 'test subject';
        return view('article', compact('subject'));
    }

    public function edit($id) {
        $article = [];
        return view('edit', compact('article'));
    }

    public function update(Request $request, $id) {
        $res = ['res' => false, 'msg' => ""];
        if (!$request->isXmlHttpRequest()) {
            $res['msg'] = "잘못된 접근입니다.";
            goto sendRes;
        }

        sendRes:
        return response()->json($res);
    }

    public function destroy(Request $request, $id) {
        $res = ['res' => false, 'msg' => ""];
        if (!$request->isXmlHttpRequest()) {
            $res['msg'] = "잘못된 접근입니다.";
            goto sendRes;
        }

        sendRes:
        return response()->json($res);
    }
}
