var $change_status_of_service_modal;

$(document).ready(function() {

	$change_status_of_service_modal = $('#change-status-of-service-modal');

});

function showCancelRequestForPerformanceModal(service_id){

	//get modal elements

	$yes_link = $change_status_of_service_modal.find('.yes-link');
	$optional_content = $change_status_of_service_modal.find('.optional-content');

	//update modal elements

	$yes_link.attr("href", ROOT+'/service/request-for-performance/cancel/'+service_id)
	$optional_content.html("Are you sure you want to <span class='bold-weight reject-color'>cancel</span> this request for performance?");

	//open modal
	$change_status_of_service_modal.openModal();
}

function showDeleteServiceModal(service_id){

	//get modal elements

	$yes_link = $change_status_of_service_modal.find('.yes-link');
	$optional_content = $change_status_of_service_modal.find('.optional-content');

	//update modal elements

	$yes_link.attr("href", ROOT+'/service/delete/'+service_id)
	$optional_content.html("Are you sure you want to <span class='bold-weight reject-color'>delete</span> this request?");

	//open modal
	$change_status_of_service_modal.openModal();
}