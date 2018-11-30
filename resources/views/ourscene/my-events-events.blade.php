<?php
	use OurScene\Models\Event;
?>

@extends('ourscene.layouts.my-events')

@section('my-events-head')

@stop

@section('my-events-content')

<div id="my-events-events">

	<!-- Booked -->

	<div class="card events-under-status">
		<div class="header-title booked-color">
			<img class="header-icon" src="{{ asset('images/icons/approved.svg') }}"/>
			Booked
		</div>
		<div class="card-action content">
		@if(count($confirmed_events))
			<table>
			@foreach($confirmed_events as $confirmed_event)
				<?php
					$event = Event::find($confirmed_event->event_id);
				?>
				<tr>
					<td>
						<div class="row">
							<div class="col s9 m2 l1">
								<div class="circular-img-container profile-pic" style="background-image: url('{{ getProfilePicture($event['venue']['id']) }}')"></div>
							</div>

							<div class="col s3 hide-on-med-and-up action right-align">
								<a href="{{ action('MessageController@getMessageConversationWithUser', array('id' => $event['venue']['id'])) }}" class="btn ourscene-btn-1 l-display-only">Message</a>
								<a href="{{ action('MessageController@getMessageConversationWithUser', array('id' => $event['venue']['id'])) }}" class="message-btn l-no-display right">
									<img src="{{ asset('images/icons/message-purple.svg') }}" class="table-icon">
								</a>
								<a class="invisible"><img class="table-icon" src="{{ asset('images/icons/cancel.svg') }}"/></a>
							</div>

							<div class="col s12 m2 l2" >
								<span class="bold-weight">{{ $event['venue']['name'] }}</span>
							</div>
							<div class="col s12 m2 l2" style="overflow-wrap: break-word; word-wrap: break-word;">
								<a href="{{ action('EventController@getEvent', array('id' => $event['_id'])) }}" class="event-title-link">{{ $event['title'] }}</a>
							</div>
							<div class="col s12 m2 l2">
								{{ date('F d, Y h:i A', $confirmed_event['start_datetime']->sec) }}
							</div>
							<div class="col s12 m2 l2">
								{{ date('F d, Y h:i A', $confirmed_event['end_datetime']->sec) }}
							</div>
							<div class="col hide-on-small-only m2 l3 action right-align">
								<a href="{{ action('MessageController@getMessageConversationWithUser', array('id' => $event['venue']['id'])) }}" class="btn ourscene-btn-1 l-display-only">Message</a>
								<a href="{{ action('MessageController@getMessageConversationWithUser', array('id' => $event['venue']['id'])) }}" class="message-btn l-no-display">
									<img src="{{ asset('images/icons/message-purple.svg') }}" class="table-icon">
								</a>
								<a class="invisible"><img class="table-icon" src="{{ asset('images/icons/cancel.svg') }}"/></a>
							</div>
						</div>
					</td>
				</tr>
			@endforeach
			</table>
		@else
			<div class="no-events center-align">No booked events</div>
		@endif
		</div>
	</div>

	<!-- Pending -->

	<div class="card events-under-status">
		<div class="header-title pending-color">
			<img class="header-icon" src="{{ asset('images/icons/pending.svg') }}"/>
			Pending
		</div>
		<div class="card-action content">
		@if(count($pending_events))
			<table>
			@foreach($pending_events as $pending_event)
				<?php
					$event = Event::find($pending_event->event_id);
				?>
				<tr>
					<td>
						<div class="row">
							<div class="col s9 m2 l1">
								<div class="circular-img-container profile-pic" style="background-image: url('{{ getProfilePicture($event['venue']['id']) }}')"></div>
							</div>
							<div class="col s3 hide-on-med-and-up action right-align">
								<a href="{{ action('MessageController@getMessageConversationWithUser', array('id' => $event['venue']['id'])) }}" class="btn ourscene-btn-1 l-display-only">Message</a>
								<a href="{{ action('MessageController@getMessageConversationWithUser', array('id' => $event['venue']['id'])) }}" class="message-btn l-no-display">
									<img src="{{ asset('images/icons/message-purple.svg') }}" class="table-icon">
								</a>
								<a onClick="showCancelRequestForPerformanceModal('{{ $pending_event['_id'] }}')"><img class="table-icon" src="{{ asset('images/icons/cancel.svg') }}"/></a>
							</div>
							<div class="col s12 m2 l2">
								<span class="bold-weight">{{ $event['venue']['name'] }}</span>
							</div>
							<div class="col s12 m2 l2">
								<a href="{{ action('EventController@getEvent', array('id' => $event['_id'])) }}" class="event-title-link">{{ $event['title'] }}</a>
							</div>
							<div class="col s12 m2 l2">
								{{ date('F d, Y h:i A', $pending_event['start_datetime']->sec) }}
							</div>
							<div class="col s12 m2 l2">
								{{ date('F d, Y h:i A', $pending_event['end_datetime']->sec) }}
							</div>
							<div class="col hide-on-small-only m2 l3 action right-align">
								<a href="{{ action('MessageController@getMessageConversationWithUser', array('id' => $event['venue']['id'])) }}" class="btn ourscene-btn-1 l-display-only">Message</a>
								<a href="{{ action('MessageController@getMessageConversationWithUser', array('id' => $event['venue']['id'])) }}" class="message-btn l-no-display">
									<img src="{{ asset('images/icons/message-purple.svg') }}" class="table-icon">
								</a>
								<a onClick="showCancelRequestForPerformanceModal('{{ $pending_event['_id'] }}')"><img class="table-icon" src="{{ asset('images/icons/cancel.svg') }}"/></a>
							</div>
						</div>
					</td>
				</tr>
			@endforeach
			</table>
		@else
			<div class="no-events center-align">No pending requests</div>
		@endif
		</div>
	</div>

	<!-- Next time -->

	<div class="card events-under-status">
		<div class="header-title next-time-color">
			<img class="header-icon" src="{{ asset('images/icons/next-time.svg') }}"/>
			Next time
		</div>
		<div class="card-action content">
		@if(count($rejected_events))
			<table>
			@foreach($rejected_events as $rejected_event)
				<?php
					$event = Event::find($rejected_event->event_id);
				?>
				<tr>
					<td>
						<div class="row">
							<div class="col s9 m2 l1">
								<div class="circular-img-container profile-pic" style="background-image: url('{{ getProfilePicture($event['venue']['id']) }}')"></div>
							</div>
							<div class="col s3 hide-on-med-and-up action right-align">
								<a href="{{ action('MessageController@getMessageConversationWithUser', array('id' => $event['venue']['id'])) }}" class="btn ourscene-btn-1 l-display-only">Message</a>
								<a href="{{ action('MessageController@getMessageConversationWithUser', array('id' => $event['venue']['id'])) }}" class="message-btn l-no-display right">
									<img src="{{ asset('images/icons/message-purple.svg') }}" class="table-icon">
								</a>
								<a class="invisible"><img class="table-icon" src="{{ asset('images/icons/cancel.svg') }}"/></a>
							</div>
							<div class="col s12 m2 l2">
								<span class="bold-weight">{{ $event['venue']['name'] }}</span>
							</div>
							<div class="col s12 m2 l2">
								<a href="{{ action('EventController@getEvent', array('id' => $event['_id'])) }}" class="event-title-link">{{ $event['title'] }}</a>
							</div>
							<div class="col s12 m2 l2">
								{{ date('F d, Y h:i A', $rejected_event['start_datetime']->sec) }}
							</div>
							<div class="col s12 m2 l2">
								{{ date('F d, Y h:i A', $rejected_event['end_datetime']->sec) }}
							</div>
							<div class="col hide-on-small-only m2 l3 action right-align">
								<a href="{{ action('MessageController@getMessageConversationWithUser', array('id' => $event['venue']['id'])) }}" class="btn ourscene-btn-1 l-display-only">Message</a>
								<a href="{{ action('MessageController@getMessageConversationWithUser', array('id' => $event['venue']['id'])) }}" class="message-btn l-no-display">
									<img src="{{ asset('images/icons/message-purple.svg') }}" class="table-icon">
								</a>
								<a class="invisible"><img class="table-icon" src="{{ asset('images/icons/cancel.svg') }}"/></a>
							</div>
						</div>
					</td>
				</tr>
			@endforeach
			</table>
		@else
			<div class="no-events center-align">No rejected requests</div>
		@endif
		</div>
	</div>

</div>

<!-- Modals -->

<!-- Change status of service modal -->

	@include('modals.confirm-with-link-modal', [
		'modal_id' => 'change-status-of-service-modal',
		'modal_content' => '',
		'modal_confirm_link' => '',
	])

@stop

@section('my-events-scripts')

<script src="{{ asset('js/my-events-events.js') }}"></script>

@stop
