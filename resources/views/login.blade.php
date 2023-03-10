@extends('layouts.main', ['title' => 'LOGIN'])

@section('content')
    <div class="row mt-3">
        <label for="email" class="form-label col-md-2">email</label>
        <div class="col-md-10">
            <input type="text" id="email" name="email" class="form-control" value="nienow.piper@example.com">
            <div class="invalid-feedback" data-for="email"></div>
        </div>
    </div>
    <div class="row mt-3">
        <label for="password" class="form-label col-md-2">password</label>
        <div class="col-md-10">
            <input type="password" id="password" name="password" class="form-control" value="nienow.p">
            <div class="invalid-feedback" data-for="password"></div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="form-check form-switch align-content-end">
            <input type="checkbox" class="form-check-input" role="switch" id="remember" name="remember" checked>
            <label class="form-check-label" for="remember">Remember</label>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-12">
            <input type="button" id="login_btn" class="btn btn-primary form-control" value="login">
        </div>
    </div>
@endsection

@section('footerScript')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            // 'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr('content')
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
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
                    alert(data.msg);
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
    })
});
</script>
@endsection
