<div id="edit-invited-artist-modal" class="modal">
	<div class="modal-content">

		<div class="ourscene-modal-title-1">Edit invited artist</div>

		<br/>

		{!! Form::open(array(
			'id'			=> 'edit-invited-artist-form',
		)) !!}

		<input type="hidden" name="service_id"/>

		<div class="input-field col s12 m8 l4">
			<input type="text" name="artist_name" placeholder="" required>
			<input type="hidden" name="artist_id" value=""/>
			<label for="" class="active">
				Name
				<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
			</label>
		</div>

		<div class="input-field col s12 m8 l4">
			<label class="active">Start date <span class="required-color">*</span></label>
		</div>

		<div class="input-field col s12 m8 l4">
			<input type="text" class="ourscene-date" readonly="readonly" name="start_date" required></input>
		</div>

		<div class="input-field col s12 m8 l4">
			<input type="text" class="time-picki-picker" name="start_time" required></input></br>
			<label for="" class="active">
				Start time
				<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
			</label>
		</div>

		<div class="input-field col s12 m8 l4">
			<label class="active">End date <span class="required-color">*</span></label>
		</div>

		<div class="input-field col s12 m8 l4">
			<input type="text" class="ourscene-date" readonly="readonly" name="end_date" required></input>
		</div>

		<div class="input-field col s12 m8 l4">
			<input type="text" class="time-picki-picker" name="end_time" required></input></br>
			<label for="" class="active">
				End time
				<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
			</label>
		</div>
		
		<div class="input-field col s12 m8 l4" style="display: none;">
			<input type="number" name="price" min="0.00" step="0.01" value="0.00" required></input>
			<label for="" class="active">
				Price
				<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
			</label>
		</div>

		{!! Form::close() !!}
	</div>

	<div class="modal-footer">
		<a class="modal-action modal-close btn ourscene-btn-3">Cancel</a>
		<a class="modal-action btn" style="margin-right: 10px;" onClick="$('<input type=\'submit\'>').hide().appendTo('#edit-invited-artist-form').click().remove();">Update</a>
	</div>
</div>