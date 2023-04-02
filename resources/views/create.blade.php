@extends('layouts.article')

@section('content')
    <!-- article header -->
    <div class="row bg-light" style="padding-top: 5rem;">
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
                    <input type="file" id="upload_file" class="form-control col-md-9 col-sm-6">
                    <input type="button" id="file_upload_btn" class="btn btn-dark col-md-3 col-sm-4" value="이미지 업로드">
                </div>
                <small class="text-danger">※ 업로드 후 표시된 파일을 선택해야 내용에 반영됩니다.</small>
            </div>
            <div class="article-footer text-end mt-5">
                <input type="button" id="test_btn" class="btn btn-dark col-12" value="TEST">
                <input type="button" id="save_btn" class="btn btn-dark col-12" value="새 글 작성">
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function () {
            {{-- todo --}}
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            // init editor
            let quill = new Quill('#editor', {
                // debug: true,
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{'font': []}],
                        [{'header': [1, 2, 3, 4, 5, 6, false]}],
                        [{'align': []}],
                        ['bold', 'underline', 'italic', 'strike'],
                        [{'color': []}, {'background': []}],
                        ['clean']
                    ]
                }
            });

            // image test
            console.log(quill.insertEmbed(0, 'image', '{{ asset('storage/image/etc/dog.png') }}'));

            $("#test_btn").click(function () {
                let delta = quill.getContents();
                /*console.log(delta);
                let dest_src = "http://localhost/p02/storage/image/etc/dog.png";
                quill.setContents(delta.filter(
                    val => !(typeof val.insert === 'object'
                        && val.insert.hasOwnProperty('image')
                        && val.insert.image === dest_src)
                ));*/

                console.log(delta.filter(
                    val => typeof val.insert === 'object' && val.insert.hasOwnProperty('image')
                ));
            });

            // store
            $("#save_btn").click(function () {
                let content = quill.getContents();
                let editor_data = {
                    subject: $("#subject").val(),
                    content: content.ops,
                    uploaded_files: [],
                    embedded_files: [],
                };

                let embedded_src_list = [];
                content.map(val => {
                    if (typeof val.insert === 'object' && val.insert.hasOwnProperty('image')) {
                        embedded_src_list.push(val.insert.image);
                    }
                });

                uploaded_files.map(val => {
                    editor_data.uploaded_files.push(val.file_id);

                    if (embedded_src_list.length > 0) {
                        let embed_idx = embedded_src_list.indexOf(val.src);

                        if (embed_idx !== -1) {
                            editor_data.embedded_files[embed_idx] = val.file_id;
                        }
                    }
                });

                $.ajax({
                    url: '{{ route('store') }}',
                    type: 'post',
                    data: editor_data,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);

                        quill.setContents(data.content);
                    },
                    error: function (xhr) {
                        console.log('ERROR :');
                        console.log(xhr);
                    }
                });
            });

            // file upload
            let uploaded_files = [];
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

                        let img = $("<img class='img-thumbnail bg-light rounded' title='" + data.title + "' src='" + data.src + "' data-id='" + data.file_id + "'>");
                        let btn = $("<input type='button' class='btn-close delete_btn' data-id='" + data.file_id + "'>");
                        let div = $("<div class='uploaded-file'></div>");
                        div.append(img);
                        div.append(btn);
                        $("div.uploaded-files").append(div);

                        uploaded_files.push({
                            file_id: data.file_id,
                            src: data.src
                        });
                    },
                    error: function (xhr) {
                        console.log(xhr);
                    }
                });
            });

            // uploaded file editor insert
            // editor selection catch
            let quill_idx = null;
            quill.on('selection-change', function (range, oldRange, source) {
                quill_idx = range ? range.index : range;
            });

            // insert
            $(document).on("click", "div.uploaded-file img", function (e) {
                if (quill_idx === null) {
                    quill_idx = quill.getLength() - 1;
                }
                quill.insertEmbed(quill_idx, 'image', $(this).attr('src'));
            });

            // uploaded file delete
            $(document).on("click", "div.uploaded-file input.delete_btn", function (e) {
                if (!confirm("파일을 삭제하시겠습니까?")) {
                    return false;
                }

                let delete_btn = $(this);

                // editor embedded file delete
                let dest_src = $("img[data-id=" + delete_btn.data('id') + "]").attr('src');
                quill.setContents(delta.filter(
                    val => !(typeof val.insert === 'object'
                        && val.insert.hasOwnProperty('image')
                        && val.insert.image === dest_src)
                ));

                // uploaded file delete
                $.ajax({
                    url: '{{ route('delete') }}',
                    type: 'delete',
                    data: {
                        file_id: delete_btn.data('id')
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (!data.res) {
                            alert(data.msg);
                            return false;
                        }
                        {{-- todo --}}
                        // delete_btn.parents("div.uploaded-file").remove();
                        delete_btn.parent().remove();
                        $("#upload_file").val("");
                    },
                    error: function (xhr) {
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

        /*.ql-editor {
            height: 30rem;
            overflow-y: scroll;
        }*/
    </style>
@endsection

@section('loadScript')
    <!-- Theme included stylesheets -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <!-- Core build with no theme, formatting, non-essential modules -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.core.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.core.js"></script>

    <!-- Include the Quill library -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
@endsection
