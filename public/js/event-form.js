$(document).ready(function() {

	// Choose between create regular event or create promotion

	var $regular_event_form_container = $('#regular-event-form-container');
	var $promotion_form_container = $('#promotion-form-container');


	$('input[type=radio][name=event_form_type]').change(function() {

		if (this.value == 'regular_event') {
			$promotion_form_container.hide();
			$regular_event_form_container.show();
		}
		else{
			$regular_event_form_container.hide();
			$promotion_form_container.show();
		}
	});

	// Enable/disable other event type

    var $other_type = $('#other-type');

	$('#regular-event-form input[type=radio][name=type]').change(function() {
		
		if (this.value == 'other') {
			$other_type.attr('disabled', false);
		}
		else{
			$other_type.val('');
			$other_type.attr('disabled', true);
		}
	});

	// Enable/disable other promotion type

    var $promotion_other_type = $('#promotion-other-type');

	$('#promotion-form input[type=radio][name=type]').change(function() {
		
		if (this.value == 'other') {
			$promotion_other_type.attr('disabled', false);
		}
		else{
			$promotion_other_type.val('');
			$promotion_other_type.attr('disabled', true);
		}
	});

	// Invite artist

	$('#invite-artist-form').submit(function(e){
		
		e.preventDefault();

		var $modal = $('#invite-artist-modal');
		var has_error = false;

		//add artist to request of service table and list
		
		var artist_id = $modal.find('input[name=artist_id]').val();
		var artist_name = $modal.find('input[name=artist_name]').val();
		var start_date = $modal.find('input[name=start_date]').val();
		var start_time = $modal.find('input[name=start_time]').val();
		var end_date = $modal.find('input[name=end_date]').val();
		var end_time = $modal.find('input[name=end_time]').val();
		var price = $modal.find('input[name=price]').val();
		
		//check if invited artist is selected
		if(!artist_id){
			$modal.find('#error-no-selected-invited-artist').show();
			has_error = true;
		}

		if(!has_error){

			addArtist(idx_invite_artist++, artist_id, artist_name, start_date, start_time, end_date, end_time, price);

			//close invite artist modal
			$modal.closeModal();
		}
	});

	// Edit invite artist

	$('#edit-invite-artist-form').submit(function(e){
		
		e.preventDefault();

		var $modal = $('#edit-invite-artist-modal');
		var $table = $('#invite-artists-table');

		var has_error = false;

		//add artist to request of service table and list
		
		var artist_id = $modal.find('input[name=artist_id]').val();
		var artist_name = $modal.find('input[name=artist_name]').val();
		var start_date = $modal.find('input[name=start_date]').val();
		var start_time = $modal.find('input[name=start_time]').val();
		var end_date = $modal.find('input[name=end_date]').val();
		var end_time = $modal.find('input[name=end_time]').val();
		
		var idx = $modal.find('input[name=edit_id]').val();
		
		//check if invited artist is selected
		if(!artist_id){
			$modal.find('#error-no-selected-invited-artist').show();
			has_error = true;
		}
		
		if(!has_error){
			
			//update artist to invite artist list
	
			invite_artist = {
				"artist": {
					"id": artist_id,
					"name": artist_name
				},
				"performance_time": {
					"start_date": start_date,
					"start_time": start_time,
					"end_date": end_date,
					"end_time": end_time,
				}
			}

			invite_artists[idx] = invite_artist;

			//update table
			var $entry = $table.find('.entry-'+idx);
			
			$entry.find('.artist-name').html(artist_name);
			$entry.find('.start-datetime').html(moment(start_date+' '+start_time).format('DD MMMM Y hh:mm A'));
			$entry.find('.end-datetime').html(moment(end_date+' '+end_time).format('DD MMMM Y hh:mm A'));

			//close edit invite artist modal
			$modal.closeModal();
		}

		console.log(invite_artists);
	});

	// Edit artist lineup

	$('#edit-artist-lineup-form').submit(function(e){
		
		e.preventDefault();

		var $form = $(this);
		var $modal = $('#edit-artist-lineup-modal');
		var $table = $('#artist-lineup-table');

		//get inputs

		var service_id = $form.find('input[name=service_id]').val();
		var start_date = $form.find('input[name=start_date]').val();
		var start_time = $form.find('input[name=start_time]').val();
		var end_date = $form.find('input[name=end_date]').val();
		var end_time = $form.find('input[name=end_time]').val();

		//update invited artists list

		artist_lineup[service_id]['performance_time']['start_date'] = start_date;
		artist_lineup[service_id]['performance_time']['start_time'] = start_time;
		artist_lineup[service_id]['performance_time']['end_date'] = end_date;
		artist_lineup[service_id]['performance_time']['end_time'] = end_time;

		//update table
		var $entry = $table.find('.entry-'+service_id);

		$entry.find('.start-datetime').html(moment(start_date+' '+start_time).format('DD MMMM Y hh:mm A'));
		$entry.find('.end-datetime').html(moment(end_date+' '+end_time).format('DD MMMM Y hh:mm A'));

		//close modal
		$modal.closeModal();
	});

	// Edit invited artist

	$('#edit-invited-artist-form').submit(function(e){
		
		e.preventDefault();

		var $form = $(this);
		var $modal = $('#edit-invited-artist-modal');
		var $table = $('#invited-artists-table');

		//get inputs

		var service_id = $form.find('input[name=service_id]').val();
		var start_date = $form.find('input[name=start_date]').val();
		var start_time = $form.find('input[name=start_time]').val();
		var end_date = $form.find('input[name=end_date]').val();
		var end_time = $form.find('input[name=end_time]').val();

		//update invited artists list

		invited_artists[service_id]['performance_time']['start_date'] = start_date;
		invited_artists[service_id]['performance_time']['start_time'] = start_time;
		invited_artists[service_id]['performance_time']['end_date'] = end_date;
		invited_artists[service_id]['performance_time']['end_time'] = end_time;

		//update table
		var $entry = $table.find('.entry-'+service_id);

		$entry.find('.start-datetime').html(moment(start_date+' '+start_time).format('DD MMMM Y hh:mm A'));
		$entry.find('.end-datetime').html(moment(end_date+' '+end_time).format('DD MMMM Y hh:mm A'));

		//close modal
		$modal.closeModal();
	});

	/* Validate forms */

	// Regular event form

	var regular_event_form_validated = false;

	$('#regular-event-form').submit(function(e){

		if(regular_event_form_validated)
			return;

		e.preventDefault();

		var $form = $(this);
		var $submit_btn = $form.find('#submit-btn');
		var has_error = false;

		//disable submit button
		$submit_btn.prop('disabled', true);

		//hide errors
		$form.find('.error-field').hide();

		//get form inputs
		var start_date = $('#start-date').val();
		var start_time = $('#start-time').val();
		var end_date = $('#end-date').val();
		var end_time = $('#end-time').val();
		var opening_time = $('#opening-time').val();

		var start_datetime = createDatetime(start_date, start_time);
		var end_datetime = createDatetime(end_date, end_time);
		var opening_datetime = createDatetime(start_date, opening_time);

		if(USER_TYPE == 'venue'){
			
			//check invite artists

			if(FORM_ACTION== 'add'){
				/*if(Object.keys(invite_artists).length == 0){
					$form.find('#error-no-artists').show();
					has_error = true;
				}*/
			}
			
			for(var i in invite_artists){

				var performance_datetime = invite_artists[i]['performance_time'];
				var performance_start_datetime = createDatetime(performance_datetime['start_date'], performance_datetime['start_time']);
				var performance_end_datetime = createDatetime(performance_datetime['end_date'], performance_datetime['end_time']);

				console.log(performance_end_datetime);
				console.log(performance_start_datetime);
				if(performance_start_datetime.getTime() == performance_end_datetime.getTime()){
					console.log('duration!!!');
					$form.find('#invite-artists-performance-should-have-duration').show();
					has_error = true;
					break;
				}

				if(! datetimeInRange(start_datetime, end_datetime, performance_start_datetime)
				|| ! datetimeInRange(start_datetime, end_datetime, performance_end_datetime)){
					$form.find('#invite-artists-performance-outside-event').show();
					has_error = true;
					break;
				}
			}

			if(FORM_ACTION=='edit'){
				
				//check invited artist

				for(var i in invited_artists){

					var performance_datetime = invited_artists[i]['performance_time'];
					var performance_start_datetime = createDatetime(performance_datetime['start_date'], performance_datetime['start_time']);
					var performance_end_datetime = createDatetime(performance_datetime['end_date'], performance_datetime['end_time']);

					if(! datetimeInRange(start_datetime, end_datetime, performance_start_datetime)
					|| ! datetimeInRange(start_datetime, end_datetime, performance_end_datetime)){
						$form.find('#invited-artists-performance-outside-event').show();
						has_error = true;
						break;
					}
				}

				//check artist lineup

				for(var i in artist_lineup){

					var performance_datetime = artist_lineup[i]['performance_time'];
					var performance_start_datetime = createDatetime(performance_datetime['start_date'], performance_datetime['start_time']);
					var performance_end_datetime = createDatetime(performance_datetime['end_date'], performance_datetime['end_time']);

					if(! datetimeInRange(start_datetime, end_datetime, performance_start_datetime)
					|| ! datetimeInRange(start_datetime, end_datetime, performance_end_datetime)){
						$form.find('#artist-lineup-performance-outside-event').show();
						has_error = true;
						break;
					}
				}

			}
		}

		if(USER_TYPE == 'artist'){
			var venue_id = $form.find('#venue-id').val();

			if(! venue_id){
				$form.find('#error-no-venue-selected').show();
				has_error = true;	
			}

		}

		//check start datetime and end datetime

		if(datetimeGreaterThan(start_datetime, end_datetime)){
			$form.find('#error-start-date-more-than-end-date').show();
			has_error = true;
		}

		//check opening datetime

		// if(datetimeGreaterThan(opening_datetime, start_datetime)){
		// 	$form.find('#error-opening-date-more-than-start-date').show();
		// 	has_error = true;
		// }

		if(!has_error){
			
			//get and append invited artists to form

			var $hidden_artists = $("<input type='hidden' name='artists'/>");
			$hidden_artists.val(JSON.stringify(invite_artists));

			$(this).append($hidden_artists);

			if(FORM_ACTION=='edit'){
				
				var $hidden_invited_artists = $("<input type='hidden' name='invited_artists'/>");
				$hidden_invited_artists.val(JSON.stringify(invited_artists));

				var $hidden_artist_lineup = $("<input type='hidden' name='artist_lineup'/>");
				$hidden_artist_lineup.val(JSON.stringify(artist_lineup));
				
				var $hidden_delete_invited_artist_ids = $("<input type='hidden' name='delete_invited_artist_ids'/>");
				$hidden_delete_invited_artist_ids.val(JSON.stringify(delete_invited_artist_ids));

				var $hidden_delete_artist_lineup_ids = $("<input type='hidden' name='delete_artist_lineup_ids'/>");
				$hidden_delete_artist_lineup_ids.val(JSON.stringify(delete_artist_lineup_ids));

				$(this).append($hidden_invited_artists);
				$(this).append($hidden_artist_lineup);
				$(this).append($hidden_delete_invited_artist_ids);
				$(this).append($hidden_delete_artist_lineup_ids);
			}

			//submit form
			regular_event_form_validated = true;
			$(this).submit();
		}
		else{
			//show error
			$form.find('#error-in-regular-event-form').show();

			//enable submit button
			$submit_btn.prop('disabled', false);

			//scroll to top
			$('html, body').animate({scrollTop : 0},800);
		}

	});

	// Promotion form

	var promotion_form_validated = false;

	$('#promotion-form').submit(function(e){

		if(promotion_form_validated)
			return;

		e.preventDefault();

		var $form = $(this);
		var $submit_btn = $form.find('#submit-btn');
		var has_error = false;

		//disable submit button
		$submit_btn.prop('disabled', true);

		//hide errors
		$form.find('.error-field').hide();

		//get form inputs
		var start_date = $form.find('#promotion-start-date').val();
		var start_time = $form.find('#promotion-start-time').val();
		var end_date = $form.find('#promotion-end-date').val();
		var end_time = $form.find('#promotion-end-time').val();

		var start_datetime = createDatetime(start_date, start_time);
		var end_datetime = createDatetime(end_date, end_time);

		//check start datetime and end datetime

		if(datetimeGreaterThan(start_datetime, end_datetime)){
			$form.find('#error-start-date-more-than-end-date').show();
			has_error = true;
		}

		if(!has_error){

			//submit form
			promotion_form_validated = true;
			$(this).submit();
		}
		else{
			//show error
			$form.find('#error-in-promotion-form').show();

			//enable submit button
			$submit_btn.prop('disabled', false);

			//scroll to top
			$('html, body').animate({scrollTop : 0},800);
		}

	});

});

