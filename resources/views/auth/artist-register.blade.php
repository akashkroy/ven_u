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

<div id="register-artist" class="main body-reg-artist">
	<div class="card registration-panel">

		<div class="register-heading">
			<div>
				<img id="user-type-icon" src="{{asset('images/icons/artist.svg')}}" />
				<span>Register as <b>Artist</b> </span>
			</div>
			<br/>
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

				@if(Session::has('success'))
					<div class="success-field">{{ Session::get('success') }}</div>
				@endif
				@if(Session::has('error'))
					<div class="error-field">{{ Session::get('error') }}</div>
				@endif

				<div id="retype-password-error" class="error-field" style="display: none;"></div>
				<div id="email-error" class="error-field" style="display: none;"></div>

				<div id="file-size-exceeded-error" class="error-field" style="display: none;">
					Your total upload file size exceeded 320 MB.
				</div>
				<div id="register-error" class="alert alert-danger" role="alert" style="display: none;"></div>

			</div>
		</div>

		<div class="row" style="margin: 0;">
			
			<!-- Tab navigation -->

			<div class="tab-navigation">
				<ul class="tabs" role="tablist">
					<li role="presentation" class="tab col s3 reg-step-tab @if(Session::has('success')) disabled @endif"><a href="#details"><span>1 &nbsp;&nbsp;&nbsp; Profile Details</span></a></li>
					<li role="presentation" class="tab col s3 reg-step-tab @if(Session::has('success')) active @else disabled @endif"><a href="#materials"><span>2 &nbsp;&nbsp;&nbsp; Media</span></a> </li>
				</ul>
		  	</div>
		
			<!-- Tab for profile details -->

			<div id="details" class="col s12 m12 l12" role="tabpanel">
				
				{!! Form::open(array(
					'url'		=> action('UserController@postRegisterAsArtist'),
					'method'	=> 'POST',
					'files'		=> 'true',
					'id'		=> 'register-artist-form',
					'class'     => "col s12"
				)) !!}
				<input type="hidden" id="timezone_offset" name="timezone_offset" value=""/>

				<div class="row input-field">
		    		<div class="col s12 m6 l6">
						<label for="" class="active">Profile picture</label>
					</div>
				</div>

				<div class="row">
		    		<div class="col s12 m12 l12 input-field">
						<img id="profile-picture-preview" class="profile-picture-preview circle" src="{{asset('images/icons/artist.svg')}}"/>
						<input id="input-profile-picture" type="file"  class="form-control" name="profile-picture" onchange="loadProfilePicture(event)"/>
					</div>
				</div>

				<div class="note">
					Required items indicated are with
					<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
				</div>

				<br/>

				<div class="row input-field">
		    		<div class="col s12 m6 l6">
						<label for="name">Artist Name <font style="color: #f00;">*</font></label>
						<input id="name" type="text" class="form-control registration-txtbx-1" name="name" value="{!! old('name') !!}" placeholder="Name" autofocus required/>
					</div>
				</div>

				<div class="row input-field">
		    		<div class="col s12 m6 l6">
						<label for="register-email">Email address <font style="color: #f00;">*</font></label>
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
						<input type="password" id="register-retype-password" class="registration-txtbx-1" name="retype_password" value="{!! old('password') !!}" placeholder="Retype Password" minlength="8" pattern=".{8,}" required/>
					</div>
				</div>

				<div class="row input-field">
					<div class="col s12 m0 l0">
						<label for="" class="active">Ages <font style="color: #f00;">*</font></label>
					</div>
		    		<div class="col s6 m2 l3">
						<input type="radio" id="age-none" name="ages" class="with-gap" value="none" checked/>
						<label for="age-none">None</label>
					</div>
		    		<div class="col s6 m2 l3">
						<input type="radio" id="age-18" name="ages" class="with-gap" value="18+"/>
						<label for="age-18">18+</label>
					</div>
					<div class="col s6 m2 l3">
						<input type="radio" id="age-21" name="ages" value="21+" class="with-gap"/>
						<label for="age-21">21+</label>
					</div>
				</div>

				<br/>

				<div class="row input-field">
		    		<div class="col s12 m6 l6 input-field">
						<label for="city">City <font style="color: #f00;">*</font></label>
						<input type="text" id="city" class="registration-txtbx-1" name="city" value="{!! old('city') !!}" placeholder="Brooklyn Heights"  required/>
					</div>
					<div class="col s12 m6 l6 input-field">
						<label for="zipcode">Zip Code <font style="color: #f00;">*</font></label>
						<input type="text" id="zipcode" class="registration-txtbx-1" maxlength="5" name="zipcode" value="{!! old('zipcode') !!}" placeholder="5110309" required/>
					</div>
				</div>
				<div class="row input-field">
		    		<div class="col s12 m6 l6 ">
						<label for="phone_number">Contact Number</label>
						<input type="text" id="phone_number" name="phone_number" class="registration-txtbx-1" 
							value="{!! old('phone_number') !!}" placeholder="Contact number" pattern="[0-9-+\s\(\)]*"/>
					</div>
				</div>
				<div class="row input-field">
		    		<div class="col s12 m12 l12 ">
						<label for="description">About <font style="color: #f00;">*</font></label>
						<textarea id="description" class="registration-txtbx-1" name="description" cols="50" rows="5" style="resize: none;" placeholder="About the artist" required>{!!Input::old('description')!!}</textarea>
					</div>
				</div>

				<br/>

				<div class="row">
					<div class=" col s12 m4 l4">
						<a class="btn modal-trigger" href="#artist-genre-modal">ADD GENRE</a>
					</div>
				</div>

				<br/>

				<div class="row input-field">
		    		<div class="col s12 m6 l6">
						<label for="" class="active">Social media accounts</label>
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
					<div class="col s12 m6 l6 input-field">
						<label for="soundcloud_account">Soundcloud</label>
						<input type="text" id="soundcloud_account" name="soundcloud_account" class="registration-txtbx-1" placeholder="" value="{!! old('soundcloud_account') !!}"/>
					</div>
					<div class="col s12 m6 l6 input-field">
						<label for="bandcamp_account">Bandcamp</label>
						<input type="text" id="bandcamp_account" name="bandcamp_account" class="registration-txtbx-1" placeholder="" value="{!! old('bandcamp_account') !!}"/>
					</div>
				</div>
				
				<br/>

				<div class="row" style="display:none">
				@include('auth/payment-form')
				</div>
				<br/>

				@include('auth/agree-to-terms')

				@include('modals/artist-genre-modal')

				{!! Form::close() !!}
		  	</div>

		  	<!-- End tab for profile details -->

			<!-- Tab for materials -->
			
			<div id="materials" class="col s12 m12 l12" role="tabpanel">
				{!! Form::open(array(
					'url'		=> action('UserController@postRegisterMaterials'),
					'method'	=> 'POST',
					'files'		=> 'true',
					'class'    => "col s12",
					'id' => 'add-media-form'
				)) !!}

				<div class="row input-field">
		    		<div class="col s12 m6 l6">
						<label for="" class="active">Upload Songs <font class="light-weight" style="color: #AAA;">(.mp3 | .m4a | .wav)</font></label>
					</div>
				</div>

				<!-- Songs -->

				<div class="row" id="register-materials-songs"></div>

				<div class="row">
					<label class="col s6 m4 l4">
						<a href="javascript:void(0);" id="add-more-songs" class="btn btn-link">Add Song</a>
					</label>
				</div>
				
				<br/><br/>

				<!-- Images -->

				<div class="row input-field">
		    		<div class="col s12 m6 l6">
						<label for="" class="active">Upload Images <font class="light-weight" style="color: #AAA;">(.jpg | .jpeg | .png | .gif)</font></label>
					</div>
				</div>

				<div class="row" id="register-materials-images"></div>

				<div class="row">
					<label class="col s6 m4 l4">
						<a href="javascript:void(0);" id="add-more-images" class="btn btn-link">Add Image</a>
					</label>
				</div>

				<br/><br/>

				<!-- Videos -->

				<div class="row input-field">
		    		<div class="col s12 m6 l6">
						<label for="" class="active">Upload Videos <font class="light-weight" style="color: #AAA;">(.mp4 | .mov )</font></label>
					</div>
				</div>

				<div class="row" id="register-materials-videos"></div>

				<div class="row">
					<label class="col s6 m4 l4">
						<a href="javascript:void(0);" id="add-more-videos" class="btn btn-link">Add Video</a>
					</label>
				</div>

				<br/><br/>

				<div class="row" style="margin: 0;">
					<div class="col s12 m12 l12">
						<a href="{{ action('HomeController@getIndex') }}" class="btn ourscene-btn-1">SKIP</a>
						<button type="submit" id="media-add-form-submit-btn" class="btn ourscene-btn-1 reg-btn" required/>SAVE</button>
					</div>
				</div>

				{!! Form::close() !!}
			</div>
			<!-- End of materials tab-->
		</div>
	</div>
