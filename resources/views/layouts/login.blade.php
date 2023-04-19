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
        <nav class="navbar fixed-top col-md-12 bg-black">
            <div class="col-2 text-center">
                <a class="navbar-brand link-light fw-bold" href="{{ route('list') }}">H S P</a>
            </div>
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
