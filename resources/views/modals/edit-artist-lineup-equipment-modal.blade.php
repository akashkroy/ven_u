<div id="edit-artist-lineup-equipment-modal" class="modal">
	<div class="modal-content">

		<div class="ourscene-modal-title-1">Edit artist lineup equipment</div>

		{!! Form::open(array(
			'id'			=> 'edit-artist-lineup-equipment-form',
			'url'			=> action('EventController@postEditArtistLineupEquipment'),
			'method'		=> 'POST'
		)) !!}

		<!-- Equipments -->

		<div class="section-title">Equipment</div>

		<div id="equipments" class="section">

		<div class="row">
			<div class="col s12 m8 l6">
				<table id="add-equipments-table"></table>
			</div>
		</div>

			<!-- Add equipment -->

		@if(count($all_equipments) > 0)

			<br/><br/>

			<div class="row">
				<div class="col s10 m8 l6">
					<select type="dropdown" id="add-house-equipment-select">
					@foreach($all_equipments as $equipment)
						<option value="{{ $equipment->_id }}">{{$equipment->name}}</option>
					@endforeach
					</select>
				</div>
				<div class="col s2 m4 l6">
					<a onClick="EALEM_addHouseEquipment(true)"><img src="{{ asset('images/icons/approved.svg') }}" class="add-icon"/></a>
				</div>
			</div>
		@else
			You have no equipment.
		@endif
		</div>

		<select type="dropdown" id="add-house-equipment-with-trashed-select" class="hide">
		@foreach($all_equipments_with_trashed as $equipment)
			<option value="{{ $equipment->_id }}">{{$equipment->name}}</option>
		@endforeach
		</select>

		<input type="hidden" name="service_id" />
		
		{!! Form::close() !!}

	</div>

	<div class="modal-footer">
		<a class="modal-action modal-close btn ourscene-btn-plain-1">Cancel</a>
		<a class="modal-action btn ourscene-btn-plain-1" style="margin-right: 10px;" onClick="$('<input type=\'submit\'>').hide().appendTo('#edit-artist-lineup-equipment-form').click().remove();">SUBMIT</a>
	</div>

	<!-- Hidden house equipments contents -->

	@foreach($all_equipments_with_trashed as $equipment)
		<div id="house-equipment-with-trashed-info-{{ $equipment->_id }}" class="hide">
			<b>{{ $equipment->name }}</b>
			@if(count($equipment->inclusion))
				<br/><br/>
				@foreach($equipment->inclusion as $inclusion)
					<p>{{ $inclusion }}</p>
				@endforeach
			@endif
		</div>
	@endforeach

</div>

<script>
	var remove_icon_src = "{{ asset('images/icons/delete.svg') }}";
</script>
<script src="{{ asset('js/edit-artist-lineup-equipment-modal.js') }}"></script>
