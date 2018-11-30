<!-- View Event Modal -->

<div class="modal fade" id="modal-viewevent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"></h4>
			</div>
			<div class="modal-body gray-border-bottom">

			<h4>Age Requirements</h4>
			<p id="view_age_restrictions"></p>

			<br/>

			<h4>Event Type</h4>
			<p id="view_event_type"></p>			

			<br/>

			<h4>Duration</h4>
			
			<h5>From</h5>
			<p id="view_event_start_date"></p>
			<p id="view_event_start_time"></p>
			
			<h5>To</h5>
			<p id="view_event_end_date"></p>
			<p id="view_event_end_time"></p>

			<br/>

			<h4>Opening</h4>
			<p id="view_event_opening_time"></p>

			<br/>

			<h4>Description</h4>
			<p id="view_event_description"></p>

			<br/>

			<h4>Venue</h4>
			<p id="view_event_venue"></p>

			<hr>

			{!! Form::open(array(
						'id' 			=>	'update-cover-charge-form'
				))	
			!!}

			<h4>Cover charge</h4>
			<textarea type="text" id="view_event_cover_charge" name="cover_charge" disabled="true" form="update-cover-charge-form"></textarea>
			<input type="hidden" name="event_id" id="cover-charge-event-id" value=""></input>
			<button type='submit' id="update-cover-charge-button" class="btn btn-primary" style="display: none">Update cover charge</button>

			{!!Form::close()!!}

			<hr>

			<h4>Services</h4>
			<div id="lineup">
				<table>
					<tbody id="view-event-service-table-body">
						<tr>
							<th>Artist Name</th>
							<th>Time</th>
							<th></th>
						</tr>
					</tbody>
				</table>
			</div>

			<hr>

			<h4>Equipment</h4>
			<div id="view-equipment-list">
				<table>
					<tbody id="equipment-table-body">
						<tr>
							<th>Name</th>
							<th>Owner</th>
							<th>Quantity</th>
							<th>Inclusions</th>
							<th>Options</th>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="modal-body gray-border-bottom" id="viewevent-option">

			@if (isset($is_artist))
				@if ($is_artist)
					<button id="request_performance_btn" style="display: block">Request Performance</button>
				@else
					<button id="request_performance_btn" style="display: none">Request Performance</button>
				@endif
			@endif
				
			</div>

			{!! Form::open(array(
						'id' 			=>	'update-event-form',
						'url'			=>	action('EventController@renderUpdateEventForm'),
						'method'		=> 'POST',

				))	!!}

			{!!Form::close()!!}

			{!! Form::open(array(
						'id' 			=>	'pay-event-form	',
						'url'			=>	action('EventController@renderUpdateEventForm'),
						'method'		=> 'POST',

				))	!!}

			{!!Form::close()!!}

			</div>
		</div>					
	</div>
</div>

@include('modals.serviceequipment')
@include('modals.performance')
@include('modals.alert')
@include('modals.confirmation')