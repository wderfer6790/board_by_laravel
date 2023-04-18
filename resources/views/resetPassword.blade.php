@extends('layouts.login')

@section('content')
    <div class="row" style="padding-top: 8rem;">
        <label for="email" class="form-label col-md-2">email</label>
        <div class="col-md-10">
            {{ $email }}
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
        <label for="password_check" class="form-label col-md-2">password check</label>
        <div class="col-md-10">
            <input type="password" id="password_check" name="password_check" class="form-control" value="">
            <div class="invalid-feedback" data-for="password_check"></div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-12">
            <input type="button" id="reset_pw_btn" class="btn btn-dark form-control" value="reset password">
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
    $("#reset_pw_btn").click(function() {
        if (processing) return false;

        if (!confirm("비밀번호를 재설정 하시겠습니까?")) return false;

        processing = true;

        $.ajax({
            url: '{{ route('resetPasswordProcess') }}',
            type: 'post',
            data: {
                'auth': '{{ $auth }}',
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
                        $(".is-invalid").each(function() {
                            $(this).removeClass('is-invalid');
                        });

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
