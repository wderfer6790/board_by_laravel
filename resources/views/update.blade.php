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
            <input type="hidden" id="article_id" value="{{ $article_id }}">
            <!-- Create the editor container -->
            <div id="editor" class="rounded-bottom">
                <p>Hello World!</p>
                <p>Some initial <strong>bold</strong> text</p>
                격언은 인생을 현명하게 살아가는 데 도움을 주는 가르침과 훈계입니다.<br>
                금언(金言) 혹은 잠언(箴言)이라고도 하고요.<br>
                예를 들면 ‘시간은 금이다.’ 같은 말이지요.<br>
                속담이란 교훈이나 풍자를 하기 위해<br>
                어떤 사실을 비유의 방법으로 서술하는 간결한 관용어구입니다.<br>
                예를 들면 ‘소 잃고 외양간 고친다.’ 같은 말지요.<br>
                명언이란 유명한 사람의 입에서 나와 널리 알려진 말로,<br>
                간결하고 짧은 문장으로 교훈이나 가르침을 주는 말입니다.<br>
                예를 들면 소크라테스의 ‘악법도 법이다.’ 같은 말이지요.<br>
                가장 구별하기 쉬운 것이 명언입니다.<br>
                이 말은 누군가 그 말을 한 사람이 있는 것이고요.<br>
                '악법도 법이다.'라는 말은<br>
                소크라테스라는 유명한 철학자가 한 말이지요.<br>
                그러나 속담과 격언은 그 말을 한 사람이 누구인지 전해지지 않습니다.<br>
                속담과 격언이 좀 혼동이 되는데요.<br>
                속담은 우리나라에서 예로부터 전해오는<br>
            </div>
            <div class="file-upload mt-4">
                <div class="uploaded-files text-start"></div>
                <div class="input-group mt-2">
                    <input type="file" id="upload_file" class="form-control col-md-10 col-sm-6">
                    <input type="button" id="file_upload_btn" class="btn btn-dark col-md-2 col-sm-4" value="파일 업로드">
                </div>
                <small class="text-danger">※ 업로드 후 표시된 파일을 선택해야 내용에 반영됩니다.</small>
            </div>
            <div class="article-footer text-end mt-5">
                <input type="button" id="save_btn" class="btn btn-dark col-12" value="{{ $article_id ? "작 성" : "새 글 작성" }}">
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
                    url: '{{ route('board.process') }}',
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
                    url: '{{ route('file.upload') }}',
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
                    url: '{{ route('file.embed') }}',
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
                    url: '{{ route('file.delete') }}',
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
