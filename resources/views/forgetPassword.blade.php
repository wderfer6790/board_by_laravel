@extends('layouts.login')

@section('content')
    <div class="row" style="padding-top: 8rem;">
        <label for="email" class="form-label col-md-2">email</label>
        <div class="col-md-10">
            <input type="text" id="email" name="email" class="form-control" value="">
            <div class="invalid-feedback" data-for="email"></div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-12">
            <input type="button" id="send_email_btn" class="btn btn-dark form-control" value="send reset password email">
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-12 text-end">
            <a href="{{ route('login') }}" class="link-dark">return login</a>
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
    $("#send_email_btn").click(function() {
        if (processing) return false;

        let email = $("#email").val().trim();
        if (email.length === 0) {
            alert('이메일을 입력해주세요.');
            $("#email").focus();
            return false;
        }

        processing = true;
        $.ajax({
            url: '{{ route('forgetPasswordEmail') }}',
            type: 'post',
            data: {
                email: email,
            },
            success: function(data) {
                alert(data.msg);
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
