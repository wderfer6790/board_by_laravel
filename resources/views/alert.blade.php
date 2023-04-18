<html>
<head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <title>HSP</title>
    <style>
        body {
            overflow: hidden;
        }
        .modal {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal_body {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 50%;
            height: 20%;
            padding: 2rem;
            text-align: center;
            background-color: #F6F6F6;
            border-radius: 10px;
            box-shadow: 0 2px 3px 0 rgba(34, 36, 38, 0.15);

            transform: translateX(-50%) translateY(-50%);
        }
        .msg {
            font-size: 1.3rem;
            margin-bottom: 2rem;
        }
        .confirm_btn {
            border: none;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            background-color: #bdbdbd;
            color: #212529;
            font-weight: bold;
            cursor: pointer;
        }

        .confirm_btn:active {
            background-color: #212529;
            color: #bdbdbd;
        }
    </style>
</head>
<body>
<div class="modal">
    <div class="modal_body">
        <p class="msg">{{ $msg }}</p>
        <input type="button" class="confirm_btn" onclick="location.href='{{ route($to) }}'" value="확 인">
    </div>
</div>
</body>
</html>
