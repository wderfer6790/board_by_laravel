@extends('layouts.main')

@section('content')
<div class="row card-list"></div>
@endsection

@section('footerScript')
    <script>
        $(document).ready(function () {
            let total = 0;
            let sort = '-updated_at';
            let filter = '';

            $.ajax({
                url: '{{ route('getArticles') }}',
                method: 'get',
                data: {
                    total: total,
                    sort: sort,
                    filter: filter,
                },
                success: function (data) {
                    total = data.total;

                    if (data.articles.length === 0) {

                    } else {
                        const container = $("div.card-list");

                        $.each(data.articles, function (k, article) {
                            let card = $("<div class='card col-md-3'>");
                            let cardImage = $("<img src='" + article.thumbnail + "' class='card-img-top'>");
                            let cardBody = $("<div class='card-body'>");
                            let cardTitle = $("<h5 class='card-title' data-id='" + article.id + "'>" + article.subject + "</h5>");
                            let cardText = $("<p class='card-text'>" + article.content + "</p>");
                            let author = $("<p class='card-author text-end'>" + article.author + "</p>");
                            let other = $("<p class='card-other'><span class='views'>조회수 " + article.views + "회</span><span class='date'>" + article.updated_at + "</span></p>");

                            cardBody.append(cardTitle, author, cardText, other);
                            card.append(cardImage, cardBody);

                            container.append(card);
                        });
                    }
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            });

            $(document).on('click', "h5.card-title", function() {
                let articleUrl = "{{ route('article', ":id") }}";
                articleUrl = articleUrl.replace(':id', $(this).data('id'));
                location.href = articleUrl + "?total=" + total + "&sort=" + sort + "&filter=" + filter;
            });
        });
    </script>
@endsection

@section('style')
    <style>
        div.card {
            margin: 1rem 3rem;
            padding: 0;
        }
        img.card-img-top {
            opacity: 0.5;
        }
        h5.card-title, p.card-text {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            word-wrap: break-word;
            margin: 0;
        }

        p.card-text {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        p.card-author {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        p.card-other {
            font-size: 0.9rem;
        }

        p.card-other span.views {
            float: left;
        }

        p.card-other span.date {
            float: right;
        }
    </style>
@endsection
