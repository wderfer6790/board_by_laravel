@extends('layouts.login')

@section('content')
    <div class="row" style="padding-top: 8rem;">
        <label for="email" class="form-label col-md-2">email</label>
        <div class="col-md-10">
            <input type="text" id="email" name="email" class="form-control" value="wderfer6790@nate.com">
            <div class="invalid-feedback" data-for="email"></div>
        </div>
    </div>
    <div class="row mt-3">
        <label for="username" class="form-label col-md-2">name</label>
        <div class="col-md-10">
            <input type="text" id="username" name="username" class="form-control" value="sya">
            <div class="invalid-feedback" data-for="username"></div>
        </div>
    </div>
    <div class="row mt-3">
        <label for="password" class="form-label col-md-2">password</label>
        <div class="col-md-10">
            <input type="password" id="password" name="password" class="form-control" value="qwer">
            <div class="invalid-feedback" data-for="password"></div>
        </div>
    </div>
    <div class="row mt-3">
        <label for="password_check" class="form-label col-md-2">password check</label>
        <div class="col-md-10">
            <input type="password" id="password_check" name="password_check" class="form-control" value="qwer">
            <div class="invalid-feedback" data-for="password_check"></div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-12">
            <input type="button" id="signin_btn" class="btn btn-dark form-control" value="sign in">
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
    $("#signin_btn").click(function() {
        if (processing) return false;

        if (!confirm("작성한 내용으로 가입하시겠습니까?")) return false;

        processing = true;

        $.ajax({
            url: '{{ route('signinProcess') }}',
            type: 'post',
            data: {
                'email': $("#email").val().trim(),
                'username': $("#username").val().trim(),
                'password': $("#password").val().trim(),
                'password_check': $("#password_check").val().trim(),
            },
            success: function(data) {
                if (data.res) {
                    alert(data.msg);
                    location.href = "{{ route('login') }}";

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
