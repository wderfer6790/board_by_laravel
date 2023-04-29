@extends('layouts.article')

@section('content')
    <!-- article header -->
    <div class="row bg-light">
        <div class="col">
            <div class="article-header">
                <h1 class="article-subject mb-4">{{ $article->subject }}</h1>
                <span class="author float-start">
                    <img class="author-thumbnail rounded-circle" src="{{ $article->user->file->count() > 0 ? asset($article->user->file->get(0)->path) : asset('storage/image/no_image.png') }}"> {{ $article->user->name }}</span>
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
            <div class="reply_input_box row m-3">
                <div class="col-md-1 text-start">
                    <img src="{{ $user_thumbnail }}" class="rounded-circle author-thumbnail">
                </div>
                <div class="col-md-9">
                    <textarea class="reply_input" rows="1"></textarea>
                </div>
                <div class="col-md-2 text-end">
                    <a href="javascript:void(0);" class="reply_btn">reply</a>
                    &nbsp;
                    <a href="javascript:void(0);" class="reply_cancel_btn">cancel</a>
                </div>
            </div>
            <div class="reply_update_box row mt-3 d-none">
                <div class="col-md-10">
                    <textarea class="reply_input" rows="1"></textarea>
                </div>
                <div class="col-md-2 text-end">
                    <a href="javascript:void(0);" class="reply_update_btn">update</a>
                    &nbsp;
                    <a href="javascript:void(0);" class="reply_cancel_btn">cancel</a>
                </div>
            </div>
            <hr class="m-5">
            @endauth

            @forelse($article->replies as $reply)
                <div class="reply reply_box m-5">
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
                                &nbsp;<a class="reply_edit_btn" data-id="{{ $reply->id }}">edit</a>
                                &nbsp;<a class="reply_delete_btn" data-id="{{ $reply->id }}">delete</a>
                            @enddisplayOption
                        </div>

                        <p class="reply-content m-3">
                            {!! $reply->file->count() > 0 ? "<img src='" . asset($reply->file->get(0)->path) . "' class='reply_img'><br>" : "" !!}
                            {!! nl2br($reply->content) !!}
                        </p>
                    </div>
                </div>
                @if($reply->child && $reply->child->count() > 0)
                    @foreach($reply->child as $child)
                        <hr class="m-5">
                        <div class="reply-child reply_box m-5">
                            <div class="writer bg-light text-start">
                                <div class="float-start">
                                    <img class="reply-thumbnail rounded-circle"
                                         src="{{ $child->user->file->count() > 0 ? asset($child->user->file->get(0)->path) : asset('storage/image/no_image.png') }}"> {{ $child->user->name }}
                                    <small>{{ date("H:i y/m/d", strtotime($child->updated_at)) }}</small>
                                </div>

                                <div class="float-end">
                                    @auth
                                    <a class="nested_reply_btn" data-id="{{$reply->id}}">reply</a>
                                    @endauth
                                    @displayOption($child->user_id)
                                        &nbsp;<a class="reply_edit_btn" data-id="{{$child->id}}">edit</a>
                                        &nbsp;<a class="reply_delete_btn" data-id="{{$child->id}}">delete</a>
                                    @enddisplayOption
                                </div>
                                <p class="reply-content m-3">
                                    {!! $child->file->count() > 0 ? "<img src='" . asset($child->file->get(0)->path) . "' class='reply_img'><br>" : "" !!}
                                    {!! nl2br($child->content) !!}
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

            // article edit
            $(document).on('click', "#edit_btn", function (e) {
                e.preventDefault();
                location.href = '{{ route('edit', $article->id) }}' + location.search;
            });

            // article delete
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

            // reply input auto height
            $(document).on('keyup', 'textarea.reply_input', function() {
                $(this).css('height', 'auto');
                $(this).css('height', $(this).prop('scrollHeight') + 'px');
            });

            // nested reply input display
            $(document).on('click', 'a.nested_reply_btn', function() {
                let reply_input_box = $("div.reply_input_box").filter(':first').clone();

                reply_input_box.find('textarea.reply_input').val('');
                reply_input_box.find('a.reply_btn').data('id', $(this).data('id'));
                reply_input_box.find('a.reply_cancel_btn').data('id', $(this).data('id'));

                $(this).parents('div.reply_box').append(reply_input_box);
            });

            // reply input cancel
            $(document).on('click', 'a.reply_cancel_btn', function() {
                if (typeof $(this).data('id') !== 'undefined') {
                    $(this).parent().parent().remove();
                } else {
                    $(this).parent().parent().find('textarea.reply_input')
                        .val('')
                        .css('height', 'auto');
                }
            });

            // reply store
            $(document).on('click', '.reply_btn', function () {
                let reply = $(this).parents('div.reply_input_box').find('.reply_input');
                if (reply.val().trim().length === 0) {
                    alert('댓글을 입력해주세요.');
                    reply.focus();
                    return false;
                }

                if (!confirm('댓글을 작성하시겠습니까?')) {
                    reply.focus();
                    return false;
                }

                // nested reply parent id add in url
                let parent_id = $(this).data('id');
                let child = false;
                let url = '{{ route('replyStore', $article->id) }}';
                if (typeof parent_id !== 'undefined') {
                    child = true
                    url = url + `/${parent_id}`;
                }

                $.ajax({
                    url: url,
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
                        // nested reply, reply-child class
                        let div = $(`<div class='${child ? "reply-child" : "reply"} reply_box m-5'></div>`);
                        let div2 = $("<div class='writer bg-light text-start'></div>");

                        let div_ahtor = $("<div class='float-start'></div>");
                        let author_thumbnail = $("<img class='reply-thumbnail rounded-circle' src='" + data.author_thumbnail + "'>");
                        let publish_date = $("<small>" + data.publish_date + "</small>");
                        div_ahtor.append(author_thumbnail, "&nbsp;", data.author, "&nbsp;", publish_date);

                        let div_option = $("<div class='float-end'></div>");
                        let nested_reply_btn = $("<a class='nested_reply_btn' data-id='" + data.parent_id + "'>reply</a>");
                        let reply_edit_btn = $("<a class='reply_edit_btn' data-id='" + data.id + "'>edit</a>");
                        let reply_delete_btn = $("<a class='reply_delete_btn' data-id='" + data.id + "'>delete</a>");
                        div_option.append(nested_reply_btn, "&nbsp;&nbsp;", reply_edit_btn, "&nbsp;&nbsp;", reply_delete_btn);

                        let reply_content = $("<p class='reply-content m-3'>" + data.content + "</p>");
                        let reply_image = data.image ? $("<img src='" + data.image + "' class='reply_img'>") : "";
                        reply_content.prepend(reply_image);

                        div2.append(div_ahtor, div_option, reply_content);
                        div.append(div2);

                        // nested reply, parent reply append
                        let hr = $("div.reply").length > 0 ? "<hr class='m-5'>" : '';
                        if (child) {
                            $("a.nested_reply_btn[data-id=" + parent_id + "]").filter(':last').parents('div.reply_box').after(hr, div);
                            reply.parents('div.reply_input_box').remove();
                        } else {
                            $("div.reply_container").append(hr, div);
                            reply.val('');
                        }

                        div.attr('tabindex', -1).focus();
                    },
                    error: function (xhr) {
                        console.log(xhr);
                    }
                });
            });

            // reply edit display
            $(document).on('click', 'a.reply_edit_btn', function() {
                let reply_box = $(this).parents('div.reply_box');
                let reply_update_box = $("div.reply_update_box").filter(':first').clone();
                let reply_input = reply_update_box.find('textarea.reply_input');

                reply_input.val(reply_box.find('p.reply-content').text().trim());

                reply_update_box.find('a.reply_update_btn').data('id', $(this).data('id'));
                reply_update_box.find('a.reply_cancel_btn').data('id', $(this).data('id'));
                reply_update_box.removeClass('d-none');

                reply_box.append(reply_update_box);

                reply_input.trigger('keyup').focus();
            });

            // reply edit
            $(document).on('click', 'a.reply_update_btn', function() {
                let btn = $(this);
                let reply = btn.parents('div.reply_update_box').find('.reply_input');
                if (reply.val().trim().length === 0) {
                    alert('댓글을 입력해주세요.');
                    reply.focus();
                    return false;
                }

                if (!confirm('댓글을 수정하시겠습니까?')) {
                    reply.focus();
                    return false;
                }

                let url = '{{ route('replyUpdate', ':id') }}'.replace(':id', btn.data('id'));
                $.ajax({
                    url: url,
                    type: 'put',
                    dataType: 'json',
                    data: {
                        content: reply.val().trim()
                    },
                    success: function (data) {
                        if (!data.res) {
                            alert('msg');
                            return false;
                        }
                        btn.parents('div.reply_box').find('p.reply-content').text(data.content);
                        reply.parents('div.reply_update_box').remove();
                    },
                    error: function (xhr) {
                        console.log(xhr);
                    }
                });

            });

            // reply delete
            $(document).on('click', 'a.reply_delete_btn', function() {
                if (!confirm('댓글을 삭제하시겠습니까?')) {
                    return false;
                }

                let del_btn = $(this);
                let parent_id = del_btn.parents('div.reply_box').find('a.nested_reply_btn').data('id');
                let parent = del_btn.data('id') === parent_id;
                let url = '{{ route('replyDestroy', ':id') }}'.replace(':id', del_btn.data('id'));
                $.ajax({
                    url: url,
                    type: 'delete',
                    dataType: 'json',
                    success: function (data) {
                        if (!data.res) {
                            alert(data.msg);
                            return false;
                        }

                        if (parent) {
                            let replies = $(`a.nested_reply_btn[data-id=${parent_id}]`).parents('div.reply_box');
                            replies.prev().remove();
                            replies.remove();
                        } else {
                            let reply = del_btn.parents('div.reply_box');
                            reply.prev().remove(); // remove hr tag
                            reply.remove();
                        }

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

        textarea.reply_input {
            width: 100%;
            background: transparent;
            border: none;
            outline: none;
            border-bottom: solid 2px #bdbdbd;
            overflow-y: hidden;
            resize: none;
        }

        a.reply_btn, a.reply_update_btn, a.reply_cancel_btn {
            text-decoration: none;
            color: #bdbdbd;
            cursor: pointer;
        }
        a.reply_btn:hover, a.reply_update_btn, a.reply_cancel_btn:hover {
            color: #212529;
        }
    </style>
@endsection
