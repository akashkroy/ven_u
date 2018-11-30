<?php
use OurScene\Models\User;
?>
@section('head')
<style>

#ui-datepicker-div > div.ui-datepicker-header.ui-widget-header.ui-helper-clearfix.ui-corner-all > div > select{
	display: inline-block !important;
}
#ui-datepicker-div > table{
	display: none;
}
</style>
@endsection

@extends('ourscene.layouts.entrypoint')

@section('content')
<div id="register-venue" class="main body-reg-venue">
	<div class="card registration-panel">
		
		<div class="register-heading">
			<div>
				<img id="user-type-icon" src="{{asset('images/icons/artist.svg')}}" />
				<span>Register as <b>Venue</b> </span>
			</div>
		</div>
		
		<div class="row" style="margin: 0;">
			<div class="col s12 m12 l12">
				@if (count($errors) > 0)
					<div class="alert alert-danger">
						<strong>Whoops!</strong> There were some problems with your input.<br><br>
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				@if(Session::has('error'))
					<div class="error-field">{{ Session::get('error') }}</div>
				@endif

				<div id="retype-password-error" class="error-field" style="display: none;"></div>

				<div id="register-error" class="alert alert-danger" role="alert" style="display: none;">

				</div>
			</div>
		</div>

		<div id="profile-details">

			<div class="row">
				<!-- Start of Form -->
				{!! Form::open(array(
					'url'		=> action('UserController@postRegisterAsVenue'),
					'method'	=> 'POST',
					'files'		=> 'true',
					'id'		=> 'register-venue-form'
				)) !!}
				<input type="hidden" id="timezone_offset" name="timezone_offset" value=""/>

				<div class="row input-field">
		    		<div class="col s12 m6 l6">
						<label for="" class="active">Profile picture</label>
					</div>
				</div>

				<div class="row">
		    		<div class="col s12 m12 l12 input-field">
						<img 	id="profile-picture-preview" class="profile-picture-preview" src="{{asset('images/icons/artist.svg')}}"/>
						<input  id="profile-picture" type="file"  class="form-control" name="profile-picture" onchange="loadProfilePicture(event)"/>
					</div>
				</div>

				<div class="note">
					Required items indicated are with
					<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
				</div>

				<br/>

				<div class="row input-field">
		    		<div class="col s12 m6 l6">
						<label for="name">Venue Name <font style="color: #f00;">*</font></label>
						<input id="name" type="text" class="form-control registration-txtbx-1" name="name" value="{!! old('name') !!}" placeholder="Name" autofocus required/>
					</div>
				</div>
				<div class="row input-field">
		    		<div class="col s12 m6 l6">
						<label for="register-email">Email address<font style="color: #f00;">*</font></label>
						<input type="email" id="register-email"  name="register-email" class="registration-txtbx-1" value="{!! old('email') !!}" placeholder="your@email.com" required/>
					</div>
				</div>
				<div class="row">
		    		<div class="col s12 m6 l6 input-field">
						<label for="register-password">Password <font style="color: #f00;">*</font></label>
						<input type="password" id="register-password" class="registration-txtbx-1" name="password" value="{!! old('password') !!}" placeholder="8 min. characters" minlength="8" pattern=".{8,}" required/>
					</div>
					<div class="col s12 m6 l6 input-field">
						<label for="register-password">Retype Password <font style="color: #f00;">*</font></label>
						<input type="password" id="register-retype-password" class="registration-txtbx-1" name="retype_password" value="{!! old('password') !!}" placeholder="Retype Password" minlength="8" required/>
					</div>
				</div>
				<div class="row input-field">
		    		<div class="col s12 m12 l12 ">
						<label for="description">About<font style="color: #f00;">*</font></label>
						<textarea id="description" class="registration-txtbx-1" name="description" cols="50" rows="20" style="resize: none;" placeholder="About the venue"required>{!!Input::old('description')!!}</textarea>
					</div>
				</div>

				<br/>

				<div class="row input-field">
		    		<div class="col s12 m6 l6">
						<label for="" class="active">Venue type</label>
					</div>
				</div>

				<div class="row input-field">
					@foreach($venue_types as $key => $value)
						<div class="col s6 m4 l4">
							<input type="checkbox" id="{!! $key !!}" name="{!! $key !!}" class="filled-in"/>
							<label for="{!! $key !!}">{!!$value!!}</label>
						</div>
					@endforeach
				</div>

				<br/>

				<div class="row input-field">
		    		<div class="col s12 m6 l6 ">
						<label for="about">Other</label>
						<input type="text" class="registration-txtbx-1" name="other_venue_type" placeholder="Venue type"/>
					</div>
				</div>

				<br/>

				<div class="row input-field">
		    		<div class="col s12 m6 l6">
						<label for="" class="active">Address</label>
					</div>
				</div>

				<div class="row">
		    		<div class="col s12 m6 l6 input-field">
						<label for="unit_street">Unit/Building/Street <font style="color: #f00;">*</font></label>
						<input type="text" class="registration-txtbx-1" name="unit_street" placeholder="" value="{!! old('unit_street') !!}" required/>
					</div>
					<div class="col s12 m6 l6 input-field">
						<label for="city">City <font style="color: #f00;">*</font></label>
						<input type="text" class="registration-txtbx-1" name="city" placeholder="" value="{!! old('city') !!}" required/>
					</div>
					<div class="col s12 m6 l6 input-field">
						<label for="zipcode">Zip Code <font style="color: #f00;">*</font></label>
						<input type="text" maxlength="5" class="registration-txtbx-1" placeholder="" name="zipcode" value="{!! old('zipcode') !!}" required/>
					</div>
					<div class="col s12 m6 l6 input-field">
						<label for="state">State/Province <font style="color: #f00;">*</font></label>
						<input type="text" class="registration-txtbx-1" name="state" placeholder="" value="{!! old('state') !!}" required/>
					</div>
					<div class="col s12 m6 l6 input-field">
						<label for="country">Country <font style="color: #f00;">*</font></label>
						<input type="text" class="registration-txtbx-1" name="country" placeholder="" value="{!! old('country') !!}" required/>
					</div>
				</div>

				<br/>

				<div class="row input-field">
		    		<div class="col s12 m6 l6 ">
						<label for="phone_number">Contact Number <font style="color: #f00;">*</font></label>
						<input type="text" class="registration-txtbx-1" name="phone_number" placeholder="" value="{!! old('phone_number') !!}" pattern="[0-9-+\s\(\)]*" required/>
					</div>
				</div>

				<br/>

				<div class="row input-field">
		    		<div class="col s12 m6 l6">
						<label for="" class="active">Operating hours <font style="color: #f00;">*</font></label>
					</div>
				</div>

				<div class="row">
		    		<div class="col s6 m3 l3 input-field">

							<input type="text" name="operating_hrs_open" id="operating_hrs_open" class="time-picki-picker"  required>
							<label for="operating_hrs_open" style="top:-.8rem">Open </label>
					</div>

					<div class="col s6 m3 l3 input-field">

						<input type="text" name="operating_hrs_close" id="operating_hrs_close"  class="time-picki-picker" required>
						<label for="operating_hrs_close" style="top:-.8rem">Close </label>
					</div>
					<div class="col s12 m4 l4 input-field">
						<label for="seating_capacity">Seating Capacity <font style="color: #f00;">*</font></label>
						<input type="number" name="seating_capacity" class="registration-txtbx-1" min="1" step="1" placeholder="Seating Capacity" value="{!! old('seating_capacity') !!}" />
					</div>
				</div>

				<br/>

				<div class="row input-field">
		    		<div class="col s12 m6 l6">
						<label for="" class="active">Venue Serves</label>
					</div>
				</div>

				<div class="row input-field">
		    		<div class="col s6 m3 l4">
						<input type="checkbox" id="serves_alcohol" name="serves_alcohol" class="filled-in"/>
						<label for="serves_alcohol">Serves Alcohol</label>
					</div>
					<div class="col s6 m3 l4">
						<input type="checkbox" id="serves_food" name="serves_food" class="filled-in"/>
						<label for="serves_food">Serves Food</label>
					</div>
				</div>

				<br/><br/>

				<div class="row input-field">
		    		<div class="col s12 m6 l6">
						<label for="" class="active">Social Media Accounts</label>
					</div>
				</div>

				<div class="row">
		    		<div class="col s12 m6 l6 input-field">
						<label for="facebook_account">Facebook</label>
						<input type="text" id="facebook_account" name="facebook_account" class="registration-txtbx-1" placeholder="" value="{!! old('facebook_account') !!}"/>
					</div>
					<div class="col s12 m6 l6 input-field">
						<label for="twitter_account">Twitter</label>
						<input type="text" id="twitter_account" name="twitter_account" class="registration-txtbx-1" placeholder="" value="{!! old('twitter_account') !!}"/>
					</div>
				</div>

				<br/>

				<div class="row" style="display:none">
					@include('auth/payment-form')
				</div>

				@include('auth/agree-to-terms')

				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection


