<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}" />

	<title>OurScene</title>

	<!-- Jquery -->
	<script src="{{ asset('js/jquery-2.1.4.min.js') }}"></script>

	<!-- Materialize -->
	<link type="text/css" rel="stylesheet" href="{{ asset('materialize/css/materialize.min.css') }}"  media="screen,projection"/>
	<script type="text/javascript" src="{{ asset('materialize/js/materialize.min.js') }}"></script>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<script>
		var ROOT = "{{ action('HomeController@getIndex') }}";
		var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
	</script>

	@yield('head')

	<!-- Ourscene styles -->
	<link href="{{ asset('css/legal.css') }}" rel="stylesheet">

</head>
<body>
		<nav id="legal-navigation">
	    	<div class="nav-wrapper">

				<a href="{{ action('SearchController@getSearch') }}" class="brand-logo left">
					<img src="{{ asset('images/icons/logo.svg') }}" class="brand-logo-icon"/>
					<b>OurScene</b>
				</a>
				
	    	</div>
		</nav>
	@yield('content')

	@yield('scripts')

</body>
</html>