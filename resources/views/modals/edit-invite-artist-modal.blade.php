<div id="edit-invite-artist-modal" class="modal">
	<div class="modal-content">

		<div class="ourscene-modal-title-1">Edit Invite artist</div>
		
		<div id="error-no-selected-invited-artist" class="error-field" style="display: none;">
			There is no selected artist.
		</div>
		<br/>

		{!! Form::open(array(
			'id'			=> 'edit-invite-artist-form',
		)) !!}

		<input type="hidden" name="edit_id"/>
		
		<div class="input-field col s12 m8 l4">
			<input type="text" id="edit-artist-name-autocomplete" name="artist_name" placeholder="" required>
			<input type="hidden" name="artist_id" id="artist-id" value=""/>
			<label for="edit-artist-name-autocomplete" class="active">
				Name
				<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
			</label>
		</div>

		<div class="input-field col s12 m8 l4">
			<label class="active">Start date <span class="required-color">*</span></label>
		</div>

		<div class="input-field col s12 m8 l4">
			<input type="text" class="ourscene-date" readonly="readonly" name="start_date" value="<?= $start_date; ?>" required></input>
		</div>

		<div class="input-field col s12 m8 l4">
			<input type="text" class="time-picki-picker" name="start_time" value="<?= $start_time; ?>" required></input></br>
			<label for="" class="active">
				Start time
				<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
			</label>
		</div>

		<div class="input-field col s12 m8 l4">
			<label class="active">End date <span class="required-color">*</span></label>
		</div>

		<div class="input-field col s12 m8 l4">
			<input type="text" class="ourscene-date" readonly="readonly" name="end_date" value="<?= $end_date; ?>" required></input>
		</div>

		<div class="input-field col s12 m8 l4">
			<input type="text" class="time-picki-picker" name="end_time" value="<?= $end_time; ?>" required></input></br>
			<label for="" class="active">
				End time
				<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
			</label>
		</div>

		{!! Form::close() !!}
	</div>

	<div class="modal-footer">
		<a class="modal-action modal-close btn ourscene-btn-3">Cancel</a>
		<a class="modal-action btn" style="margin-right: 10px;" onClick="$('<input type=\'submit\'>').hide().appendTo('#edit-invite-artist-form').click().remove();">Update</a>
	</div>
</div>