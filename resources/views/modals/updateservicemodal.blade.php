<!-- Update Service Modal -->

<div class="modal fade" id="modal-updateservice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Update Existing Service</h4>
			</div>
			<div class="modal-body gray-border-bottom">
				<div class="service">
				{!! Form::open(array(
					'id'			=> 'update-service-form'
				)) !!}
						<select type="dropdown" name="artist_id">
								@foreach ($artists as $artist)
									<option value="{{ $artist['_id']}}">{{ $artist['name'] }}</option>
								@endforeach
						</select></br>
						Start Date:
						<input type="text" class="ourscene-date" readonly="readonly" id="update-service-startdate"
						 	name="start_date" value="<?= $start_date; ?>" min="<?= $start_date; ?>" max="<?= $end_date; ?>"></input>
						Start Time:
						<input type="time" id="update-service-starttime" name="start_time" value=""></input></br>
						End Date:
						<input type="text" class="ourscene-date" readonly="readonly" id="update-service-enddate"
							 name="end_date" value="<?= $end_date; ?>" min="<?= $start_date; ?>" max="<?= $end_date; ?>"></input>
						End Time:
						<input type="time" id="update-service-endtime" name="end_time"></input></br>
						<span class="etime">Price: $</span>
						<input type="number" min="0.00" step="0.01" id="update-service-price" name="price" value="0.00"></input>
						</br>
						<input type="hidden" name="service_id" value="" id="update-service-id"></input>
						<input type="submit" value="Update">
				{!! Form::close() !!}
				</div>
			</div>
		</div>					
	</div>
</div>
@include('modals.alert')