<!-- Add house equipments modal -->

<div id="add-house-equipments-modal" class="modal">
	<div class="modal-content">

		<div class="ourscene-modal-title-1">Add house equipment</div>

		@if(count($all_equipments) > 0)
		<select type="dropdown" id="add-house-equipment-select">
			@foreach($all_equipments as $equipment)
				<option value="{{ $equipment->_id }}">
					{{$equipment->name}}
				</option>
			@endforeach
		</select>

		@else
			You have no equipment.
		@endif
	</div>

	<div class="modal-footer">
		<a class="modal-action modal-close btn ourscene-btn-3">Cancel</a>
	@if(count($all_equipments))
		<a class="modal-action modal-close btn" style="margin-right: 10px;" onClick="addHouseEquipment()">Add</a>
	@endif

	</div>

	<!-- Hidden house equipments contents -->

	@foreach($all_equipments as $equipment)
		<div id="house-equipment-info-{{ $equipment->_id }}">
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
