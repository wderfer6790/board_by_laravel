<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>HSP</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('loadScript')
    @yield('style')
</head>
<body class="bg-light">
<div id="body_wrapper" class="container">
    <!-- nav -->
    <div class="row">
        <nav class="navbar fixed-top col-12 bg-black">
            <div class="col-2 text-center">
                <a class="navbar-brand link-light fw-bold" href="{{ route('list') }}">H S P</a>
            </div>
            <div class="search_box offset-1 col-6">
                <input type="text" id="search_value" class="form-control input-sm">
            </div>

            @auth
            <div class="offset-1 col-2 dropdown text-start">
                <a href="#" id="user_btn" class="link-dark" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ $user_thumbnail }}" width="50" height="50"
                         class="rounded-circle border border-secondary border-2">
                </a>
                <ul class="dropdown-menu bg-dark text-small" aria-labelledby="user_btn">
                    <li><a class="dropdown-item bg-dark link-light" href="{{ route('create') }}">New Article</a>
                    </li>
                    <li><a class="dropdown-item bg-dark link-light" href="{{ route('profile') }}">Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item bg-dark link-danger" href="{{ route('logout') }}">Logout</a></li>
                </ul>
            </div>
            @endauth

            @guest
                <div class="offset-1 col-2 dropdown text-start">
                    <a href="{{ route('login') }}" class="link-light">LOGIN</a>
                </div>
            @endguest
        </nav>
    </div>

    <div id="content_wrapper">
        @yield('content')
    </div>
</div>

<div id="footer" class="text-center text-dark">
    @created by Park Hyeon Soo
</div>

@yield('script')
</body>
</html>