</div>
<script type="text/javascript">

	var payment_is_required = false;
	
	$(".payment-input").on("change blur", function() {
   		if(!payment_is_required){
   			$('.payment_required').show();
   			payment_is_required = true;
   		}
   			
	});

	var materials_has_loaded_animators = false;

	$('#add-media-form').submit(function(e){

		//validate
		var file_size_exceeded = false;		

		var totalsize = 0;

	   $('#add-media-form input:file').each(function(){
	     if($(this).val().length > 0){
	        totalsize=totalsize+$(this)[0].files[0].size;
	      }
	 	});
	  	if (totalsize > 335544320) {
	  		console.log('did exceed');
	  	  file_size_exceeded = true;
	      $('#file-size-exceeded-error').show();
	  	}

	  	if(file_size_exceeded){
	  		e.preventDefault();
	  		console.log('prevent default file exceeded');
	  		//scroll to top
			$('html, body').animate({scrollTop : 0},800);
	  	}

		if(!file_size_exceeded && !materials_has_loaded_animators){
			e.preventDefault()
			
			$('#file-size-exceeded-error').hide();

			if($("#register-materials-images > div.col.s12.m10.l10.file-field.input-field > div > div.col.s2.m2.l2.upload-btn > input[type='file']").val() != ""){
				$('#register-materials-images').append('<div class="row"> <div class="col s7"> <div class="progress"> <div class="indeterminate"></div> </div> </div> </div>')
			}
			if($('#register-materials-songs > div > div > div.col.s2.m2.l2.upload-btn > input[type="file"]').val() != ""){
				$('#register-materials-songs').append('<div class="row"> <div class="col s7"> <div class="progress"> <div class="indeterminate"></div> </div> </div> </div>')
			}

			if($('#register-materials-videos > div > div > div.col.s2.m2.l2.upload-btn > input[type="file"]').val() != ""){
				$('#register-materials-videos').append('<div class="row"> <div class="col s7"> <div class="progress"> <div class="indeterminate"></div> </div> </div> </div>')
			}
			$('#media-add-form-submit-btn').prop("disabled",true)
			$('#media-add-form-submit-btn').css({"opacity":".6"})
			$('#media-add-form-submit-btn').text('UPLOADING ')
			$('#media-add-form-submit-btn').append('<img src="{{asset('images/icons/media_loader.svg')}}" style="position:relative;top: 3px; " alt="" width="13px" />')
			materials_has_loaded_animators = true;
			$(this).submit()
		}
	});

	var register_artist_form_validated = false;
	var paypal_api_url = "{{env("PAYPAL_REST_API_BASE_URL")}}";
	var paypal_client_id = "{{env("PAYPAL_CLIENT_ID")}}";
	var $register_artist_btn = $('#register-btn');

	$('#register-artist-form').submit(function(e){
		
		$register_artist_btn.css({"opacity":".6"});

		if(!register_artist_form_validated){
			e.preventDefault();

			var no_error = true;

			//hide all errors
			$('.error-field').hide();

			//disable register artist button
			$register_artist_btn.prop('disabled', true);

			var $new_password = $('#register-password');
			var $retype_password = $('#register-retype-password');

			var $retype_password_error = $('#retype-password-error');

			// Validate password

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

			// Validate Paypal

			if($('#paypal-number').val() != '' ){
				if($('#paypal-month').val() == ""){
					alert("Expiration month missing. Please provide complete credit card information.")
					$register_artist_btn.css({"opacity":"1"})
					$register_artist_btn.prop('disabled', false);
					return
				}
				else if ($('#paypal-year').val() == "") {
					alert("Expiration year missing. Please provide complete credit card information.")
					$register_artist_btn.css({"opacity":"1"})
					$register_artist_btn.prop('disabled', false);
					return
				}
				else if ($('#paypal-number').val() == "") {
					alert("Credit Card Number missing. Please provide complete credit card information.")
					$register_artist_btn.css({"opacity":"1"})
					$register_artist_btn.prop('disabled', false);
					return
				}
			}

			if(no_error){

				// Validate email

				var $email = $('#register-email');
				var $email_error = $('#email-error');

				$.ajax({
					url: ROOT+"/validate-email",
					type: "GET", 
					data: "email="+$email.val(),
					success: function(data){
						response = data;

						if(data['error']){
							
							no_error=false;
							
							//show email error
							$email_error.html('The given email address is already in use.');
							$email_error.show();

							//scroll to top
							$('html, body').animate({scrollTop : 0}, 800);

							//enable register button
							$register_artist_btn.css({"opacity":"1"})
							$register_artist_btn.prop('disabled', false);
						}
						else{

							if ($('#paypal-first-name').val() == "" && $('#paypal-last-name').val() == "" && $('#paypal-number').val() == "") {
								register_artist_form_validated = true;
								$('#register-artist-form').submit();
							}
							else{
								$.ajax({
									url: paypal_api_url+"/v1/oauth2/token",
									type: "POST", 
									data: "grant_type=client_credentials",
									beforeSend: function (xhr) {
											xhr.setRequestHeader('Authorization', 'Basic ' + btoa(paypal_client_id));
											xhr.setRequestHeader('Accept-Language', 'en_US');
									},
									dataType: "json", 
									success: function(data){
										console.log('auth success');
									}
								 }).done(function(data){
								 	var payer_id_registration
									if ($('#paypal-email').val() == ""){
										payer_id_registration = $('#register-email').val()
									}
									else{
										payer_id_registration = $('#paypal-email').val()
									}

									$.ajax({
											 url: paypal_api_url+"/v1/vault/credit-card",
											 type: "POST", 
											 data: JSON.stringify({
												"number": $('#paypal-number').val().replace(new RegExp("-", 'g'),""),
												"payer_id": payer_id_registration,
												"type": $('#paypal-card-type').val(),
												"expire_month": $('#paypal-month').val(),
												"expire_year": $('#paypal-year').val(),
												"first_name" : $('#paypal-first-name').val(),
												"last_name": $('#paypal-last-name').val()
											 }),
											 beforeSend: function (xhr) {
														xhr.setRequestHeader('Authorization', 'Bearer ' + data.access_token);
														xhr.setRequestHeader("Content-Type" , "application/json");
											 },
											 dataType: "json", // specify the dataType for future reference
											 success: function(data){
												console.log(data)
												$("#card-id").val(data.id)
												if($("#card-id").val() == ""){
													no_error = false
													$register_artist_btn.css({"opacity":"1"})
													register_artist_form_validated = false
													alert("Something went wrong. Please try again.")
												}
												if(no_error){
													register_artist_form_validated=true;
													$('#register-artist-form').submit();
												}
												else{
													//enable register button
													$register_artist_btn.prop('disabled', false);
												}
											 }
									}).done(function(data){
										$register_artist_btn.prop('disabled', false);
									}).fail(function(data){
										$register_artist_btn.prop('disabled', false);
										 alert("Invalid Credit Card Information");
										 register_artist_form_validated = false;
										 $register_artist_btn.css({"opacity":"1"})
									 }).always(function(){

									 })
								}).fail(function(data){

								}).always(function(){

								})
							}
						}
					}

				});

			}
			else{
				//enable register button
				$register_artist_btn.css({"opacity":"1"})
				$register_artist_btn.prop('disabled', false);
			}

		}
	});

	$(document).ready(function() {
		var timezone_offset = new Date().getTimezoneOffset();
		$("#timezone_offset").val(timezone_offset);
	});

</script>
@endsection
