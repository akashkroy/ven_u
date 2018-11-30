@extends('ourscene/layouts.main')

@section('head')

<script>
	var AJAX_VALIDATE_CURRENT_PASSWORD = "{{ action('UserController@getValidateCurrentPassword') }}";
	var ASSET_URL = "{{asset('')}}"
</script>
<style media="screen">
	#equipment-list tbody tr{
		cursor: pointer;
	}
</style>
@stop

@section('content')

<div id="settings" class="card">
	<div class="card-action title">
		<img src="{{ asset('images/icons/settings.svg') }}"/>
		Settings
	</div>

	<div class="row card-action" id="ourscene-tab-navigation-row">

		<div class="col s12 m10 l6">
		  <!--tabs -->
		  <ul class="tabs" role="tablist">
		    <li role="presentation" class="tab col s3 active-reg-step-tab"><a href="#details"><span>Profile </span></a></li>
		    <li role="presentation" class="tab col s3 inactive-reg-step-tab"><a href="#equipment"><span>@if(Session::get('user_type') == 'venue') Equipment @elseif(Session::get('user_type') == 'artist') Media @endif</span></a></li>
		    <li role="presentation" class="tab col s3 inactive-reg-step-tab"><a href="#payments"><span>Payments</span></a></li>
		    <li role="presentation" class="tab col s3 inactive-reg-step-tab"><a href="#account-info"><span>Account Info</span></a></li>
		  </ul>
	  	</div>
	</div>

	<div class="row card-action">
		<div id="details" class="col s12 m12 l12" role="tabpanel">
			@if($user->user_type === 'venue')
				@include('ourscene/settings.venue-details')
			@else
				@include('ourscene/settings.artist-details')
			@endif
		</div>

		<div id="equipment" class="col s12 m12 l12" role="tabpanel">
			<div id="success-equipment" class="col s12 m10 l7 alert-equipment" style="display:none;">
				<div class="success-field">The equipment was deleted.</div>
			</div>
		@if(Session::has('success-equipment'))
			<div class="col s12 m10 l7 alert-equipment">
				<div class="success-field">{{ Session::get('success-equipment') }}</div>
			</div>
		@endif
			@include('ourscene/settings.equipment')
		</div>

		<div id="payments" class="col s12 m12 l12" role="tabpanel">
			@include('ourscene/settings.payment-info')
		</div>

		<div id="account-info" class="col s12 m12 l12" role="tabpanel">
			@include('ourscene/settings.account-info')
		</div>
	</div>
</div>

<!-- Modals -->

@include('modals.add-update-equipment')
@include('modals.activate-deactivate-modal')
@include('modals.equipment-edit')
@if($user->user_type == 'artist')
	@include('modals/artist-genre-modal')
@endif

@stop


