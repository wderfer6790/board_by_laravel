<html>
<head>
    <meta charset="utf-8">
</head>
<body>
<div>
    <h1 style="width: 100%;position:relative;background-color: #edf2f7;padding: 10px 0 40px 0;color: #4b4b4b;">{{ config('app.name') }}</h1>
    <div style="width: 50%;margin: 0 auto;background-color: #ffffff;padding: 20px;">
        <h1>비밀번호 재설정</h1>
        <p>아래 링크를 통해 비밀번호 재설정 메뉴로 넘어갑니다.</p>
        <a href="{{ route('resetPassword', $auth) }}" style="text-decoration: none; font-weight: bold;color: #212529;cursor: pointer;">reset password</a>
        <p>감사합니다, <b>{{ $user->name }}</b></p>
    </div>
</div>
</body>
</html>
