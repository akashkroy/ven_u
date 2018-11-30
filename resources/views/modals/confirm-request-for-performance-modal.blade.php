<div id="confirm-request-for-performance-modal" class="modal confirm-with-link-modal">	
	
	{!! Form::open(array(
		'id'			=> 'confirm-request-for-performance-modal-form',
		'url'			=> '',
		'action'		=> 'POST'
	)) !!}

	<div class="modal-content">
		Are you sure you want to <span class='bold-weight confirm-color'>accept</span> this request for performance?

		<br/><br/><br/>

		<div class="row">
			<div class="input-field col s12 m8 l4">
				<input type="text" class="ourscene-date" readonly="readonly" name="start_date" placeholder="" value="" required>
				<label class="active">
					Start date
					<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
				</label>
			</div>
			<div class="input-field col s12 m8 l4">
				<input type="text" class="time-picki-picker" name="start_time" placeholder="" value="" required>
				<label class="active">
					Start time
					<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
				</label>
			</div>
		</div>
		<div class="row">
			<div class="input-field col s12 m8 l4">
				<input type="text" name="end_date" class="ourscene-date" readonly="readonly" placeholder="" value="" required>
				<label class="active">
					End date
					<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
				</label>
			</div>
			<div class="input-field col s12 m8 l4">
				<input type="text" class="time-picki-picker" name="end_time" placeholder="" value="" required>
				<label class="active">
					End time
					<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
				</label>
			</div>

		</div>
	</div>

	<div class="modal-footer">
		<input type="submit" class="modal-action btn ourscene-btn-plain-1" value="Book">	
		<a class="modal-action modal-close btn ourscene-btn-plain-1">Cancel</a>
	</div>

	{!! Form::close() !!}

</div>