function addHouseEquipment(){
	
	var equipment_id = $("#add-house-equipment-select").val();
	
	var $add_equipments_table = $('#add-equipments-table');
	
	var equipment_info = $('#house-equipment-info-'+equipment_id).html();

	var row = '\
		<tr class="equipment-row"> \
			<td> \
				'+equipment_info+' \
			</td> \
			<td class="right-align"> \
				<a onclick="removeHouseEquipment(this)"> \
					<img class="remove-icon" src="'+remove_icon_src+'"/> \
				</a> \
				<input type="hidden" name="equipments[]" value="'+equipment_id+'"> \
			</td> \
		</tr> \
	';

	$add_equipments_table.append(row);
}

function removeHouseEquipment(this_ref){
	$(this_ref).closest('.equipment-row').remove();	
}

function showInviteArtistModal(){

	$modal = $('#invite-artist-modal');
	$form = $modal.find('#invite-artist-form');
	//hide errors
	$modal.find('.error-field').hide();

	//enable all inputs
	$modal.find('input').prop('disabled', false);

	//clear invite artist form fields
	$form[0].reset();
	$form.find('#artist-id').val('');


	$modal.openModal();
}

function addArtist(idx, artist_id, artist_name, start_date, start_time, end_date, end_time, price){

	//add artist to request of service list

	//add artist to artists table

	var $invite_artists_table = $('#invite-artists-table');

	var row = '\
		<tr class="artist-row entry-'+idx+'"> \
			<td> \
				<div class="row"> \
					<div class="col s12 m6 l6"> \
						<div class="ourscene-label-1">Name</div> \
						<span class="artist-name">'+artist_name+'</span><br/><br/> \
					</div> \
					<div class="col s12 m6 l6"> \
						<div class="ourscene-label-1">Performance Time</div> <br/> \
						<b>Start</b><br/><span class="start-datetime">'+moment(start_date+' '+start_time).format('D MMMM Y hh:mm A')+'</span><br/> \
						<b>End</b><br/><span class="end-datetime">'+moment(end_date+' '+end_time).format('D MMMM Y hh:mm A')+'</span><br/> \
					</div> \
				</div> \
			</td> \
			<td class="right-align"> \
				<a onclick="showEditInviteArtistModal(this, \''+idx+'\')"> \
					<img class="remove-icon" src="'+edit_icon_src+'"/> \
				</a> \
				<a onclick="removeArtist(this, '+idx+')"> \
					<img class="remove-icon" src="'+remove_icon_src+'"/> \
				</a> \
			</td> \
		</tr> \
	';

	$invite_artists_table.append(row);

	//add artist to invited artist list
	
	invite_artist = {
		"artist": {
			"id": artist_id,
			"name": artist_name
		},
		"performance_time": {
			"start_date": start_date,
			"start_time": start_time,
			"end_date": end_date,
			"end_time": end_time,
		}
	}

	invite_artists.push(invite_artist);
}

