<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login( Request $request) {
        $res = ['res' => false, 'msg' => ""];
        if (!$request->isXmlHttpRequest()) {
            $res['msg'] = "잘못된 접근입니다.";
            goto sendRes;
        }
//
//        auth()->logout();
//        $request->session()->invalidate();
//        $request->session()->regenerateToken();
//        goto sendRes;

        $data = $request->only(['email', 'password', 'remember']);

        // validate
        $validator = Validator::make([
            'email' => $data['email'],
            'password' => $data['password']
        ], [
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => "이메일을 입력해주세요.",
            'email.email' => "이메일 형식이 올바르지 않습니다.",
            'password.required' => "비밀번호를 입력해주세요.",
        ]);

        // authentication
        if ($validator->fails()) {
            $res['msg'] = $validator->errors()->messages();
            goto sendRes;
        }

        if(auth()->attempt([
            'email' => $data['email'],
            'password' => $data['password']
        ], $data['remember'] ?? false)) {
            $user = User::where('email', $data['email'])->first();

//            $request->session()->regenerate();

            $res['res'] = true;
            $res['msg'] = "로그인되었습니다.";
        } else {
            $res['msg'] = "이메일 또는 비밀번호가 올바르지 않습니다.";
        }

        sendRes:
        return response()->json($res);
    }

    public function logout(Request $request) {
        auth()->logout();
//        $request->session()->invalidate();
//        $request->session()->regenerateToken();

        return redirect('login');
    }
}
