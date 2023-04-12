<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\{User, Article, Reply, File};

class ReplyController extends Controller
{
    public function store(Request $request, $article_id, $parent_id = null) {
        $res = ['res' => false, 'msg' => ""];
        if (!$request->isXmlHttpRequest()) {
            $res['msg'] = "잘못된 접근입니다.";
            goto sendRes;
        }

        $content = $request->input('content');
        if (!$content) {
            $res['msg'] = "댓글을 입력해주세요.";
            goto sendRes;
        }

        $user = auth()->user();
        $reply = new Reply([
            'article_id' => $article_id,
            'user_id' => $user->id,
            'parent_id' => $parent_id,
            'content' => $content
        ]);

        if (!$reply->save()) {
            $res['msg'] = "문제가 발생하였습니다. 다시 시도해주세요.";
            goto sendRes;
        }

        $res['res'] = true;
        $res['reply_id'] = $reply->id;
        $res['author'] = $user->name;
        $res['author_thumbnail'] = $user->file->count() > 0 ? asset($user->file->get(0)->path) : asset('storage/image/no_image.png');
        $res['publish_date'] = date('H:i y/m/d', strtotime($reply->updated_at));
        $res['content'] = $reply->content;
        $res['image'] = $reply->file->count() > 0 ? asset($reply->file->get(0)->path) : "";

        sendRes:
        return response()->json($res);
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
