<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Jquery -->
	<script src="{{ asset('js/jquery-2.1.4.min.js') }}"></script>

	<!-- Materialize -->
	<link type="text/css" rel="stylesheet" href="{{ asset('materialize/css/materialize.min.css') }}"  media="screen,projection"/>
	<script type="text/javascript" src="{{ asset('materialize/js/materialize.min.js') }}"></script>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	@yield('head')

	<!-- Ourscene styles -->
	<link href="{{ asset('css/http-code.css') }}" rel="stylesheet">
</head>
<body>
	<div class="wrapper">
		<div class="wrapper-content">
			<div id="http-status">
				<img id="ourscene-logo" src="{{ asset('images/icons/logo.svg') }}">
				<div id="code">ERROR @yield('code')</div>
				<div id="message">@yield('message')</div>
			</div>
		</div>
	</div>
</body>
</html>