$(document).ready(function() {

	/* Validate forms */

	// Request for performance form

	var request_for_performance_form_validated = false;

	$('#request-for-performance-form').submit(function(e){
		
		if(request_for_performance_form_validated)
			return;

		e.preventDefault();

		var $form = $(this);
		var $submit_btn = $form.find('#submit-btn');
		var has_error = false;

		//hide errors
		$form.find('.error-field').hide();

		//get form inputs
		var start_date = $form.find('input[name=start_date]').val();
		var start_time = $form.find('input[name=start_time]').val();
		var end_date = $form.find('input[name=end_date]').val();
		var end_time = $form.find('input[name=end_time]').val();

		var start_datetime = createDatetime(start_date, start_time);
		var end_datetime = createDatetime(end_date, end_time);

		var event_start_datetime = createDatetime(event_start_date, event_start_time);
		var event_end_datetime = createDatetime(event_end_date, event_end_time);

		//check start datetime and end datetime

		if(datetimeGreaterThan(start_datetime, end_datetime)){
			$form.find('#error-start-date-more-than-end-date').show();
			has_error = true;
		}

		if(! datetimeInRange(event_start_datetime, event_end_datetime, start_datetime)
			|| ! datetimeInRange(event_start_datetime, event_end_datetime, end_datetime)){
			$form.find('#error-performance-outside-event').show();
			has_error = true;
		}

		//check start datetime and opening datetime

		if(!has_error){
		
			//disable submit button
			$submit_btn.prop('disabled', true);

			//submit form
			request_for_performance_form_validated = true;
			$(this).submit();
		}
		else{
			//scroll to top
			$('#request-for-performance-modal').animate({scrollTop : 0},800);
		}

	});
});

function RFPM_addHouseEquipment(){
	
	var $modal = $("#request-for-performance-modal");

	var equipment_id = $modal.find("#add-house-equipment-select").val();
	
	var $add_equipments_table = $modal.find('#add-equipments-table');
	
	var equipment_info = $modal.find('#house-equipment-info-'+equipment_id).html();

	var row = '\
		<tr class="equipment-row"> \
			<td> \
				'+equipment_info+' \
			</td> \
			<td class="right-align"> \
				<a onclick="RFPM_removeHouseEquipment(this)"> \
					<img class="remove-icon" src="'+remove_icon_src+'"/> \
				</a> \
				<input type="hidden" name="equipments[]" value="'+equipment_id+'"> \
			</td> \
		</tr> \
	';

	$add_equipments_table.append(row);
}

function RFPM_removeHouseEquipment(this_ref){
	$(this_ref).closest('.equipment-row').remove();	
}