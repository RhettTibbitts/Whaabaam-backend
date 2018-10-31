<!doctype html>
<html>
    <head> 
        <!-- <noscript> <h1>Please Enable JavaScript</h1></noscript> -->
        <noscript><meta http-equiv="refresh" content="0; url=whatyouwant.html" /></noscript>
        
        <meta charset="utf-8">
        <title>{{ PROJECT_NAME }} @yield('title')</title>
        <link rel="shortcut icon" href="{{ asset('public/images/system/logo.png') }}">
        <link href="{{ asset('public/css/frontEnd/reset.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('public/css/frontEnd/style.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('public/css/frontEnd/font-awesome.css') }}" rel="stylesheet" type="text/css">

        <!-- <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css"> -->

        <link href="{{ asset('public/css/frontEnd/common.css') }}" rel="stylesheet" type="text/css">

        <script type="text/javascript" src="{{ asset('public/js/frontEnd/html5.js') }}"></script>
        <script type="text/javascript" src="{{ asset('public/js/frontEnd/jquery-1.11.0.js') }}"></script>
        <script type="text/javascript" src="{{ asset('public/js/frontEnd/jquery.validate.js') }}"></script>

        <!-- <script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="{{ asset('public/js/frontEnd/jquery.toaster.js') }}"></script> -->

    </head>
    <body>
        <div class="loader "></div>
        <section class="container" >
            @include('frontEnd.common.header')
            @include('frontEnd.common.messages')

            <?php /* <div id="cp-store-root" data-cp-settings='{ "access_key": "d9fd36a82142d72a5b53b3dafcf9b983" }'></div>
            <script>
                (function ( d, s, id ) {
                    var js, cpJs = d.getElementsByTagName( s )[0], t = new Date();
                    if ( d.getElementById( id ) ) return;
                    js = d.createElement( s );
                    js.id = id;
                    js.setAttribute( 'data-cp-url', 'https://store.canvaspop.com' );
                    js.src = 'https://store.canvaspop.com/static/js/cpopstore.js?bust=' + t.getTime();
                    cpJs.parentNode.insertBefore( js, cpJs );
                }( document, 'script', 'canvaspop-jssdk' ));
            </script> */ ?>

            @yield('content')
            
            <section id="footer">
                @yield('homepage_footer')

                @include('frontEnd.common.footer')            
            </section><!--#footer-->

            @yield('script')
        </section>
    </body>
</html>