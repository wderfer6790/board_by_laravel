@extends('layouts.article')

@section('content')
    <!-- article header -->
    <div class="row bg-light" style="padding-top: 5em;">
        <div class="col">
            <div class="article-header">
                <h1 class="article-subject mb-4">{{ $article->subject }}</h1>
                <span class="author float-start"><img class="author-thumbnail rounded-circle"
                                                      src="{{ $article->user->file->count() > 0 ? asset($article->user->file->get(0)->path) : 'no_thumbnail' }}"> {{ $article->user->name }}</span>
                <span class="write-date float-end">{{ $article->updated_at }}</span>
            </div>
        </div>
    </div>
    <hr class="mt-3 mb-4">
    <!-- article content -->
    <div class="row bg-light mt-5">
        <div class="col">
            <div class="article-content">

            </div>
        </div>
    </div>
    <hr class="mt-5">
    @displayOption($article->user_id)
        <p class="text-end">
            <a id="edit_btn" class="fw-bold">edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a id="delete_btn" class="fw-bold">delete</a>
        </p>
    @enddisplayOption

    <!-- article replay -->
    <div class="row bg-light mt-5">
        <div class="col reply_container">
            @auth
            <div class="m-5">
                <div class="reply_box input-group">
                    <textarea class="reply_input form-control"></textarea>
                    <input type="button" class="reply_btn btn btn-dark" value="reply">
                </div>
            </div>
            @endauth

            @forelse($article->replies as $reply)
                <div class="reply m-5">
                    <div class="writer bg-light text-start">
                        <div class="float-start">
                            <img class="reply-thumbnail rounded-circle"
                                 src="{{ $reply->user->file->count() > 0 ? asset($reply->user->file->get(0)->path) : asset('storage/image/no_image.png') }}"> {{ $reply->user->name }}
                            <small>{{ date("H:i y/m/d", strtotime($reply->updated_at)) }}</small>
                        </div>

                        <div class="float-end">
                            @auth
                            <a class="nested_reply_btn" data-id="{{ $reply->id }}">reply</a>
                            @endauth
                            @displayOption($reply->user_id)
                                &nbsp;&nbsp;<a class="reply_edit_btn" data-id="{{ $reply->id }}">edit</a>
                                &nbsp;&nbsp;<a class="reply_delete_btn" data-id="{{ $reply->id }}">delete</a>
                            @enddisplayOption
                        </div>

                        <p class="reply-content m-3">
                            {!! $reply->file->count() > 0 ? "<img src='" . asset($reply->file->get(0)->path) . "' class='reply_img'><br>" : "" !!}
                            {{ $reply->content }}
                        </p>
                    </div>
                </div>
                @if($reply->child && $reply->child->count() > 0)
                    @foreach($reply->child as $child)
                        <hr class="m-5">
                        <div class="reply-child m-5">
                            <div class="writer bg-light text-start">
                                <div class="float-start">
                                    <img class="reply-thumbnail rounded-circle"
                                         src="{{ $child->user->file->count() > 0 ? asset($child->user->file->get(0)->path) : asset('storage/image/no_image.png') }}"> {{ $child->user->name }}
                                    <small>{{ date("H:i y/m/d", strtotime($child->updated_at)) }}</small>
                                </div>

                                <div class="float-end">
                                    @auth
                                    <a class="nested_reply_btn" data-id="{{$child->id}}">reply</a>
                                    @endauth

                                    @displayOption($child->user_id)
                                        &nbsp;&nbsp;<a class="reply_edit_btn" data-id="{{$child->id}}">edit</a>
                                        &nbsp;&nbsp;<a class="reply_delete_btn" data-id="{{$child->id}}">delete</a>
                                    @enddisplayOption
                                </div>
                                <p class="reply-content m-3">
                                    {!! $child->file->count() > 0 ? "<img src='" . asset($child->file->get(0)->path) . "' class='reply_img'><br>" : "" !!}
                                    {{ $child->content }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if(!$loop->last)
                    <hr class="m-5">
                @endif

            @empty

            @endforelse
        </div>
    </div>
    <div class="quill_container d-none"></div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
                }
            });

            // content render
            let quill = new Quill(".quill_container");
            quill.setContents({!! $content !!});
            $("div.article-content").html(quill.root.innerHTML);

            // edit
            $(document).on('click', "#edit_btn", function (e) {
                e.preventDefault();
                location.href = '{{ route('edit', $article->id) }}' + location.search;
            });

            // delete
            $(document).on('click', "#delete_btn", function (e) {
                e.preventDefault();
                if (confirm('글을 삭제하시겠습니까?')) {
                    $.ajax({
                        url: '{{ route('destroy', ['id' => $article->id]) }}',
                        method: 'delete',
                        success: function (data) {
                            alert(data.msg);
                            if (data.res) {
                                location.href = '{{ route('list') }}' + location.search;
                            }
                        },
                        error: function (xhr) {
                            console.log(xhr);
                        }
                    });
                }
            });

            // reply
            $(document).on('click', '.reply_btn', function () {
                let reply = $(this).parent().find('.reply_input');
                if (reply.val().trim().length === 0) {
                    alert('댓글을 입력해주세요.');
                    $(this).focus();
                    return false;
                }

                if (!confirm('댓글을 작성하시겠습니까?')) {
                    $(this).focus();
                    return false;
                }

                $.ajax({
                    url: '{{ route('replyStore', $article->id) }}',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        content: reply.val().trim()
                    },
                    success: function (data) {
                        if (!data.res) {
                            alert('msg');
                            return false;
                        }

                        let div = $("<div class='reply m-5'></div>");
                        let div2 = $("<div class='writer bg-light text-start'></div>");

                        let div_ahtor = $("<div class='float-start'></div>");
                        let author_thumbnail = $("<img class='reply-thumbnail rounded-circle' src='" + data.author_thumbnail + "'>");
                        let publish_date = $("<small>" + data.publish_date + "</small>");
                        div_ahtor.append(author_thumbnail, "&nbsp;", data.author, "&nbsp;", publish_date);

                        let div_option = $("<div class='float-end'></div>");
                        let nested_reply_btn = $("<a class='nested_reply_btn' data-id='" + data.id + "'>reply</a>");
                        let reply_edit_btn = $("<a class='reply_edit_btn' data-id='" + data.id + "'>edit</a>");
                        let reply_delete_btn = $("<a class='reply_delete_btn' data-id='" + data.id + "'>delete</a>");
                        div_option.append(nested_reply_btn, "&nbsp;&nbsp;", reply_edit_btn, "&nbsp;&nbsp;", reply_delete_btn);

                        let reply_content = $("<p class='reply-content m-3'>" + data.content + "</p>");
                        let reply_image = data.image ? $("<img src='" + data.image + "' class='reply_img'>") : "";
                        reply_content.prepend(reply_image);

                        div2.append(div_ahtor, div_option, reply_content);
                        div.append(div2);

                        let hr = $("div.reply").length > 0 ? "<hr class='m-5'>" : '';

                        $("div.reply_container").append(hr, div);
                        div.focus();
                        reply.val("");
                    },
                    error: function (xhr) {
                        console.log(xhr);
                    }
                });
            });

            // nested reply
            $(document).on('click', '.nested_reply_btn', function () {
                let parent_reply_div = $(this).parent();
                let reply_input = $(this).parent().find('.nested_reply_input');

                if (reply_input.val().trim().length === 0) {
                    alert('댓글을 입력해주세요.');
                    $(this).focus();
                    return false;
                }

                if (!confirm('댓글을 작성하시겠습니까?')) {
                    $(this).focus();
                    return false;
                }

                let parent_id = $(this).data('id');
                $.ajax({
                    url: '{{ route('replyStore', $article->id) }}' + `/${parent_id}`,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        content: reply_input.val().trim()
                    },
                    success: function (data) {
                        if (!data.res) {
                            alert('msg');
                            return false;
                        }

                        let div = $("<div class='reply-child m-5'></div>");
                        let div2 = $("<div class='writer bg-light text-start'></div>");

                        let div_ahtor = $("<div class='float-start'></div>");
                        let author_thumbnail = $("<img class='reply-thumbnail rounded-circle' src='" + data.author_thumbnail + "'>");
                        let publish_date = $("<small>" + data.publish_date + "</small>");
                        div_ahtor.append(author_thumbnail, "&nbsp;", data.author, "&nbsp;", publish_date);

                        let div_option = $("<div class='float-end'></div>");
                        let nested_reply_btn = $("<a class='nested_reply_btn' data-id='" + parent_id + "'>reply</a>");
                        let reply_edit_btn = $("<a class='reply_edit_btn' data-id='" + data.id + "'>edit</a>");
                        let reply_delete_btn = $("<a class='reply_delete_btn' data-id='" + data.id + "'>delete</a>");
                        div_option.append(nested_reply_btn, "&nbsp;&nbsp;", reply_edit_btn, "&nbsp;&nbsp;", reply_delete_btn);

                        let reply_content = $("<p class='reply-content m-3'>" + data.content + "</p>");
                        let reply_image = data.image ? $("<img src='" + data.image + "' class='reply_img'>") : "";
                        reply_content.prepend(reply_image);

                        div2.append(div_ahtor, div_option, reply_content);
                        div.append(div2);

                        let hr = $("div.reply").length > 0 ? "<hr class='m-5'>" : '';

                        parent_reply_div.next(hr, div);
                        div.focus();
                        reply_input.val("");
                        {{-- todo reply input hide --}}
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
        img.author-thumbnail {
            width: 2rem;
            height: 2rem;
        }

        img.reply-thumbnail {
            width: 2rem;
            height: 2rem;
        }

        div.reply-child {
            padding-left: 5rem;
        }

        #edit_btn, #delete_btn, a.nested_reply_btn, a.reply_edit_btn, a.reply_delete_btn {
            cursor: pointer;
            color: #212529;
            text-decoration: none;
        }

        #edit_btn:hover, #delete_btn:hover, a.nested_reply_btn:hover, a.reply_edit_btn:hover, a.reply_delete_btn:hover {
            color: #bdbdbd;
        }

        a.nested_reply_btn, a.reply_edit_btn, a.reply_delete_btn {
            font-size: 0.8rem;
        }

        small {
            font-size: 0.7rem;
        }

        p.reply-content {
            clear: both;
        }
    </style>
@endsection
