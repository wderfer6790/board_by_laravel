<html>
<head>
    <meta charset="utf-8">
</head>
<body>
<div>
    <h1 style="width: 100%;position:relative;background-color: #edf2f7;padding: 10px 0 40px 0;color: #4b4b4b;">{{ config('app.name') }}</h1>
    <div style="width: 50%;margin: 0 auto;background-color: #ffffff;padding: 20px;">
        <h1>이메일 인증</h1>
        <p>가입을 완료하기 위해 아래 버튼을 클릭하여 이메일을 인증해주세요.</p>
        <form action="{{ route('verifyEmail') }}" method="post" target="_blank" style="width: 100%;text-align: center;">
            @csrf
            <input type="hidden" name="auth" value="{{ $auth }}">
            <input type="submit" value="이메일 인증" style="padding: 10px 20px;font-weight: 600;color: white;border: none;border-radius: 5px;background-color: #48bb78;cursor: pointer;">
        </form>
        <p>감사합니다, <b>{{ $user->name }}</b></p>
    </div>
</div>
</body>
</html>
