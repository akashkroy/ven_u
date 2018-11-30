<?php
use OurScene\Models\User;
use OurScene\Helpers\DatetimeUtils;

if($form_action == "add"){
	$form_url = action('EventController@postCreateEvent');
	$submit_btn_label = 'Create';
}
else{
	$form_url = action('EventController@postEditEvent', array('id' => $event['_id']));
	$submit_btn_label = 'Update';
}

?>
<style media="screen">
	.dropdown-content.autocomplete{
		width: 94%;
	}
</style>

{!! Form::open(array(
	'id'			=> 'regular-event-form',
	'url'			=> $form_url,
	'action'		=> 'POST'
)) !!}

<div id="error-in-regular-event-form" class="error-field" style="display: none;">
	Please check and review the form again.
</div>

<div class="section-header">Event details</div>
<br/><br/>
Required items are indicated with <span class="required-color">*</span>.
<br/><br/>

<!-- Event details -->

<div id="event-details" class="section">
	<input type="hidden" id="timezone_offset" name="timezone_offset" value=""/>
	<div class="row">
		<div class="input-field col s12 m8 l4">
			<input type="text" name="title" id="title" placeholder="Event Name" value="{{ isset($event) ? $event['title'] : '' }}" required/>
			<label for="title" class="active"><span class="required-color">*</span> Event name</label>
		</div>
	</div>

	<div id="error-start-date-more-than-end-date" class="error-field" style="display: none;">
		Start datetime should be earlier than your end datetime.
	</div>

	<div class="row">
		<div class="input-field col s12 m8 l4">
			<input type="text" name="start_date" id="start-date" placeholder="" 
				class="ourscene-date"  readonly="readonly"  value="<?= $start_date; ?>" required>
			<label for="start-date" class="active"><span class="required-color">*</span> Start date</label>
		</div>
		<div class="input-field col s12 m8 l4">
			<input type="text" class="time-picki-picker" name="start_time" id="start-time" placeholder="" value="<?= $start_time; ?>" required>
			<label for="start-time" class="active"><span class="required-color">*</span> Start time</label>
		</div>
	</div>
	<div class="row">
		<div class="input-field col s12 m8 l4">
			<input type="text" name="end_date" id="end-date" placeholder=""
				class="ourscene-date" readonly="readonly" value="<?= $end_date; ?>" required>
			<label for="end-date" class="active"><span class="required-color">*</span> End date</label>
		</div>
		<div class="input-field col s12 m8 l4">
			<input type="text" class="time-picki-picker" name="end_time" id="end-time" placeholder="" value="<?= $end_time; ?>" required>
			<label for="end-time" class="active"><span class="required-color">*</span> End time</label>
		</div>
	</div>

	<div id="error-opening-date-more-than-start-date" class="error-field" style="display: none;">
		The opening time of venue should be on or before the start datetime of the event.
	</div>
	<div class="row">
		<div class="input-field col s12 m8 l4">
			<input type="text" class="time-picki-picker" name="opening_time" id="opening-time" placeholder="" @if(isset($opening_time) && $opening_time!="") value="{{$opening_time}}" @endif/>
			<label for="opening-time" class="active"> Doors</label>
		</div>
	</div>
	<div class="row">
		<div class="input-field col s12 m8 l4">
			<textarea name="description" id="description" class="materialize-textarea" placeholder="Description">{{ isset($event) ? $event['description'] : '' }}</textarea>
			<label for="description" class="active">Description</label>
		</div>
	</div>
	@if(Session::get('user_type') == 'artist')
		<div id="error-no-venue-selected" class="error-field" style="display: none;">
			Please select a venue.
		</div>
		<div class="row">
			<div class="input-field col s12 m8 l4">
				<input type="text" id="venue-name-autocomplete" class="select-dropdown" placeholder="Venue Name" required autocomplete="off">
				<label for="venue-name-autocomplete" class="active"><span class="required-color">*</span> Venue Request</label>
				<div>
					<ul id="venue-name-autocomplete-dropdown"  class="dropdown-content autocomplete" style="top:35px;">
					</ul>
				</div>
				<!-- <div class="arrow" style="width: 0; height: 0; border-left: 5px solid transparent; border-right: 5px solid transparent; border-top: 5px solid black; float:right; margin-top: -30px">

				</div> -->
				<input type="hidden" id="venue-id" name="venue_id" value=""/>
			</div>
		</div>
	@else
		<input type="hidden" id="venue-id" name="venue_id" value="{{ Session::get('id') }}"/>
	@endif
	<div class="row">
		<div class="col s12 m12 l8">
			<div>
				<label class="ourscene-label-1"><span class="required-color">*</span> Event type</label>
			</div>
			<div class="input-field row">

				<div class="col s12 m6 l4">
					<input name="type" type="radio" id="music" class="with-gap" value="music" checked="checked" required/>
					<label for="music">Music</label>
				</div>
				<div class="col s12 m6 l4">
					<input name="type" type="radio" id="comedy" class="with-gap" value="comedy (stand up)"/>
					<label for="comedy">Comedy (Stand Up)</label>
				</div>
				<div class="col s12 m6 l4">
					<input name="type" type="radio" id="theatrical-performance" class="with-gap" value="theatrical performance"/>
					<label for="theatrical-performance">Theatrical Performance</label>
				</div>
				<div class="col s12 m6 l4">
					<input name="type" type="radio" id="dance" class="with-gap" value="dance"/>
					<label for="dance">Dance</label>
				</div>
				<div class="col s12 m6 l4">
					<input name="type" type="radio" id="other" class="with-gap" value="other"/>
					<label for="other">Other</label>

					<div>
						<input name="other_type" type="text" id="other-type" value="" disabled="disabled" required/>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col s12 m12 l8">
			<div>
				<label class="ourscene-label-1"><span class="required-color">*</span> Age requirements</label>
			</div>
			<div class="input-field">
				<p>
					<input name="age_requirements" type="radio" id="age-req-none" class="with-gap" value="none" checked="checked"/>
					<label for="age-req-none">None</label>
				</p>
				<p>
					<input name="age_requirements" type="radio" id="age-req-18-plus" class="with-gap" value="18+"/>
					<label for="age-req-18-plus">18+</label>
				</p>
				<p>
					<input name="age_requirements" type="radio" id="age-req-21-plus" class="with-gap" value="21+"/>
					<label for="age-req-21-plus">21+</label>
				</p>
			</div>
		</div>
	</div>
	<br/><br/>
	<div class="row">
		<div class="input-field col s12 m8 l4">
			<textarea name="cover_charge" id="cover-charge" class="materialize-textarea" placeholder="">{{ isset($event) ? $event['cover_charge'] : '' }}</textarea>
			<label for="cover-charge" class="active">Cover charge</label>
		</div>
	</div>
	<br/><br/>
	<div class="row">
		<div class="input-field col s12 m8 l4">
			<input type="checkbox" name="pay_to_play" id="pay-to-play" value="true"/>
			<label for="pay-to-play" class="active">Pay to play</label>
		</div>
	</div>
