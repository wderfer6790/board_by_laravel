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
    {{-- todo --}}
    @if(1)
    <p class="text-end">
        <a id="edit_btn" class="fw-bold" href="{{ route('edit', ['id' => $article->id]) }}">edit</a>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <a id="delete_btn" class="fw-bold">delete</a>
    </p>
    @endif

    <!-- article replay -->
    <div class="row bg-light mt-5">
        <div class="col">
            <div class="m-5">
                <div class="input-group">
                    <textarea id="reply_input" class="form-control"></textarea>
                    <input type="button" id="reply_btn" class="btn btn-dark" value="reply">
                </div>
            </div>

            @forelse($article->replies as $reply)
                <div class="reply m-5">
                    <div class="writer bg-light text-start">
                        <img class="reply-thumbnail rounded-circle"
                             src="{{ $reply->user->file->count() > 0 ? asset($reply->user->file->get(0)->path) : 'no_thumbnail' }}">
                        {{ $reply->user->name }} <small>{{ $reply->updated_at }}</small>
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
                                <img class="reply-thumbnail rounded-circle"
                                     src="{{ $child->user->file->count() > 0 ? asset($child->user->file->get(0)->path) : 'no_thumbnail' }}">
                                {{ $child->user->name }} <small>{{ $child->updated_at }}</small>
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
    // content render
    let quill = new Quill(".quill_container");
    quill.setContents({!! $content !!});
    $("div.article-content").html(quill.root.innerHTML);

    $("#delete_btn").click(function(e) {
        e.preventDefault();
        if (confirm('글을 삭제하시겠습니까?')) {
            $.ajax({
                url: '{{ route('destroy', ['id' => $article->id]) }}',
                method: 'delete',
                success: function(data) {
                    alert(data.msg);
                    if (data.res) {
                        location.href = '{{ route('list') }}' + location.search;
                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });
        }

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

        #edit_btn, #delete_btn {
            cursor: pointer;
            color: #212529;
            text-decoration: none;
        }
        #edit_btn:hover, #delete_btn:hover {
            color: #bdbdbd;
        }
    </style>
@endsection
