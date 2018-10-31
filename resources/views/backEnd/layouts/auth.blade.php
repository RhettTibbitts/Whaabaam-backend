<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>{{ PROJECT_NAME }} @yield('title')</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset(SYS_IMG_PATH).'/logo.png' }}"/>
    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{asset('public/backEnd/vendor/bootstrap4/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/backEnd/vendor/themify-icons/themify-icons.css')}}">
    <link rel="stylesheet" href="{{asset('public/backEnd/vendor/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/backEnd/assets/css/core.css')}}">
    <link rel="stylesheet" href="{{asset('public/backEnd/assets/css/developer.css')}}">
</head>
<body>

    <?php $background = asset('public/backEnd/assets/img/photos-1/2.jpg'); ?>
    <body class="img-cover" style="background-image: url({{$background}});">
        <div class="container-fluid">
            @yield('content')
        </div>
        <!-- Vendor JS -->
        <script type="text/javascript" src="{{asset('public/backEnd/vendor/jquery/jquery-1.12.3.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('public/backEnd/vendor/tether/js/tether.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('public/backEnd/vendor/bootstrap4/js/bootstrap.min.js')}}"></script>
    </body>
</html>
