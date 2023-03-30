<html>
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <script src="{{ asset('js/app.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('loadScript')
    @yield('style')
</head>
<body class="bg-light">
<div class="container bg-light">
    <!-- nav -->
    <div class="row d-none d-md-block d-sm-none">
        <nav class="navbar fixed-top col-12 bg-black">
            <div class="col-2 text-end">
                <a class="navbar-brand link-light fw-bold" href="{{ route('list') }}">H S P</a>
            </div>
            <div class="search_box offset-1 col-6">
                <input type="text" id="search_value" class="form-control input-sm">
            </div>

            <div class="offset-1 col-2 dropdown text-left">
                <a href="#" id="user_btn" class="link-dark" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset('storage/image/etc/dog.png') }}" width="50" height="50" class="rounded-circle">
                </a>
                <ul class="dropdown-menu bg-dark text-small" aria-labelledby="user_btn">
                    <li><a class="dropdown-item bg-dark link-light" href="#">New Article</a></li>
                    <li><a class="dropdown-item bg-dark link-light" href="#">Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item bg-dark link-danger" href="#">Logout</a></li>
                </ul>
            </div>
        </nav>
    </div>

    <!-- nav mobile -->
    <div class="row d-block d-md-none d-lg-none d-xl-none d-xxl-none">
        <nav class="navbar fixed-top col-12 bg-black">
            <div class="col-2 text-end">
                <a class="navbar-brand link-light fw-bold" href="#">H S P</a>
            </div>

            <form class="offset-1 col-6" role="search">
                <input type="search" class="form-control" placeholder="Search" aria-label="Search">
            </form>
            <div class="offset-1 col-2 dropdown text-left">
                <a href="#" id="user_btn" class="link-dark" data-bs-toggle="collapse" data-bs-target="#mobile-menu"
                   aria-expanded="false" aria-controls="#mobile-menu">
                    <img src="{{ asset('storage/image/etc/dog.png') }}" width="50" height="50" class="rounded-circle">
                </a>
            </div>

            <ul id="mobile-menu" class="list-group list-group-flush collapse col-12 bg-black">
                <li class="list-group-item bg-black text-center link-light">New Article</li>
                <li class="list-group-item bg-black text-center link-light">Profile</li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li class="list-group-item bg-black text-center link-danger">Logout</li>
            </ul>
        </nav>
    </div>

    @yield('content')

    <hr style="margin-top: 10rem;">
    <div class="row mt-5 mb-4">
        <div class="col">
            <p class="text-center text-dark">@created by Park Hyeon Soo</p>
        </div>
    </div>
</div>
@yield('script')
</body>
</html>
