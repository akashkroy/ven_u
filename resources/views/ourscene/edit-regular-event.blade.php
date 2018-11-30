@extends('ourscene/layouts.main')

<?php
	use OurScene\Helpers\DatetimeUtils;
?>

@section('head')

<!-- Autocomplete -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

@endsection

@section('content')

<div id="event-form" class="card">

	<div class="card-action title">
		<img src="{{ asset('images/icons/create.svg') }}"/>
		Edit Event <span class="edit-event-title">{{ $event['title'] }}</span>

		<a class="btn ourscene-btn-3 right delete-event-btn modal-trigger" href="#confirm-cancel-event-modal">Cancel event</a>
	</div>

	<div id="event-form-container">
		<div id="regular-event-form-container">
			@include('ourscene.regular-event-form')
		</div>
	</div>
</div>

<!-- Modals -->

@if(Session::get('user_type') == 'venue')
	
	<!-- Invite artist modal -->
	@include('modals.invite-artist-modal')
	
	<!-- Edit invite artist modal -->
	@include('modals.edit-invite-artist-modal')
	
	<!-- Edit artist lineup modal -->
	@include('modals.edit-artist-lineup-modal')
	
	<!-- Edit invited artist modal -->
	@include('modals.edit-invited-artist-modal')

@endif

<!-- Confirm delete event modal -->

@include('modals.confirm-with-link-modal', [
	'modal_id' => 'confirm-cancel-event-modal',
	'modal_content' => 'Are you sure you want to <span class="reject-color bold-weight">cancel</span> this event?',
	'modal_confirm_link' => action('EventController@getCancelEvent', array('id' => $event['_id'])),
])

@include('modals.add-house-equipments-modal')

<!-- Autocompletes -->

<!-- Invite artist modal > Artist autocomplete -->

@include('scripts/autocomplete-user',
	['user_type' => 'artist',
	'textbox_selector' => '#invite-artist-modal input[name=artist_name]',
	'hidden_value_selector' => '#invite-artist-modal input[name=artist_id]',
	'dropdown_selector' => '#invite-artist-modal .artist-name-autocomplete-dropdown']
)


@endsection

@section('scripts')

<script>

$(document).ready(function(){

	//uncheck all event types
	$("input[name=type]").prop('checked', false);

	//check the edit event type
	$("input[name=type][value='{{ $event['event_type'] }}']").prop('checked', true);

	//check if there is not a checked event type
	if(! $("input[name='type']:checked").length){
		//initialize and enable the other event type
		$("input[name=type][value='other']").prop('checked', true);

		$event_other_type = $("#other-type");

		$event_other_type.val("{{ $event['event_type'] }}");
		$event_other_type.prop('disabled', false);
	}

	//check the edit event age requirements
	$("input[name=age_requirements][value='{{ $event['age_requirements'] }}']").prop('checked', true);

@if($event['pay_to_play'])
	//check pay to play
	$("input[name=pay_to_play]").prop('checked', true);
@endif
});
</script>

<!-- Event form JS -->

<script>
	var AJAX_AUTOCOMPLETE_ARTISTS = "{{ action('UserController@getAutocompleteArtists') }}";
	var AJAX_AUTOCOMPLETE_VENUES = "{{ action('UserController@getAutocompleteVenues') }}";
	var USER_TYPE = "{{ Session::get('user_type') }}";
	var FORM_ACTION = "{{ $form_action }}";

	var edit_icon_src = "{{ asset('images/icons/create.svg') }}";
	var remove_icon_src = "{{ asset('images/icons/delete.svg') }}";

	var invite_artists = [];
	var idx_invite_artist = 0;

	var invited_artists = {};
@foreach($invited_artists as $service)
	invited_artists['{{ $service->_id }}'] = {
		"artist": {
			"id": "{{ $service['artist']['id'] }}",
			"name": "{{ $service['artist']['name'] }}",
		},
		"performance_time": {
			"start_date": "{{ DatetimeUtils::formatDateFromBackendToFrontEnd(DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service->start_datetime)->sec) }}",
			"start_time": "{{ DatetimeUtils::formatTimeFromBackendToFrontend(DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service->start_datetime)->sec) }}",
			"end_date": "{{ DatetimeUtils::formatDateFromBackendToFrontEnd(DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service->end_datetime)->sec) }}",
			"end_time": "{{ DatetimeUtils::formatTimeFromBackendToFrontend(DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service->end_datetime)->sec) }}",
		}
	}
@endforeach
	var delete_invited_artist_ids = [];

	var artist_lineup = {};
@foreach($artist_lineup as $service)
	artist_lineup['{{ $service->_id }}'] = {
		"artist": {
			"id": "{{ $service['artist']['id'] }}",
			"name": "{{ $service['artist']['name'] }}",
		},
		"performance_time": {
			"start_date": "{{ DatetimeUtils::formatDateFromBackendToFrontEnd(DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service->start_datetime)->sec) }}",
			"start_time": "{{ DatetimeUtils::formatTimeFromBackendToFrontend(DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service->start_datetime)->sec) }}",
			"end_date": "{{ DatetimeUtils::formatDateFromBackendToFrontEnd(DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service->end_datetime)->sec) }}",
			"end_time": "{{ DatetimeUtils::formatTimeFromBackendToFrontend(DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service->end_datetime)->sec) }}",
		}
	}
@endforeach
	var delete_artist_lineup_ids = [];

</script>
<script src="{{ asset('js/event-form.js') }}"></script>

@endsection
