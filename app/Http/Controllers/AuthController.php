<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator, Hash, Mail};
use App\Models\User;
use App\Mail\{VerifyEmail, ResetPassword};

class AuthController extends Controller
{
    public function login( Request $request) {
        $res = ['res' => false, 'msg' => ""];
        if (!$request->isXmlHttpRequest()) {
            $res['msg'] = "잘못된 접근입니다.";
            goto sendRes;
        }

        $email = $request->input('email');
        $password = $request->input('password');

        // validate
        $validator = Validator::make([
            'email' => $email,
            'password' => $password
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
            'email' => $email,
            'password' => $password
        ], $request->boolean('remember'))) {
            $user = User::where('email', $email)->first();
            if (!$user->email_verified_at) {
                Mail::to($user)->send(new VerifyEmail($user));
                $res['msg'] = '인증 이메일을 발송하였습니다. 이메일 인증 후 로그인 해주세요.';
                goto sendRes;
            }
            $res['res'] = true;
        } else {
            $res['msg'] = "이메일 또는 비밀번호가 올바르지 않습니다.";
        }

        sendRes:
        return response()->json($res);
    }

    public function logout(Request $request) {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('list'));
    }

    public function signin(Request $request) {
        $res = ['res' => false, 'msg' => ""];
        if (!$request->isXmlHttpRequest()) {
            $res['msg'] = "잘못된 접근입니다.";
            goto sendRes;
        }

        $data = $request->only(['email', 'password', 'password_check', 'username']);

        // validate
        $validator = Validator::make([
            'email' => $data['email'],
            'password' => $data['password'],
            'password_check' => $data['password_check'],
            'username' => $data['username']
        ], [
            'email' => 'required|email',
            'password' => 'required|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,32}$/',
            'password_check' => 'required|same:password',
            'username' => 'required|alpha_num|min:2'
        ], [
            'email.required' => "이메일을 입력해주세요.",
            'email.email' => "이메일 형식이 올바르지 않습니다.",
            'password.required' => "비밀번호를 입력해주세요.",
            'password.regex' => "8자 이상 32자 이하 영문, 숫자, 특수문자(!@#$%^&*)를 혼합해주세요.",
            'password_check.required' => "비밀번호 확인을 입력해주세요.",
            'password_check.same' => "비밀번호와 비밀번호 확인이 일치하지 않습니다.",
            'username.required' => "사용할 이름을 입력해주세요.",
            'username.alpha_num' => "사용자명에 특수문자는 사용할 수 없습니다.",
            'username.min' => "사용자 이름을 두 자 이상 입력해주세요.",
        ]);

        // authentication
        if ($validator->fails()) {
            $res['msg'] = $validator->errors()->messages();
            goto sendRes;
        }

        $user =  new User([
            'email' => $data['email'],
            'name' => $data['username'],
            'password' => Hash::make($data['password']),
        ]);

        if (!$user->save()) {
            $res['msg'] = "가입 중 문제가 발생하였습니다.";
            goto sendRes;
        }

        Mail::to($user)->send(new VerifyEmail($user));

        $res['res'] = true;
        $res['msg'] = "인증 이메일을 발송하였습니다. 이메일을 확인해주세요.";

        sendRes:
        return response()->json($res);
    }

    public function verifyEmail(Request $request) {
        $user = User::whereRaw("SHA2(email, 256) = '" . $request->post('auth') . "'")->first();
        if (!$user) {
            return view('alert', [
                'to' => 'login',
                'msg' => "잘못된 접근입니다."
            ]);
        }

        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->save();

        $name = $user->name;

        return view('verifyEmail', ['name' => $name]);
    }

    public function resendEmail(Request $request) {
        $res = ['res' => false, 'msg' => ""];
        if (!$request->isXmlHttpRequest() || $email = $request->post('email')) {
            $res['msg'] = "잘못된 접근입니다.";
            goto sendRes;
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            $res['msg'] = "회원정보를 찾을 수 없습니다. 다시 가입해주시기 바랍니다.";
            goto sendRes;
        }

        if (!is_null($user->email_verified_at)) {
            $res['msg'] = "이미 이메일 인증이 완료된 상태입니다.";
            goto sendRes;
        }

        Mail::to($user)->send(new VerifyEmail($user));
        $res['msg'] = "인증 이메일을 재발송하였습니다.";

        sendRes:
        return response()->json($res);
    }

    public function forgetPassword(Request $request) {
        $res = ['res' => false, 'msg' => ""];
        if (!$request->isXmlHttpRequest() || !$email = $request->post('email')) {
            $res['msg'] = "잘못된 접근입니다.";
            goto sendRes;
        }

        if (!$user = User::where('email', $email)->first()) {
            $res['msg'] = "가입된 정보를 찾을 수 없습니다.";
            goto sendRes;
        }

        Mail::to($user)->send(new ResetPassword($user));

        $res['res'] = true;
        $res['msg'] = "비밀번호 재설정 이메일을 발송하였습니다.";

        sendRes:
        return response()->json($res);
    }


    public function resetPassword($auth) {
        $email = User::whereRaw("SHA2(email, 256) = '{$auth}'")->value('email');
        if (!$email) {
            return view('alert', [
                'to' => 'login',
                'msg' => "잘못된 접근입니다."
            ]);
        }

        return view('resetPassword', compact('auth', 'email'));
    }


    public function resetPasswordProcess(Request $request) {
        $res = ['res' => false, 'msg' => ""];
        if (!$request->isXmlHttpRequest()) {
            $res['msg'] = "잘못된 접근입니다.";
            goto sendRes;
        }

        $data = $request->only(['auth', 'password', 'password_check']);

        $user = User::whereRaw("SHA2(email, 256) = '{$data['auth']}'")->first();
        if (!$user) {
            $res['msg'] = "사용자 정보를 찾을 수 없습니다.";
            goto sendRes;
        }

        $validator = Validator::make([
            'password' => $data['password'],
            'password_check' => $data['password_check'],
        ], [
            'password' => 'required|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,32}$/',
            'password_check' => 'required|same:password',
        ], [
            'password.required' => "비밀번호를 입력해주세요.",
            'password.regex' => "8자 이상 32자 이하 영문, 숫자, 특수문자(!@#$%^&*)를 혼합해주세요.",
            'password_check.required' => "비밀번호 확인을 입력해주세요.",
            'password_check.same' => "비밀번호와 비밀번호 확인이 일치하지 않습니다.",
        ]);

        // authentication
        if ($validator->fails()) {
            $res['msg'] = $validator->errors()->messages();
            goto sendRes;
        }

        if (!$user->update(['password' => Hash::make($data['password'])])) {
            $res['msg'] = "비밀번호 재설정 중 문제가 발생하였습니다.";
            goto sendRes;
        }

        $res['res'] = true;
        $res['msg'] = "비밀번호가 변경되었습니다.";

        sendRes:
        return response()->json($res);
    }


}
