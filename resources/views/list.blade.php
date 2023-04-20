@extends('layouts.main')

@section('content')
    <div class="row card-list"></div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            let init = true;
            let total = 0;
            let sort = '-updated_at';
            let search = '';
            let search_first = false;
            if (location.search.length > 0) {
                let arr = location.search.replace('?', '').split('&');
                arr.map(query => {
                    let [key, val] = query.split('=');
                    switch (key) {
                        case 'total':
                            total = val;
                            break;
                        case 'sort':
                            sort = val;
                            break;
                        case 'search':
                            search = val;
                            break;
                    }
                });
            }

            // article link
            $(document).on('click', "h5.card-title", function () {
                const id = $(this).data('id');

                let viewsUrl = '{{ route('increaseCount', ['id' => ':id', 'type' => 'views']) }}';
                viewsUrl = viewsUrl.replace(':id', id);
                $.post(viewsUrl);

                let articleUrl = "{{ route('article', ":id") }}";
                articleUrl = articleUrl.replace(':id', id);
                location.href = articleUrl + "?total=" + total + "&sort=" + sort + "&search=" + search;
            });

            // infinite scroll
            $(document).on('scroll', function() {
                if (window.innerHeight + window.scrollY >= document.body.offsetHeight) {
                    getArticles();
                }
            });

            // search
            $("#search_value").keydown(function(e) {
                if (e.keyCode === 13) {
                    search = $(this).val();
                    total = 0
                    search_first = true;
                    getArticles();
                }
            });

            getArticles();

            function getArticles() {
                if (search_first) $("div.card-list").empty();

                $.ajax({
                    url: '{{ route('getArticles') }}',
                    method: 'get',
                    async: false,
                    data: {
                        init: init,
                        total: total,
                        sort: sort,
                        search: search,
                        search_first: search_first,
                    },
                    dataType: 'json',
                    success: function (data) {
                        init = false;
                        total = data.total;
                        sort = data.sort;
                        search = data.search ?? '';
                        search_first = false;

                        history.replaceState(null, null, "{{ url('') }}/?total=" + total + "&sort=" + sort + "&search=" + search);

                        if (data.articles.length === 0) {

                        } else {
                            const container = $("div.card-list");

                            $.each(data.articles, function (k, article) {
                                let card = $("<div class='card col-md-3'>");
                                let cardImage = $("<img src='" + article.thumbnail + "' class='card-img-top article-thumbnail'>");
                                let cardBody = $("<div class='card-body'>");
                                let cardTitle = $("<h5 class='card-title' data-id='" + article.id + "'>" + article.subject + "</h5>");
                                let author = $("<p class='card-author text-end'><img class='author-profile rounded-circle' src='" + article.profile + "'> " + article.author + "</p>");
                                let other = $("<p class='card-other'><span class='views'>조회수 " + article.views + "회</span><span class='date'>" + article.updated_at + "</span></p>");

                                // cardBody.append(cardTitle, cardText, author, other);
                                cardBody.append(cardTitle, author, other);

                                card.append(cardImage, cardBody);

                                container.append(card);
                            });
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr);
                    }
                });
            }

        });
    </script>
@endsection

@section('style')
    <style>
        div.card {
            margin: 1rem 3rem;
            padding: 0;
        }

        img.article-thumbnail {
            opacity: 0.5;
            height: 18rem;
        }

        h5.card-title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            word-wrap: break-word;
            margin: 0;
            cursor: pointer;
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
        img.author-profile {
            height: 1.8rem;
            width: 1.8rem;
        }
    </style>
@endsection