</div>

<!-- Equipments -->

<div id="equipments" class="section">
	<div class="section-header"><img src="{{ asset('images/icons/house-equipment.svg') }}" width="25" style="position:relative;top:.5em;margin-right:.4em;" alt="" />
		<span>Equipment</span>
	</div>

	<div class="row">
		<div class="col s12 m8 l6">
			<table id="add-equipments-table">
			@foreach($equipments as $equipment)
				<tr class="equipment-row">
					<td>
						<b>{{ $equipment->name }}</b>
					@if(count($equipment->inclusion))
						<br/><br/>
						@foreach($equipment->inclusion as $inclusion)
							<p>{{ $inclusion }}</p>
						@endforeach
					@endif
					</td>
					<td class="right-align">
						<a onclick="removeHouseEquipment(this)">
							<img class="remove-icon" src="{{ asset('images/icons/delete.svg') }}"/>
						</a>
						<input type="hidden" name="equipments[]" value="{{ $equipment->_id }}">
					</td>
				</tr>
			@endforeach
			</table>

			<br/><br/>

	    	<!-- Add house equipment button -->
			<button type="button" data-target="add-house-equipments-modal" class="btn ourscene-btn-2 modal-trigger">Add Equipment</a>
		</div>
	</div>
</div>

@if(Session::get('user_type') == 'venue' && $form_action == 'edit')

<!-- Artist lineup -->

