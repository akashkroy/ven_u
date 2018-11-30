function RFSM_addHouseEquipment(){
	
	var $modal = $("#confirm-request-for-service-modal");

	var equipment_id = $modal.find("#add-house-equipment-select").val();
	
	var $add_equipments_table = $modal.find('#add-equipments-table');
	
	var equipment_info = $modal.find('#house-equipment-info-'+equipment_id).html();

	var row = '\
		<tr class="equipment-row"> \
			<td> \
				'+equipment_info+' \
			</td> \
			<td class="right-align"> \
				<a onclick="RFSM_removeHouseEquipment(this)"> \
					<img class="remove-icon" src="'+remove_icon_src+'"/> \
				</a> \
				<input type="hidden" name="equipments[]" value="'+equipment_id+'"> \
			</td> \
		</tr> \
	';

	$add_equipments_table.append(row);
}

function RFSM_removeHouseEquipment(this_ref){
	$(this_ref).closest('.equipment-row').remove();	
}