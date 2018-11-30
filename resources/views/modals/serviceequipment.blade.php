<?php 
	use OurScene\Models\Equipment;

	$all_equipment = Equipment::user(Session::get('id'))->get();
	$default_equipment = Equipment::isDefault()->user(Session::get('id'))->get();

?>
<div class="modal fade" id="modal-service-equipment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Insert a new equipment</h4>
			</div>
			<div class="modal-body gray-border-bottom">
					@if(count($all_equipment) > 0)
					<select type="dropdown" id="service-equipment-selector">
						@foreach($all_equipment as $equipment)
							<option value="{{ $equipment->_id }}">
							<p>{{$equipment->name}} -</p>
							@foreach($equipment->inclusion as $inclusion)
								<p>{{$inclusion}}</p>
							@endforeach
							</option>
						@endforeach
					</select>
					<button type="button" class="btn" id="add-service-equipment-button">Include</button>
					@else
					<select type="dropdown" disabled="true">
							<option>There are you have no registered equipment</option>
					</select>
					@endif

					{!! Form::open(array(
							'id'			=> 'add-equipments-form',
					)) !!}				
					<div id="service-equipment-list">
					<input id="service-equipment-list-event-id" type="hidden" name="event_id" value="">
					<table>
						<tbody id="service-equipment-table-body">
							<tr>
								<th>Name</th>
								<th>Owner</th>
								<th>Quantity</th>
								<th>Inclusions</th>
								<th>Options</th>
							</tr>
							@foreach($default_equipment as $equipment)
							<tr class="service-equipment-row">
								<td>{{$equipment->name}}</td>
								<td>{{$equipment->owner}}</td>
								<td>{{$equipment->qty}}</td>
								<td>
								@foreach($equipment->inclusion as $inclusion)
									{{$inclusion}}
								@endforeach		
								</td>						
								<td>
									<button type="button" onclick="removeNewServiceEquipment(this)">Remove</button><input type="hidden" name="equipments[]" value="{{$equipment->_id}}">
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<button type="submit">Add</button>
				{!! Form::close() !!}

				</div>
			</div>
		</div>					
	</div>
</div>