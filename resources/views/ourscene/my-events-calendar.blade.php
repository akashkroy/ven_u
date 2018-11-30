@extends('ourscene.layouts.my-events')

@section('my-events-head')

<!-- FullCalendar -->

<script src="{{ asset('fullcalendar/fullcalendar.js') }}"></script>
<link rel='stylesheet' href="{{ asset('fullcalendar/fullcalendar.css') }}"></link>

@stop

@section('my-events-content')

<div id="my-events-calendar">

	<div id="calendar-container" class="card">
		
		<!-- Calendar title -->

		<div id="calendar-title-container">
			<img src="{{ asset('images/icons/calendar-month-purple.svg') }}"/>
			<span id="calendar-title"></span>
		</div>

		<!-- Calendar -->

		<div id="calendar" class="ourscene-calendar"></div>
				
	</div>

	<!-- Create event from drag and drop form -->

	{!! Form::open(array(
			'id' 			=>	'create-event-from-drag-and-drop-form',
			'url'			=>	action('EventController@postCreateEventFromDragAndDrop'),
			'method'		=>	'POST'
	))	!!}
		<input type="hidden" name="start_datetime" id="start-datetime"/>
		<input type="hidden" name="end_datetime" id="end-datetime"/>
	{!!Form::close()!!}
</div>
@stop

@section('my-events-scripts')

<script type="text/javascript" src="{{ asset('js/my-events-calendar.js') }}"></script>

@stop