@section('scripts')

	<script src="{{ asset('js/settings.js') }}"></script>
	<script src="{{ asset('js/update-media.js') }}"></script>

	<script>
	// get ajax request all equipment



	//Settings
	function makeDefaultProfilePic(image){
		image.onerror = "";
	    image.src = "{{asset('images/icons/artist.svg')}}";
	    return true;
	}
	// Get all equipments
	function getEquipments(){
		var table = $('#equipment-list-table')


		table.hide()
		$.ajax({
			url: "{{action('EquipmentController@getAjaxEquipment')}}" ,
			type: "GET",
				dataType: "json",
				success: function(data){
					table.find('tbody').empty()
					if(data.length < 1){
						table.append('<tr><td colspan="3">No Equipment Yet.<td><tr>')
						return
					}
					var str = ""
					str += '<tr class=""></tr>'
					data.forEach(function(item){
						str+= '<tr>'
						str += '<td class="hide"><input type="hidden" class="equipment_id" value="'+  item._id +'"></td>'
						str += '<td class="hide"><input type="hidden" class="equipment_type" value="' + item.type +  '"></td>'
						str+= '<td class="equip_name">'+ item.name +'</td>'
						str+= '<td>'
						str+= '<ul>'

						item.inclusion.forEach(function(item_){
							str+= '<li>' + item_ + '</li>'
						})
						str+= '</ul>'
						str+= '</td>'
						str+= '<td> <a href="javascript:void(0);" class="btn-flat edit-equipment-trigger"><i class="material-icons">mode_edit</i></href> <a href="javascript:void(0);" class="btn-flat delete-equipment-trigger"><i class="material-icons">delete</i></href> </td>'
						str+= '</tr>'
					})
					table.append(str)
				}
		 }).done(function(data){
			 console.log(data)
			 table.show()
		 }).fail(function(data){
			//  console.log(data)
		 }).always(function(){
			 console.log("completed")
			 table.show()
		 })

	}
	// Get all images
	function ajaxGetMaterialsImage(){
		$("#material-images").empty()
		$.ajax({
			url: "{{action('UserController@getAjaxMaterials')}}" ,
			type: "GET",
				dataType: "json",
				success: function(data){
					var str = ""

					data.forEach(function(item){
						str+= '<div class="col s6 m2 l2 ">'
						str+= ' <input type="hidden" value="'+ item._id +'">'
						str+= '<div class="material-placeholder">'
						str+= ' <img src="'+ item.url +'" class="materialboxed media-image image-preview">'
						str+= ' <a href="#!" class="remove-material-image">Remove</a>'
						str+= '</div>'
						str+= '</div>'
					})
					$("#material-images").append(str)
				}
		 }).done(function(data){
			 console.log(data)
		 }).fail(function(data){
			//  console.log(data)
		 }).always(function(){
			 console.log("completed")
		 })
	}

	var change_own_password_form_validated = false;

	$('#change-own-password-form').submit(function(e){
		console.log('submit');
		e.preventDefault();
		if(!change_own_password_form_validated){
			e.preventDefault();

			var no_error = true;

			var $new_password = $('#password');
			var $retype_password = $('#retype-password');
			var $current_password = $('#current-password');

			var $retype_password_error = $('#error-new-password');
			var $check_current_password_error = $('#check-current-password-error');

			var $change_own_password_button = $('#change-own-password-btn');

			//disable change own password button
			// $change_own_password_button.prop('disabled', true);

			//hide errors
			$retype_password_error.hide();
			$check_current_password_error.hide();

			//check if current and input current password match
			$.ajax({
				url: AJAX_VALIDATE_CURRENT_PASSWORD,
				type: "GET",
				data: {
					password: $current_password.val()
				},
				success: function (data) {
					if(data['error']){
						//show check current password error
						// $check_current_password_error.html("Please enter your correct current password.");
						$check_current_password_error.show();

						console.log('Please enter your correct current password.');

						$current_password.val('');

						no_error=false;
					}else{
						//CURRENT PASSWORD VALIDATED

						//hide current password error
						$check_current_password_error.hide();

						//check if new and retype password match
						if($new_password.val() == $retype_password.val()){
							//hide retype password error
							$retype_password_error.hide();
							$no_error=true;

						}else{

							$new_password.val('');
							$retype_password.val('');
							$new_password.focus();

							//show retype password error
							$retype_password_error.html('Passwords do not match.');
							$retype_password_error.show();

							console.log('Passwords do not match');
							no_error=false;
						}
					}

					if(no_error){
						console.log('will submit');
						change_own_password_form_validated=true;
						$('#change-own-password-form')[0].submit();
					}
				},
				error: function (data){
					console.log('error changing password');
					//show current password error
					$check_current_password_error.html("Something went wrong. Please try again.");
					$check_current_password_error.show();

					//enable change own password button
					// $change_own_password_button.prop('disabled', false);
				}
			});
		}
		return false;
	});

	$('#save-profile-form').submit(function(e){
		$('#genre-collapsible > li > .collapsible-header > input').each(function(){
			if($(this).is(':checked')){
				$("#save-profile-form").append($(this))
			}
		})

		$('#genre-collapsible > li > div.collapsible-body > div > div input').each(function(){
			// console.log($(this).val())
			if($(this).is(':checked')){
				$("#save-profile-form").append($(this))
			}
		})
		$("#save-profile-form").append(items)
		$(this).submit()
		e.preventDefault()
	})

	// Update equipment modal

	var selectedRow = null;
	var selecteEquipmentId = null;

	$(document).on('click','.edit-equipment-trigger',function(){
		$('#equipment-list-table').hide()
		$('.preloader').show()
		var $modal = $('#equipment-edit-modal');

		$modal.openModal()

		selecteEquipmentId = ($(this).parent().parent().find('.equipment_id').val())
		type = ($(this).parent().parent().find('.equipment_type').val())
		name = $(this).parent().parent().find('td:nth-child(3)').text()
		inclusion_length = $(this).parent().parent().find('td:nth-child(4) ul li').length

		var $default_checkbox = $modal.find('#edit-equipment-type-default');

		if(type=="default")
			$default_checkbox.prop('checked', true);
		else
			$default_checkbox.prop('checked', false);

		$("#inclusion_edit_field").empty()

		selectedRow = $(this).parent().parent().find('td:nth-child(4) ul li')
		// $("#inclusion_edit_field").append('<label>Inclusion</label>')
		selectedRow.each(function(){
			$("#inclusion_edit_field").append('<div><input type="text" id="inclu" name="inclusions[]"  class="registration-txtbx-1 col s8 m8 l8" placeholder="Content" value="'+ $(this).text() +'"/><a href="javascript:void(0);" id="remove-equipment-content" class="btn ourscene-btn-plain-1 col s4 m2 l2">Remove</a></div>')
		})
		$("#equipment_name").val(name)
	})

	// Update equipment button
	$("#update-equipment-btn").on('click',function(){
		$('.alert-equipment').hide()

		$('#success-equipment').text("Updating...")
		$('#success-equipment').show()

		var $modal = $('#equipment-edit-modal');
		var flag;
		var inclusion = []
		var equipmentName = $('#equipment_name').val();
		var $default_checkbox = $modal.find('#edit-equipment-type-default');

		var type = ($default_checkbox.is(":checked"))? 'default' : 'others';

		$("#inclusion_edit_field input").each(function(){
			if ($(this).val() == "") {
				alert("Please fill up all fields")
				inclusion = []
				flag = true
				return;
			}
			else{
				flag = false
				inclusion.push($(this).val())
			}

		})
		if(flag) return
		var params = "name="+equipmentName+"&id="+selecteEquipmentId+"&type="+type+"&inclusion="+inclusion
		$.ajax({
			url: "{{action('UserController@ajaxUpdateEquipment')}}?" +params ,
			type: "POST", // default is GET but you can use other verbs based on your needs.
				dataType: "json", // specify the dataType for future reference
				success: function(data){
					console.log(data)
					// ajaxGetEquipment()
				}
		 }).done(function(data){
			$('#success-equipment').text("").append("<div class='success-field'>The equipment was updated.</div>")
			getEquipments()
			$('.preloader').hide()
		 }).fail(function(data){
			 alert("Something went wrong. Please try again")
		 }).always(function(){
			 console.log("completed")
		 })
	})
	// Delete equipment
	$(document).on('click','.delete-equipment-trigger',function(){
		$('#equipment-list-table').hide()
		$('.preloader').show()
		var selecteEquipmentId = ($(this).parent().parent().find('input').val())
		$(this).parent().parent().remove()

		$('.alert-equipment').hide()

		$('#success-equipment').text("Deleting...")
		$('#success-equipment').show()

		$.ajax({
			url: "{{action('UserController@ajaxDeleteEquipment')}}?id=" + selecteEquipmentId,
			type: "POST", // default is GET but you can use other verbs based on your needs.
				dataType: "json", // specify the dataType for future reference
				success: function(data){
					console.log(data)
					$('#success-equipment').text("").append("<div class='success-field'>The equipment was deleted.</div>")
					getEquipments()
					$('.preloader').hide()
				}
		 }).done(function(data){

		 }).fail(function(data){
			 alert("Something went wrong. Please try again")
		 }).always(function(){
			 console.log("completed")
		 })
	})

	</script>
@stop
