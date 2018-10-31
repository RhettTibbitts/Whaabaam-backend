<html lang="en">
	<head>
		<!-- Meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>{{ PROJECT_NAME }}: @yield('title')</title>

		<link rel="shortcut icon" type="image/png" href="{{ asset(SYS_IMG_PATH).'/logo.png' }}"/>
		<!-- <link rel="stylesheet" href="{{asset('public/backEnd/vendor/bootstrap4/css/bootstrap.min.css')}}"> -->
	</head>
	<body>
		<style>
		  ul {
		  list-style-type: square;
		  }
		  ul > li > ul {
		  list-style-type: circle;
		  }
		  ul > li > ul > li > ul {
		  list-style-type: square;
		  }
		  ol li {
		  font-family: Arial ;
		  }
		</style>
		{!! $content !!}
	</body>
</html>