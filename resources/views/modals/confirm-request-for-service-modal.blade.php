<div id="confirm-request-for-service-modal" class="modal confirm-with-link-modal">

	{!! Form::open(array(
		'id'			=> 'confirm-request-for-service-modal-form',
		'url'			=> '',
		'action'		=> 'POST'
	)) !!}

	<div class="modal-content">
		Are you sure you want to <span class='bold-weight confirm-color'>accept</span> this request for service?

		<br/><br/><br/>

		<!-- Equipments -->

		<div class="section-title">Equipment</div>

		<div id="equipments" class="section">

		<div class="row">
			<div class="col s12 m8 l6">
				<table id="add-equipments-table">
				@foreach($default_equipments as $equipment)
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
							<a onclick="RFSM_removeHouseEquipment(this)">
								<img class="remove-icon" src="{{ asset('images/icons/delete.svg') }}"/>
							</a>
							<input type="hidden" name="equipments[]" value="{{ $equipment->_id }}">
						</td>
					</tr>
				@endforeach
				</table>
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
					<a onClick="RFSM_addHouseEquipment()"><img src="{{ asset('images/icons/approved.svg') }}" class="add-icon"/></a>
				</div>
			</div>
		@else
			You have no equipment.
		@endif
		</div>

	</div>

	{!! Form::close() !!}

	<div class="modal-footer">
		<a class="modal-action modal-close btn ourscene-btn-plain-1">Cancel</a>
		<a class="modal-action btn ourscene-btn-plain-1" style="margin-right: 10px;" onClick="$('<input type=\'submit\'>').hide().appendTo('#confirm-request-for-service-modal-form').click().remove();">ACCEPT</a>
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

<script>
	var remove_icon_src = "{{ asset('images/icons/delete.svg') }}";
</script>
<script src="{{ asset('js/confirm-request-for-service-modal.js') }}"></script>
