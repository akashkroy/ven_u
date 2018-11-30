<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">

	<meta name="csrf-token" content="{{ csrf_token() }}" />

	<title>OurScene</title>

	<!-- Jquery -->
	<script src="{{ asset('js/jquery-2.1.4.min.js') }}"></script>
	<script src="{{ asset('js/timepicki.js') }}"></script>
	<link type="text/css" rel="stylesheet" href="{{ asset('css/timepicki.css') }}"/>

	<!-- jQuery UI -->
	<script src="{{ asset('js/jquery-ui.js') }}"></script>

	<!-- Materialize -->
	<link type="text/css" rel="stylesheet" href="{{ asset('materialize/css/materialize.min.css') }}"  media="screen,projection"/>
	<script type="text/javascript" src="{{ asset('materialize/js/materialize.min.js') }}"></script>

	<!-- Ourscene login and registration styles -->
	<link href="{{ asset('css/login.css') }}" rel="stylesheet">
	<link href="{{ asset('css/legal.css') }}" rel="stylesheet">
	<link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
	
	<script>
    	var assetBaseUrl = "{{ asset('') }}";
    	var loginArtistUrl = "{{ action('UserController@postLoginArtist') }}";
    	var loginVenueUrl = "{{ action('UserController@postLoginVenue') }}";
    	var registerArtistUrl = "{{ url('/artist-register') }}";
    	var registerVenueUrl = "{{ url('/venue-register') }}";
	</script>

	<script type="text/javascript" src="{{ asset('js/entrypoint.js')}}"></script>

	<script>
		var ROOT = "{{ action('HomeController@getIndex') }}";
		var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
	</script>

	<script>

	var login_type;

	function changeSignInType($this){
		if (login_type == 'artist') {
			$('#main').addClass('body-venue').removeClass('body-artist');
			$('#login-form').attr('action', loginVenueUrl);
			$('#user-type-label').text('Venue')
			$('#user-type-icon').attr('src', assetBaseUrl + 'images/icons/venue.svg');
			$('.change-type-btn').text("SIGN IN AS ARTIST");
			$("#signup-link").attr("href", registerVenueUrl);
			login_type = 'venue';
		}else{
			$('#main').addClass('body-artist').removeClass('body-venue');
			$('#login-form').attr('action', loginArtistUrl);
			$('#user-type-label').text('Artist');
			$('#user-type-icon').attr('src', assetBaseUrl + 'images/icons/artist.svg');
			$('.change-type-btn').text("SIGN IN AS VENUE");
			$('#signup-link').attr("href", registerArtistUrl);
			login_type = 'artist';
		}

	}

	function loadProfilePicture(event) {
	    var output = document.getElementById('profile-picture-preview');
	    output.src = URL.createObjectURL(event.target.files[0]);
	};
	</script>

	@yield('head')

</head>
</body>
	@yield('content')
	@yield('scripts')

	<script src="{{ asset('js/webshim/minified/polyfiller.js')}}"></script> 
	<script> 
	    webshims.polyfill('forms');
	</script>
	<script>
		$(document).ready(function(){
	    // the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered
	    $('.modal-trigger').leanModal();
	    $('.time-picki-picker').timepicki();
	  });
	</script>

</body>
</html>
