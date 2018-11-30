<div class="modal fade" id="modal-requestperformance" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Create New Service</h4>
			</div>
			<div class="modal-body gray-border-bottom">
				<div class="service">
				{!! Form::open(array(
					'id'			=> 'request-performance-form'
				)) !!}
						<span class="name">Name: </span>
						<input type="hidden" id="performance_event_id" name="event_id" value="">
						<select type="dropdown" name="artist_id">
							<option value="{{ Session::get('id')}}">{{ Session::get('name') }}</option>
						</select></br>

						Start Date:
						<input type="text" class="ourscene-date" readonly="readonly"
							 id="performance_start_date" name="start_date" value="" min="" max=""></input>
						<input type="time" id="performance_start_time" name="start_time" value="12:00"></input></br>
						End Date:
						<input type="text" class="ourscene-date" readonly="readonly"
							 id="performance_end_date" name="end_date" value="" min="" max=""></input>
						End Time:
						<input type="time" id="performance_end_time" name="end_time" value="12:00"></input></br>
						<span class="price">Price: </span>
						<input type="number" min="0.00" step="0.01" name="price" value="0.00"></input>
						</br>
						<input type="submit" value="Request"></input>
				{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