<div id="artist-lineup" class="section">
	<div class="section-header"><img src="{{ asset('images/icons/artist.svg') }}" width="25" style="position:relative;top:.5em;margin-right:.4em;" alt="" />Artist lineup</div>

	<div id="artist-lineup-performance-outside-event" class="error-field" style="display: none;">
		An artist can only perform on the event duration.
	</div>

	<div class="row">
		<div class="col s12 m12 l8">
			<table id="artist-lineup-table">
			@foreach($artist_lineup as $service)
			<tr class="artist-row entry-{{ $service->_id }}">
				<td>
					<div class="row">
						<div class="col s12 m6 l6">
							<div class="ourscene-label-1">Name</div>
							{{ $service['artist']['name'] }}<br/><br/>
						</div>
						<div class="col s12 m6 l6">
							<div class="ourscene-label-1">Performance Time</div> <br/>
							<b>Start</b><br/><span class="start-datetime">{{ date('d F Y h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service['start_datetime'])->sec) }}</span><br/>
							<b>End</b><br/><span class="end-datetime">{{ date('d F Y h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service['end_datetime'])->sec) }}</span><br/>
						</div>
					</div>
				</td>
				<td class="right-align">
					<a onclick="showEditArtistLineupModal(this, '{{ $service->_id }}')">
						<img class="remove-icon" src="{{ asset('images/icons/create.svg') }}"/>
					</a>
					<a onclick="removeArtistLineup(this, '{{ $service->_id }}')">
						<img class="remove-icon" src="{{ asset('images/icons/delete.svg') }}"/>
					</a>
				</td>
			</tr>
			@endforeach
			</table>
		</div>
	</div>
</div>

<!-- Invited artists -->

<div id="invited-artists" class="section">
	<div class="section-header"><img src="{{ asset('images/icons/artist.svg') }}" width="25" style="position:relative;top:.5em;margin-right:.4em;" alt="" />Invited artists</div>

	<div id="invited-artists-performance-outside-event" class="error-field" style="display: none;">
		The requested performance schedule for each invited artists should be within the event duration.
	</div>
 
	<div class="row">
		<div class="col s12 m12 l8">
			<table id="invited-artists-table">
			@foreach($invited_artists as $service)
			<tr class="artist-row entry-{{ $service->_id }}">
				<td>
					<div class="row">
						<div class="col s12 m6 l6">
							<div class="ourscene-label-1">Name</div>
							{{ $service['artist']['name'] }}<br/><br/>
						</div>
						<div class="col s12 m6 l6">
							<div class="ourscene-label-1">Performance Time</div> <br/>
							<b>Start</b><br/><span class="start-datetime">{{ date('d F Y h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service['start_datetime'])->sec) }}</span><br/>
							<b>End</b><br/><span class="end-datetime">{{ date('d F Y h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service['end_datetime'])->sec) }}<span><br/>
						</div>
					</div>
				</td>
				<td class="right-align">
					<a onclick="showEditInvitedArtistModal(this, '{{ $service->_id }}')">
						<img class="remove-icon" src="{{ asset('images/icons/create.svg') }}"/>
					</a>
					<a onclick="removeInvitedArtist(this, '{{ $service->_id }}')">
						<img class="remove-icon" src="{{ asset('images/icons/delete.svg') }}"/>
					</a>
				</td>
			</tr>
			@endforeach
			</table>
		</div>
	</div>
</div>


@endif

@if(Session::get('user_type') == 'venue')

<!-- Invite artist -->

<div id="invite-artist" class="section">
	<div class="section-header"><img src="{{ asset('images/icons/artist.svg') }}" width="25" style="position:relative;top:.5em;margin-right:.4em;" alt="" />Invite artists</div>

	<div id="error-no-artists" class="error-field" style="display: none;">
		Please add at least one artist.
	</div>
	<div id="invite-artists-performance-outside-event" class="error-field" style="display: none;">
		The requested performance schedule for each invited artists should be within the event duration.
	</div>
	<div id="invite-artists-performance-should-have-duration" class="error-field" style="display: none;">
		The requested performance schedule for each invited artists should not have the same start and end time.
	</div>

	<div class="row">
		<div class="col s12 m12 l8">
			<table id="invite-artists-table">
			</table>

			<br/><br/>

	    	<!-- Invite artist button -->
			<a onClick="showInviteArtistModal()" class="btn ourscene-btn-2">Add artist</a>
		</div>
	</div>
</div>

@endif

<br/><br/>

<input type="submit" id="submit-btn" class="btn btn-large ourscene-btn-1" value="{{ $submit_btn_label }}"/>

<script>
$(document).ready(function() {
	var timezone_offset = new Date().getTimezoneOffset();
	$("#timezone_offset").val(timezone_offset);
});
</script>

{!! Form::close() !!}