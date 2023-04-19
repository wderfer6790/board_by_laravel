@extends('layouts.login')

@section('content')
    <h1 class="border-bottom mb-5">LOGIN</h1>
    <div class="row">
        <label for="email" class="form-label col-md-2">email</label>
        <div class="col-md-10">
            <input type="text" id="email" name="email" class="form-control" value="">
            <div class="invalid-feedback" data-for="email"></div>
        </div>
    </div>
    <div class="row mt-3">
        <label for="password" class="form-label col-md-2">password</label>
        <div class="col-md-10">
            <input type="password" id="password" name="password" class="form-control" value="">
            <div class="invalid-feedback" data-for="password"></div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col">
            <div class="float-end form-check form-switch">
                <input type="checkbox" class="form-check-input" role="switch" id="remember" name="remember">
                <label class="form-check-label" for="remember">로그인 유지하기</label>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-12">
            <input type="button" id="login_btn" class="btn btn-dark form-control" value="login">
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12 text-end">
            <a href="{{ route('signin') }}" class="link-dark">sign in?</a>
            or <a href="{{ route('forgetPassword') }}" class="link-dark">forget password?</a>
        </div>
    </div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr('content')
        }
    });

    let processing = false;
    $("#login_btn").click(function() {
        if (processing) {
            return false;
        }

        processing = true;
        $.ajax({
            url: '{{ route('login') }}',
            type: 'post',
            data: {
                'email': $("#email").val().trim(),
                'password': $("#password").val().trim(),
                'remember': $("#remember").is(":checked")
            },
            success: function(data) {
                if (data.res) {
                    location.href = "{{ route('list') }}";

                } else {
                    if (typeof data.msg === 'string') {
                        alert(data.msg);
                    } else {
                        $.each(data.msg, function(id, msg) {
                            $("#" + id).addClass('is-invalid');
                            $("div.invalid-feedback[data-for=" + id + "]").text(msg[0]);
                        });

                        $("#" + Object.keys(data.msg)[0]).focus();
                    }
                }

                processing = false;
            },
            error: function(xhr) {
                processing = false;
                console.log(xhr);
            }
        });
    });
});
</script>
@endsection

@section('style')
    <style>
        .form-check-input, .form-check-label {
            cursor: pointer;
        }
    </style>
@endsection
