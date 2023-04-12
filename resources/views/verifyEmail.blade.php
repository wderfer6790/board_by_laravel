@extends('layouts.login')
@section('content')
    <div class="row justify-content-md-center bg-light"  style="padding-top: 8rem;">
        <p class="h3 text-center">{{ config('app.name') }}</p>
        <div class="col col-md-6 bg-white text-center">
            @if($name)
            <p class="h4">감사합니다, {{ $name }} <small>이메일 인증 완료</small></p>
            <input type="button" class="btn btn-dark" value="로그인 하러가기" onclick="location.href='{{ route('login') }}';">
            @else
            <p class="h5 text-warning">이메일 인증에 문제가 발생했습니다.</p>
            <p class="h6">회원가입에서 <b>이메일 재발송</b>을 통해 다시 시도해주세요.</p>
            @endif
        </div>
    </div>
@endsection
@section('style')
@endsection
