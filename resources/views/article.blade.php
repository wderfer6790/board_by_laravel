@extends('layouts.article')

@section('content')
    <!-- article header -->
    <div class="row bg-light"  style="padding-top: 5em;">
        <div class="col">
            <div class="article-header">
                <h1 class="article-subject mb-4">{{ $article->subject }}</h1>
                <span class="author float-start"><img class="author-thumbnail rounded-circle" src="{{ asset('storage/image/etc/knight.png') }}"> {{ $article->user->name }}</span>
                <span class="write-date float-end">{{ $article->updated_at }}</span>
            </div>
        </div>
    </div>
    <hr class="mt-3 mb-4">
    <!-- article content -->
    <div class="row bg-light mt-5">
        <div class="col">
            <div class="article">
               {!! $article->content !!}
            </div>
        </div>
    </div>

    <hr class="mt-5 mb-5">

    <!-- article replay -->
    <div class="row bg-light">
        <div class="col">
            <div class="input-group">
                <textarea id="reply_input" class="form-control"></textarea>
                <input type="button" id="reply_btn" class="btn btn-dark" value="reply">
            </div>

            <div class="reply m-5">
                <div class="writer bg-light text-start">
                    <img class="author-thumbnail rounded-circle" src="{{ asset('storage/image/etc/mountain.png') }}"> 설명서읽는사람 <small>2022-06-22 07:29:30</small>
                    <p class="reply-content m-3">정말 유익한 내용 감사합니다!</p>
                </div>
            </div>

            <hr class="m-5">
            <div class="reply m-5">
                <div class="writer bg-light text-start">
                    <img class="author-thumbnail rounded-circle" src="{{ asset('storage/image/etc/mountain.png') }}"> 설명서읽는사람 <small>2022-06-22 07:29:30</small>
                    <p class="reply-content m-3">정말 유익한 내용 감사합니다!</p>
                </div>
            </div>

            <hr class="m-5">
            <div class="reply m-5">
                <div class="writer bg-light text-start">
                    <img class="author-thumbnail rounded-circle" src="{{ asset('storage/image/etc/mountain.png') }}"> 설명서읽는사람 <small>2022-06-22 07:29:30</small>
                    <p class="reply-content m-3">정말 유익한 내용 감사합니다!</p>
                </div>
            </div>
            <hr class="m-5">
            <div class="reply m-5">
                <div class="writer bg-light text-start">
                    <img class="author-thumbnail rounded-circle" src="{{ asset('storage/image/etc/mountain.png') }}"> 설명서읽는사람 <small>2022-06-22 07:29:30</small>
                    <p class="reply-content m-3">정말 유익한 내용 감사합니다!</p>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('footerScript')
<script>
$(document).ready(function() {

});
</script>
@endsection
@section('style')
    <style>
        img.author-thumbnail {
            width: 2em;
            height: 2em;
        }
    </style>
@endsection