@section('scripts')
<script>
	var payment_is_required = false;
	$(".payment-input").on("change blur", function() {
   		if(!payment_is_required){
   			$('.payment_required').show();
   			payment_is_required = true;
   		}
	});


	$(document).ready(function() {
		var timezone_offset = new Date().getTimezoneOffset();
		$("#timezone_offset").val(timezone_offset);
	});
	$(document).ready(function() {

	    var register_venue_form_validated = false;
    	var paypal_api_url = "{{env("PAYPAL_REST_API_BASE_URL")}}";
    	var paypal_client_id = "{{env("PAYPAL_CLIENT_ID")}}";


		$('#register-venue-form').submit(function(e){
			$('#register-btn').css({"opacity":".6"})
			if(!register_venue_form_validated){
				e.preventDefault();

				var no_error = true;

				var $new_password = $('#register-password');
				var $retype_password = $('#register-retype-password');
				var $retype_password_error = $('#retype-password-error');
				var $register_artist_button = $('#register-btn');

				$register_artist_button.prop('disabled', true);

				$('.error-field').hide();

				//check if new and retype password match
				if($new_password.val() != $retype_password.val()){

					$new_password.val('');
					$retype_password.val('');

					//show retype password error
					$retype_password_error.html('Passwords do not match.');
					$retype_password_error.show();

					no_error=false;

					//scroll to top
					$('html, body').animate({scrollTop : 0},800);
				}

				if ($('#paypal-first-name').val() == "" && $('#paypal-last-name').val() == ""
				&& $('#paypal-number').val() == "" && $("#paypal-month").val() == "" && $('#paypal-year').val() == "") {
					$('#register-artist-form').submit();
				}

				if($('#paypal-number').val() != '' ){
					if($('#paypal-month').val() == ""){
						alert("Expiration month missing. Please provide complete credit card information.")
						$('#register-btn').css({"opacity":"1"})
						$register_artist_button.prop('disabled', false);
						return
					}
					else if ($('#paypal-year').val() == "") {
						alert("Expiration year missing. Please provide complete credit card information.")
						$('#register-btn').css({"opacity":"1"})
						$register_artist_button.prop('disabled', false);
						return
					}
					else if ($('#paypal-number').val() == "") {
						alert("Credit Card Number missing. Please provide complete credit card information.")
						$('#register-btn').css({"opacity":"1"})
						$register_artist_button.prop('disabled', false);
						return
					}
				}

				if(no_error){
					register_venue_form_validated=true;
					
					if ($('#paypal-number').val() == '' && $('#paypal-email').val() == '') {
						$('#register-venue-form').submit();
					}else{
						$.ajax({ // Get token for PAYPAL
						     url: paypal_api_url+"/v1/oauth2/token",
						     type: "POST",
						     data: "grant_type=client_credentials",
						     beforeSend: function (xhr) {
						          xhr.setRequestHeader('Authorization', 'Basic ' + btoa(paypal_client_id));
						          xhr.setRequestHeader('Accept-Language', 'en_US');
						     },
						     dataType: "json", 
						     success: function(data){

						     }
						 }).done(function(data){ // Register Credit Card in PAYPAL API VAULT
						    $.ajax({
						         url: paypal_api_url+"/v1/vault/credit-card",
						         type: "POST", 
						         data: JSON.stringify({
						          "number"	: $('#paypal-number').val().replace(new RegExp("-", 'g'),""),
						          "payer_id": $('#paypal-email').val(),
						          "type"	: $('#paypal-card-type').val(),
								  "cvv2" 	: $("#paypal-cvv").val(),
						          "expire_month": $('#paypal-month').val(),
						          "expire_year"	: $('#paypal-year').val(),
						          "first_name" 	: $('#paypal-first-name').val(),
								  "last_name"	: $('#paypal-last-name').val()
						         }),
						         beforeSend: function (xhr) {
						              xhr.setRequestHeader('Authorization', 'Bearer ' + data.access_token);
						              xhr.setRequestHeader("Content-Type" , "application/json");
						         },
						         dataType: "json", // specify the dataType for future reference
						         success: function(data){
						          $("#card-id").val(data.id)
						          if($("#card-id").val() == ""){
						            no_error = false
									$('#register-btn').css({"opacity":"1"})
									register_artist_form_validated = false
						            alert("Something went wrong. Please try again.")
						          }
						          if(no_error){
						            register_venue_form_validated=true;
						            $('#register-venue-form').submit();
						          }
						          else{
						            //enable register button
						            $register_artist_button.prop('disabled', false);
						          }
						         }
						     }).done(function(data){
						       $register_artist_button.prop('disabled', false);
						     }).fail(function(data){
						       $register_artist_button.prop('disabled', false);
						       alert("Invalid Credit Card Information")
							   register_venue_form_validated = false
							   $('#register-btn').css({"opacity":"1"})
						     }).always(function(){

						     })
						 }).fail(function(data){

						 }).always(function(){

						 })
					}
				}
				else{
					//enable register button
					$register_artist_button.prop('disabled', false);
				}

			}
		});
	});
</script>
@endsection
