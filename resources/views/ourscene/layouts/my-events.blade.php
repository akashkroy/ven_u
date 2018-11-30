@extends('ourscene.layouts.main')

@section('head')
	@yield('my-events-head')
@stop

@section('content')

	@if(Session::get('user_type') == 'venue')

		@include('navs.main-navigation-3',
			['items' => array(
				array('text' => "CALENDAR", 'image' => asset('images/icons/calendar-purple.svg'), 'image-active' => asset('images/icons/calendar-white.svg'), 'url' => action('EventController@getMyEventsCalendar')),
				)
			]
		)

	@elseif(Session::get('user_type') == 'artist')

		@include('navs.main-navigation-3',
			['items' => array(
				array('text' => "EVENTS", 'image' => asset('images/icons/calendar-events-purple.svg'), 'image-active' => asset('images/icons/calendar-events-white.svg'), 'url' => action('EventController@getMyEventsEvents')),
				array('text' => "CALENDAR", 'image' => asset('images/icons/calendar-purple.svg'), 'image-active' => asset('images/icons/calendar-white.svg'), 'url' => action('EventController@getMyEventsCalendar')),
				)
			]
		)

	@endif

	@yield('my-events-content')

@stop

@section('scripts')
	
	@yield('my-events-scripts')

@stop