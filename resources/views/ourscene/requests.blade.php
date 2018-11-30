<?php
	use OurScene\Models\Event;
	use OurScene\Models\Service;
	use OurScene\Helpers\DatetimeUtils;
?>

@extends('ourscene.layouts.main')

@section('head')
	
@stop

@section('content')

<div id="requests">

	@if(count($pending_requests))
		<div class="row">
		@foreach($pending_requests as $request)
			<?php
				$event = Event::find($request->event_id);
			?>
			<div class="col s12 m4 l3">
				<div class="ourscene-card-1 request-card">
					<div class="card main-container">
						<div class="card-content">
							<div class="profile-pic-container">
								<div class="circular-img-container profile-pic" style="background-image: url('{{ getProfilePicture($request->sender_id) }}')"></div>
							</div>
						</div>
						<div class="card-action performance-time">
							<span class="left">{{ date('d F Y', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($request['start_datetime'])->sec) }}</span>
							<span class="right">{{ date('h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($request['start_datetime'])->sec) }}</span>
							<br/>
							<span class="left">{{ date('d F Y', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($request['end_datetime'])->sec) }}</span>
							<span class="right">{{ date('h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($request['end_datetime'])->sec) }}</span>
						</div>
						<div class="card-action">
							<div class="event-title">{{ $event['title'] }}</div>
							<div class="event-venue"><a href="{{ action('UserController@getPublicProfile', array('id' => $event['venue']['id'])) }}">{{ $event['venue']['name'] }}</a></div>
						</div>
						<div class="card-action artist-applied-total">
							<img src="{{ asset('images/icons/artist-applied-orange.svg') }}" class="icon"/>
						{{ Service::servicesByEventId($event['_id'])->performance()->pending()->count() }}
						</div>
					</div>
					<div class="action-container">
						
						<?php
							$accept_on_click = '';
							$next_time_on_click = '';

							if($request['type'] == 'performance'){
								
								$start_date = date('m/d/Y', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($request['start_datetime'])->sec);
								$end_date = date('m/d/Y', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($request['end_datetime'])->sec);

								$start_time = DatetimeUtils::formatTimeFromBackendToFrontend(DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($request['start_datetime'])->sec);
								$end_time = DatetimeUtils::formatTimeFromBackendToFrontend(DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($request['end_datetime'])->sec);

								$accept_on_click = "showConfirmRequestForPerformanceModal('{$request['_id']}', '{$start_date}', '{$start_time}', '{$end_date}', '{$end_time}')";
								$next_time_on_click = "showRejectRequestForPerformanceModal('{$request['_id']}')";
							}
							else if($request['type'] == 'service'){
								$accept_on_click = "showConfirmRequestForServiceModal('{$request['_id']}')";
								$next_time_on_click = "showRejectRequestForServiceModal('{$request['_id']}')";
							}
						?>

						<div class="row action-series">
							<div class="col s6 m6 l6">
								<a class="btn ourscene-btn-2" onClick="{{ $accept_on_click }}">ACCEPT</a>
							</div>
							<div class="col s6 m6 l6">
								<a class="btn ourscene-btn-3" onClick="{{ $next_time_on_click }}">NEXT TIME</a>
							</div>
						</div>
					</div>
				
					<a href="{{ action('EventController@getEvent', array('id' => $event['_id'])) }}">
						<img src="{{ asset('images/icons/calendar-events-purple.svg') }}" class="icon event-icon"/>
					</a>
				@if($event['pay_to_play'])
					<img src="{{ asset('images/icons/pay-to-play.svg') }}" class="icon pay-to-play-icon"/>
				@endif
				</div>
			</div>
		@endforeach
		</div>
	@else
		<div class="no-requests center-align">No requests yet.</div>
	@endif
</div>

<!-- Modals -->
	
<!-- Confirm change status of service modal -->

@include('modals.confirm-with-link-modal', [
	'modal_id' => 'confirm-change-status-of-service-modal',
	'modal_content' => '',
	'modal_confirm_link' => '',
])

<!-- Confirm request for performance modal -->

@include('modals.confirm-request-for-performance-modal')

<!-- Confirm request for service modal -->

@include('modals.confirm-request-for-service-modal')

@stop

@section('scripts')

<script src="{{ asset('js/view-event.js') }}"></script>

@stop