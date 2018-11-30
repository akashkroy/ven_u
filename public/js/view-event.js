var $confirm_change_status_of_service_modal;

var $confirm_request_for_performance_modal;

var $confirm_request_for_service_modal;

$(document).ready(function() {

	$confirm_change_status_of_service_modal = $('#confirm-change-status-of-service-modal');
	$confirm_request_for_performance_modal = $('#confirm-request-for-performance-modal');
	$confirm_request_for_service_modal = $('#confirm-request-for-service-modal');

});

function showConfirmRequestForPerformanceModal(service_id, start_date_val, start_time_val, end_date_val, end_time_val){

	//get modal elements

	var $modal = $confirm_request_for_performance_modal;

	$form = $modal.find('#confirm-request-for-performance-modal-form');
	$start_date = $modal.find('input[name=start_date]');
	$start_time = $modal.find('input[name=start_time]');
	$end_date = $modal.find('input[name=end_date]');
	$end_time = $modal.find('input[name=end_time]');

	//update modal elements

	$form.attr("action", ROOT+'/service/request-for-performance/confirm/'+service_id)

	$start_date.val(start_date_val);
	$start_time.val(start_time_val);
	$end_date.val(end_date_val);
	$end_time.val(end_time_val);

	//open modal
	$modal.openModal();
}

function showRejectRequestForPerformanceModal(service_id){

	//get modal elements

	$yes_link = $confirm_change_status_of_service_modal.find('.yes-link');
	$optional_content = $confirm_change_status_of_service_modal.find('.optional-content');

	//update modal elements

	$yes_link.attr("href", ROOT+'/service/request-for-performance/reject/'+service_id)
	$optional_content.html("Are you sure you want to <span class='bold-weight reject-color'>reject</span> this request for performance?");

	//open modal
	$confirm_change_status_of_service_modal.openModal();
}

function showConfirmRequestForServiceModal(service_id){

	
	//get modal elements

	var $modal = $confirm_request_for_service_modal;

	$form = $modal.find('#confirm-request-for-service-modal-form');

	//update modal elements

	$form.attr("action", ROOT+'/service/request-for-service/confirm/'+service_id)

	//open modal
	$modal.openModal();
}

function showRejectRequestForServiceModal(service_id){

	//get modal elements

	$yes_link = $confirm_change_status_of_service_modal.find('.yes-link');
	$optional_content = $confirm_change_status_of_service_modal.find('.optional-content');

	//update modal elements

	$yes_link.attr("href", ROOT+'/service/request-for-service/reject/'+service_id)
	$optional_content.html("Are you sure you want to <span class='bold-weight reject-color'>reject</span> this request for service?");

	//open modal
	$confirm_change_status_of_service_modal.openModal();
}

function showCancelRequestForServiceModal(service_id){

	//get modal elements

	$yes_link = $confirm_change_status_of_service_modal.find('.yes-link');
	$optional_content = $confirm_change_status_of_service_modal.find('.optional-content');

	//update modal elements

	$yes_link.attr("href", ROOT+'/service/request-for-service/cancel/'+service_id)
	$optional_content.html("Are you sure you want to <span class='bold-weight reject-color'>cancel</span> this request for service?");

	//open modal
	$confirm_change_status_of_service_modal.openModal();
}