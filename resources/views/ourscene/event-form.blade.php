@extends('ourscene/layouts.main')

@section('head')
	
<!-- Autocomplete -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

@endsection

@section('content')

<div id="event-form" class="card">

	<div class="card-action title">
		<img src="{{ asset('images/icons/create.svg') }}"/>
		
	@if(Session::get('user_type') == 'venue')
		Create Event/Promotion
	@elseif(Session::get('user_type') == 'artist')
		Request a Show
	@endif
	</div>

@if(Session::get('user_type') == 'venue')
	<div id="event-form-nav" class="card-action">
		<input name="event_form_type" type="radio" id="show-create-regular-event-form" class="with-gap" checked="checked" value="regular_event">
		<label for="show-create-regular-event-form">Create event</label>
		&nbsp; &nbsp;
		<input name="event_form_type" type="radio" id="show-create-promotion-form" class="with-gap" value="promotion">
		<label for="show-create-promotion-form">Create Promotion</label>
	</div>
@endif

	<div id="event-form-container">

		<div id="regular-event-form-container">
			@include('ourscene.regular-event-form')
		</div>

		<div id="promotion-form-container" style="display: none;">
			@include('ourscene.promotion-form')
		</div>
	</div>
</div>

<!-- Modals -->

@if(Session::get('user_type') == 'venue')
	
	<!-- Invite artist modal -->
	@include('modals.invite-artist-modal')

	<!-- Edit invite artist modal -->
	@include('modals.edit-invite-artist-modal')

@endif

<!-- Add house equipments modal -->

@include('modals.add-house-equipments-modal')

<!-- Autocompletes -->

@if(Session::get('user_type') == 'venue')
	
	<!-- Invite artist modal > Artist autocomplete -->

	@include('scripts/autocomplete-user',
		['user_type' => 'artist',
		'textbox_selector' => '#invite-artist-modal input[name=artist_name]',
		'hidden_value_selector' => '#invite-artist-modal input[name=artist_id]',
		'dropdown_selector' => '#invite-artist-modal .artist-name-autocomplete-dropdown']
	)

@elseif(Session::get('user_type') == 'artist')
	
	<!-- Event details > Venue autocomplete -->

	@include('scripts/autocomplete-user',
		['user_type' => 'venue',
		'textbox_selector' => '#venue-name-autocomplete',
		'hidden_value_selector' => '#venue-id',
		'dropdown_selector' => '#venue-name-autocomplete-dropdown']
	)

@endif

@endsection

@section('scripts')

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
</script>
<script src="{{ asset('js/event-form.js') }}"></script>

@endsection