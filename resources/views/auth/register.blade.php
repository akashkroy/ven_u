<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>OurScene | Login</title>

	<!-- Bootstrap -->
	<link href="{{ asset('bootstrap-3.3.5/css/bootstrap.min.css') }}" rel="stylesheet">

</head>

<body>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Register</div>
				<div class="panel-body">
					<div class="form-group">
						<div class="col-md-6 col-md-offset-4">
							<a class="btn btn-primary" href="{{ url('/artist-register') }}">Register as Artist</a>
							<a class="btn btn-primary" href="{{ url('/venue-register') }}">Register as Venue</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

</body>
</html>