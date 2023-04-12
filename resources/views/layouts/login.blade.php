<html>
<head>
    <meta charset="UTF-8">
    <title>HSP</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <script src="{{ asset('js/app.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('loadScript')
    @yield('style')
</head>
<body>
<div class="container bg-light">
    <!-- nav -->
    <div class="row d-none d-md-block d-sm-none">
        <nav class="navbar fixed-top col-12 bg-black">
            <div class="col-2 text-end">
                <a class="navbar-brand link-light fw-bold" href="{{ route('list') }}">H S P</a>
            </div>
        </nav>
    </div>

    <!-- nav mobile -->
    <div class="row d-block d-md-none d-lg-none d-xl-none d-xxl-none">
        <nav class="navbar fixed-top col-12 bg-black">
            <div class="col-2 text-end">
                <a class="navbar-brand link-light fw-bold" href="{{ route('list') }}">H S P</a>
            </div>
        </nav>
    </div>

    @yield('content')

    <hr style="margin-top: 20rem;">
    <div class="row mt-5 mb-4">
        <div class="col">
            <p class="text-center text-dark">@created by Park Hyeon Soo</p>
        </div>
    </div>
</div>
@yield('script')
</body>
</html>
