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

	<!-- Materialize -->
	<link type="text/css" rel="stylesheet" href="{{ asset('materialize/css/materialize.min.css') }}"  media="screen,projection"/>
	<script type="text/javascript" src="{{ asset('materialize/js/materialize.min.js') }}"></script>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<!-- Moment -->
	<script src="{{ asset('moment/moment.min.js') }}"></script>

	<!-- Bootstrap Datetimepicker -->
	<link href="{{ asset('/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
	<script src="{{ asset('bootstrap-datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>

	<!-- TimePicki Timepicker -->
	<script src="{{ asset('js/timepicki.js') }}"></script>
	<link type="text/css" rel="stylesheet" href="{{ asset('css/timepicki.css') }}"/>

	<script>
		var ROOT = "{{ action('HomeController@getIndex') }}";
		var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
	</script>

	<!-- Datetime utils -->
	<script src="{{ asset('js/utils/datetime_utils.js') }}"></script>

	<!-- Autocomplete -->
	<link href="{{ asset('/css/autocomplete.css') }}" rel="stylesheet">

	<!-- Fancy Box -->
	<script type="text/javascript" src="{{ asset('js/jquery.fancybox.pack.js') }}"></script>
	<link rel="stylesheet" href="{{ asset('/css/jquery.fancybox.css') }}" media="screen" title="no title" charset="utf-8">
	@yield('head')

	<!-- Ourscene styles -->
	<link href="{{ asset('css/style.css') }}" rel="stylesheet">

</head>
<body>

	@include('navs/authenticated-navigation')
	@yield('content')

	<script>
		/* Alerts */
		@if(Session::has('success'))
			Materialize.toast("{{ Session::get('success') }}", 3000);
		@endif

		@if(Session::has('error'))
			Materialize.toast("{{ Session::get('error') }}", 3000);
		@endif

		$(".button-collapse").sideNav();

		$('.modal').on('shown', function(){
		  console.log('show');
		  $('body').css({overflow: 'hidden'});
		  $('body').css({position: 'fixed'});
		}) .on('hidden', function(){
		  $('body').css({overflow: ''});
		  $('body').css({position: ''});
		});

		var user_id = "{{ Session::get('id') }}";
		function checkMessagesCounter(){
			$.ajax({
				 url: "{{ action('MessageController@getAjaxNumberOfUnreadMessages')}}",
				 type: "GET",
				 dataType: "json",
				 success: function(data){
					console.log(data);
					if (data == "false") {

					}else {
						var unread = data;
						if(unread > 0){
			    			$('.messages-counter-icon').show();
			    			$('.messages-counter-icon').text(unread);
			    		}else{
			    			$('.messages-counter-icon').hide();
			    		}
					}
				 }
			 }).done(function(data){
			 }).fail(function(data){
			 }).always(function(){
			 })
		}
		checkMessagesCounter();
		//var autoReloadMessageCounter = setInterval("checkMessagesCounter()", 5000);

	</script>

	<script type="text/javascript" src="{{ asset('js/main.js') }}"></script>

	<script src="{{ asset('js/webshim/minified/polyfiller.js')}}"></script>
	<script>
	    webshims.polyfill('forms');
	</script>

	<!-- Main navigation search script -->
	@include('scripts/search', ['textbox_selector' => '#main-navigation-search-input', 'dropdown_selector' => '#main-navigation-search-dropdown'])

	<!-- Sidebar (mobile) search script -->
	@include('scripts/search-mobile', ['textbox_selector' => '#search-sidebar', 'dropdown_selector' => '#search-sidebar-dropdown'])

	@yield('scripts')

</body>
</html>
