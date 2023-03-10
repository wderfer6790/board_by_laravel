<html>
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <script src="{{ asset('js/app.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"
    @yield('loadScript')
    @yield('headStyle')
</head>
<body>
<div class="container">
    <h1 class="alert alert-primary mt-3">LOGIN</h1>
    @yield('content')
</div>
@yield('footerScript')
</body>
</html>
