<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\{Validator, Hash};

class UserController extends Controller
{
    public function profile() {
        $user = auth()->user();
        $thumbnail = $user->file->count() > 0 ? asset($user->file->get(0)->path) : asset('storage/image/no_image.png');

        return view('profile', compact('user', 'thumbnail'));
    }

    public function update(Request $request) {
        $res = ['res' => false, 'msg' => ""];
        if (!$request->isXmlHttpRequest()) {
            $res['msg'] = "잘못된 접근입니다.";
            goto sendRes;
        }

        $data = $request->only('username', 'uploaded_thumbnail_id', 'password', 'password_check');

        // validate
        $validator = Validator::make([
            'username' => $data['username'],
            'password' => $data['password'],
            'password_check' => $data['password_check'],
        ], [
            'username' => 'required|alpha_num|min:2',
            'password' => '',
            'password_check' => 'same:password',
        ], [
            'username.required' => "사용할 이름을 입력해주세요.",
            'username.alpha_num' => "사용자명에 특수문자는 사용할 수 없습니다.",
            'username.min' => "사용자 이름을 두 자 이상 입력해주세요.",
            'password' => '',
            'password_check.same' => "비밀번호와 비밀번호 확인이 일치하지 않습니다.",
        ]);

        // authentication
        if ($validator->fails()) {
            $res['msg'] = $validator->errors()->messages();
            goto sendRes;
        }

        $updateData = [
            'name' => $data['username']
        ];
        if ($data['password']) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user = auth()->user();
        if (!$user->update($updateData)) {
            $res['msg'] = "수정 중 문제가 발생하였습니다.";
            goto sendRes;
        }

        if ($data['uploaded_thumbnail_id']) {
            $user->file()->sync([$data['uploaded_thumbnail_id']]);
        }

        $res['res'] = true;
        $res['msg'] = "수정되었습니다.";

        sendRes:
        return response()->json($res);
    }

}
