$(document).ready(function() {

	/* Validate forms */

	// Edit artist lineup equipment form

	var edit_artist_lineup_equipment_form = true;

	$('#edit-artist-lineup-equipment-form').submit(function(e){
		
		if(edit_artist_lineup_equipment_form)
			return;

	});
});

function EALEM_addHouseEquipment(user_select){
	
	var $modal = $("#edit-artist-lineup-equipment-modal");

	if(user_select)
		var equipment_id = $modal.find("#add-house-equipment-select").val();
	else
		var equipment_id = $modal.find("#add-house-equipment-with-trashed-select").val();

	var $add_equipments_table = $modal.find('#add-equipments-table');
	
	var equipment_info = $modal.find('#house-equipment-with-trashed-info-'+equipment_id).html();

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

function EALEM_removeHouseEquipment(this_ref){
	$(this_ref).closest('.equipment-row').remove();	
}

function showEditArtistLineupEquipmentModal(service_id, equipments){

	//get modal elements

	var $modal = $('#edit-artist-lineup-equipment-modal');
	var $form = $modal.find('#edit-artist-lineup-equipment-form');
	var $equipment_select = $form.find('#add-house-equipment-with-trashed-select');
	var $add_equipments_table = $modal.find('#add-equipments-table');

	//reset elements
	
	$add_equipments_table.html('');
	$form.find('input[name="service_id"]').val(service_id);
	//update modal elements

	equipments.forEach(function (equipment){
		
		//get equipment from list

		$equipment_in_select = $equipment_select.find('option[value="'+equipment['equipment_id']+'"]');

		//add equipment to modal
		if($equipment_in_select.length){
			$equipment_in_select.attr('selected', 'selected');
			console.log('added');
			EALEM_addHouseEquipment(false);
		}
	});

	//open modal
	$modal.openModal();
}