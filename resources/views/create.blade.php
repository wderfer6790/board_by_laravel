@extends('layouts.article')

@section('content')
    <!-- article header -->
    <div class="row bg-light"  style="padding-top: 5rem;">
        <div class="col">
            <div class="article-header">
                <h3>제목</h3>
                <input type="text" class="form-control" id="subject">
            </div>
        </div>
    </div>
    <!-- article content -->
    <div class="row bg-light mt-4">
        <div class="col">
            <h3>내용</h3>
            <!-- Create the editor container -->
            <div id="editor" class="rounded-bottom"></div>
            <div class="file-upload mt-4">
                <div class="uploaded-files text-start"></div>
                <div class="input-group mt-2">
                    <input type="file" id="upload_file" class="form-control col-md-10 col-sm-6">
                    <input type="button" id="file_upload_btn" class="btn btn-dark col-md-2 col-sm-4" value="파일 업로드">
                </div>
                <small class="text-danger">※ 업로드 후 표시된 파일을 선택해야 내용에 반영됩니다.</small>
            </div>
            <div class="article-footer text-end mt-5">
                <input type="button" id="save_btn" class="btn btn-dark col-12" value="새 글 작성">
            </div>
        </div>
    </div>
@endsection

@section('footerScript')
    <script type="text/javascript">
        $(document).ready(function () {
            <!-- Initialize Quill editor -->
            let quill = new Quill('#editor', {
                theme: 'snow',
            });

            let quill_idx = null;
            quill.on('selection-change', function(range, oldRange, source) {
                quill_idx = range ? range.index : range;
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            // article add/mod
            $("#save_btn").click(function() {
                let method = 'post'
                let editor_data = {
                    contents: quill.getContents().ops
                };

                let article_id = $("#article_id").val();
                if (article_id && article_id.length > 0) {
                    method = 'put';
                    editor_data['id'] = article_id;
                }

                $.ajax({
                    url: '',
                    type: method,
                    data: editor_data,
                    dataType: 'json',
                    success: function(data) {
                        quill.setContents(data.contents);
                    },
                    error: function(xhr) {
                        console.log('ERROR :');
                        console.log(xhr);
                    }
                });
            });

            // file upload
            $("#file_upload_btn").click(function() {
                let file = $("#upload_file");
                if (file.val().length === 0) {
                    alert("파일을 선택해주세요.");
                    return false;
                }

                let f = new FormData();
                f.append('upload_file', file.get(0).files[0]);

                $.ajax({
                    url: '',
                    type: 'post',
                    contentType: false,
                    processData: false,
                    data: f,
                    dataType: 'json',
                    success: function(data) {
                        if (!data.res) {
                            alert(data.msg);
                            return false;
                        }

                        let img = $("<img class='img-thumbnail bg-light rounded' title='" + data.title + "' src='" + data.src + "' data-file_id='" + data.file_id + "'>");
                        let btn = $("<input type='button' class='btn-close' data-id='" + data.file_id + "'>");
                        let div = $("<div class='uploaded-file'></div>");
                        div.append(img);
                        div.append(btn);
                        $("div.uploaded-files").append(div);
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            });

            // uploaded file editor insert
            $(document).on("click", "div.uploaded-file img", function(e) {
                let file_id = $(this).data('file_id');
                $.ajax({
                    cache: false,
                    url: '',
                    type: 'get',
                    data: {
                        file_id: file_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (!data.res) {
                            alert(data.msg);
                        }

                        if (quill_idx === null) {
                            quill_idx = quill.getLength() - 1;
                        }

                        quill.insertEmbed(quill_idx, data.mime_type.split('/')[0], data.link);
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            });

            // uploaded file delete
            $(document).on("click", "div.uploaded-file input", function(e) {
                if (!confirm("파일을 삭제하시겠습니까?")) {
                    return false;
                }

                let btn = $(this);
                $.ajax({
                    url: '',
                    type: 'delete',
                    data: {
                        file_id: btn.data('id')
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (!data.res) {
                            alert(data.msg);
                            return false;
                        }

                        btn.parents("div.uploaded-file").remove();
                        $("#upload_file").val("");
                    },
                    error: function(xhr) {
                        console.log('ERROR :');
                        console.log(xhr);
                    }
                });

            });

        });
    </script>
@endsection

@section('style')
    <style>
        div.uploaded-file {
            display: inline-block;
            width: 8rem;
            height: 8rem;
            position: relative;
            margin-top: 0.4rem;
        }

        div.uploaded-file img {
            width: 75%;
            height: 75%;
            cursor: pointer;
        }

        div.uploaded-file img:active {
            outline: 0.1rem solid #212529;
            border-radius: 0.25rem;
        }

        .ql-container.ql-snow {
            height: auto;
        }
        .ql-toolbar.ql-snow {
            border-radius: 0.25rem 0.25rem 0 0;
        }
        .ql-editor {
            height: 30rem;
            overflow-y: scroll;
        }
    </style>
@endsection

@section('loadScript')
    <!-- Include stylesheet -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <!-- Include the Quill library -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
@endsection