function removeArtist(this_ref, idx){
	
	//remove artist from invited artist list
	delete invite_artists[idx];

	//remove artist from artists table
	$(this_ref).closest('.artist-row').remove();
}

function showEditInviteArtistModal(this_ref, idx){
	
	$modal = $('#edit-invite-artist-modal');
	$form = $modal.find('#edit-invite-artist-form');
	
	//hide errors
	$modal.find('.error-field').hide();

	//enable all inputs
	$modal.find('input').prop('disabled', false);

	//disable some inputs
	$modal.find('input[name=artist_name]').prop("disabled", true);

	//update input field values
	
	var artist = invite_artists[idx]['artist'];
	var performance_time = invite_artists[idx]['performance_time'];
	
	$modal.find('input[name=edit_id]').val(idx);
	
	$modal.find('input[name=artist_name]').val(artist['name']);
	$modal.find('input[name=artist_id]').val(artist['id']);
	
	$modal.find('input[name=start_date]').val(performance_time['start_date']);
	$modal.find('input[name=start_time]').val(performance_time['start_time']);
	$modal.find('input[name=end_date]').val(performance_time['end_date']);
	$modal.find('input[name=end_time]').val(performance_time['end_time']);
	
	//show modal
	$modal.openModal();
}

/* Invited artists */

