@extends('layouts.main')

@section('content')
    <h1 class="border-bottom mb-5">PROFILE</h1>
    <div class="row">
        <label for="email" class="form-label col-md-2">E-MAIL</label>
        <div class="col-md-10">
            {{ $user->email }}
        </div>
    </div>
    <div class="row mt-5">
        <label for="username" class="form-label col-md-2">사용자 이름</label>
        <div class="col-md-10">
            <input type="text" id="username" name="username" class="form-control" value="{{ $user->name }}">
            <div class="invalid-feedback" data-for="username"></div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-2 float-start">
            비밀번호 변경
        </div>
        <div class="col-10 float-end password_box">
            <div class="col-5 float-start">
                <input type="password" id="password" name="password" class="form-control" placeholder="비밀번호" value="">
                <div class="invalid-feedback" data-for="password"></div>
            </div>
            <div class="offset-2 col-5 float-end">
                <input type="password" id="password_check" name="password_check" class="form-control" placeholder="비밀번호 확인" value="">
                <div class="invalid-feedback" data-for="password_check"></div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="float-start col-4">
                <img src="{{ $thumbnail }}" class="img-thumbnail rounded-circle">
            </div>
            <div class="float-end col-8">
                <div class="input-group mt-2">
                    <input type="file" id="upload_file" class="form-control col-md-9 col-sm-6">
                    <input type="button" id="file_upload_btn" class="btn btn-dark col-md-3 col-sm-4" value="이미지 업로드">
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-12">
            <input type="button" id="signin_btn" class="btn btn-dark form-control" value="사용자 정보 수정">
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr('content')
                }
            });

            let processing = false;
            $("#signin_btn").click(function () {
                if (processing) return false;

                if (!confirm("수정하시겠습니까?")) return false;

                processing = true;
                $.ajax({
                    url: '{{ route('profileUpdate') }}',
                    type: 'post',
                    data: {
                        'username': $("#username").val().trim(),
                        'password': $("#password").val().trim(),
                        'password_check': $("#password_check").val().trim(),
                        'uploaded_thumbnail_id' : uploaded_thumbnail_id,
                    },
                    success: function (data) {
                        if (data.res) {
                            alert(data.msg);

                        } else {
                            if (typeof data.msg === 'string') {
                                alert(data.msg);
                            } else {
                                $.each(data.msg, function (id, msg) {
                                    $("#" + id).addClass('is-invalid');
                                    $("div.invalid-feedback[data-for=" + id + "]").text(msg[0]);
                                });

                                $("#" + Object.keys(data.msg)[0]).focus();
                            }
                        }

                        processing = false;
                    },
                    error: function (xhr) {
                        processing = false;
                        console.log(xhr);
                    }
                });
            });

            // file upload
            let uploaded_thumbnail_id;
            $("#file_upload_btn").click(function () {
                let file = $("#upload_file");
                if (file.val().length === 0) {
                    alert("파일을 선택해주세요.");
                    return false;
                }

                let formData = new FormData();
                formData.append('upload_file', file.get(0).files[0]);

                $.ajax({
                    url: '{{ route('upload') }}',
                    type: 'post',
                    contentType: false,
                    processData: false,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        if (!data.res) {
                            alert(data.msg);
                            return false;
                        }

                        $("img.img-thumbnail").attr('src', data.src);
                        uploaded_thumbnail_id = data.file_id;
                    },
                    error: function (xhr) {
                        console.log(xhr);
                    }
                });
            });
        });
    </script>
@endsection

@section('style')
    <style>
        img.img-thumbnail {
            width: 200px;
            height: 200px;
        }
    </style>
@endsection
