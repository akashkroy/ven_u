<div id="invite-artist-modal" class="modal">
	<div class="modal-content">

		<div class="ourscene-modal-title-1">Invite artist</div>
		
		<div id="error-no-selected-invited-artist" class="error-field" style="display: none;">
			There is no selected artist.
		</div>
		<br/>

		{!! Form::open(array(
			'id'			=> 'invite-artist-form',
		)) !!}

		<div class="input-field col s12 m8 l4">
		@if(Session::get('user_type') == 'venue')
			<input type="text" name="artist_name" placeholder="" required autocomplete="off">
			<input type="hidden" name="artist_id" id="artist-id" value=""/>
		@else	
			<input type="text" name="artist_name" placeholder="" value="{{ Session::get('name') }}" readonly required autocomplete="off">
			<input type="hidden" name="artist_id" id="artist-id" value="{{ Session::get('id') }}">
		@endif
			<label for="artist-name-autocomplete" class="active">
				Name
				<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
			</label>
			<div>
				<ul class="artist-name-autocomplete-dropdown dropdown-content autocomplete" style="top:35px;">
				</ul>
			</div>
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
		<a class="modal-action btn" style="margin-right: 10px;" onClick="$('<input type=\'submit\'>').hide().appendTo('#invite-artist-form').click().remove();">Add artist</a>
	</div>
</div>