function showEditInvitedArtistModal(this_ref, idx){

	$modal = $('#edit-invited-artist-modal');
	$form = $modal.find('#edit-invited-artist-form');
	//hide errors
	$modal.find('.error-field').hide();

	//enable all inputs
	$modal.find('input').prop('disabled', false);

	//disable some inputs
	$modal.find('input[name=artist_name]').prop("disabled", true);

	//update input field values
	
	var artist = invited_artists[idx]['artist'];		
	var performance_time = invited_artists[idx]['performance_time'];		

	$modal.find('input[name=service_id]').val(idx);

	$modal.find('input[name=artist_name]').val(artist['name']);
	
	$modal.find('input[name=start_date]').val(performance_time['start_date']);
	$modal.find('input[name=start_time]').val(performance_time['start_time']);
	$modal.find('input[name=end_date]').val(performance_time['end_date']);
	$modal.find('input[name=end_time]').val(performance_time['end_time']);

	//show modal
	$modal.openModal();
}

function removeInvitedArtist(this_ref, idx){
	
	//remove artist from invited artist list
	delete_invited_artist_ids.push(idx);
	
	//remove artist from artists table
	$(this_ref).closest('.artist-row').remove();
}

/* Artist lineup */

function showEditArtistLineupModal(this_ref, idx){

	$modal = $('#edit-artist-lineup-modal');
	$form = $modal.find('#edit-artist-lineup-form');
	
	//hide errors
	$modal.find('.error-field').hide();

	//enable all inputs
	$modal.find('input').prop('disabled', false);

	//disable some inputs
	$modal.find('input[name=artist_name]').prop("disabled", true);
	
	//update input field values
	
	var artist = artist_lineup[idx]['artist'];		
	var performance_time = artist_lineup[idx]['performance_time'];		

	$modal.find('input[name=service_id]').val(idx);

	$modal.find('input[name=artist_name]').val(artist['name']);

	$modal.find('input[name=start_date]').val(performance_time['start_date']);
	$modal.find('input[name=start_time]').val(performance_time['start_time']);
	$modal.find('input[name=end_date]').val(performance_time['end_date']);
	$modal.find('input[name=end_time]').val(performance_time['end_time']);

	//show modal
	$modal.openModal();
}

function removeArtistLineup(this_ref, idx){
	
	//remove artist from invited artist list
	delete_artist_lineup_ids.push(idx);
	
	//remove artist from artists table
	$(this_ref).closest('.artist-row').remove();
}