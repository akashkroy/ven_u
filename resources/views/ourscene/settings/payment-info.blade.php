<?php
use OurScene\Models\User;
?>

<div id="settings-container">
	<div class="section">
		{!! Form::open(
				array(
					'id'		=> 'paypal-payment-update-form'
				)
			)
		!!}
		<div id="success-credit" style="display:none;" class="success-field">Credit Card Saved</div>
		<div id="invalid-credit" style="display:none;" class="error-field">Invalid Credit Card</div>
		<div id="failed-credit" style="display:none;" class="error-field">Saving failed. Try Again</div>
		<div class="row">
			<div class="col s12 m12 l12">
				<span style="font-size: 11px; font-style: italic;">
					 Items indicated with&nbsp;
				<font style="color: #f00; font-style: normal; font-size: 13px;">*</font> are required.</span>
				<br/><br/><br/>
			</div>

			<div class="col s12 m12 l12">
				<div class="row">
					<div class="col s12 m4 l4 input-field">
						<label for="paypal-email">
							<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
							First Name
							<span style="font-size:.8em; color: #aaa;">(name used in credit card)</span>
						</label>
						<input type="text" id="paypal-first-name" name="paypal-first-name" class="registration-txtbx-1" placeholder="First Name" value="{!! old('paypal-email') !!}" required/>
					</div>
					<div class="col s12 m4 l4 input-field">
						<label for="paypal-email">
							<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
							Last Name
							<span style="font-size:.8em; color: #aaa;">(name used in credit card)</span>
						</label>
						<input type="text" id="paypal-last-name" name="paypal-last-name" class="registration-txtbx-1" placeholder="Last Name" value="{!! old('paypal-email') !!}" required/>
					</div>
					<div class="col s12 m12 l12 input-field">
					</div>
			        <div class="input-field col s12 m4 l4">
			          <input type="email" name="paypal-email" class="registration-txtbx-1" placeholder="Email" value="" required/>
			          <label for="first_name"><font style="color: #f00; font-style: normal; font-size: 13px;">*</font> Email</label>
			        </div>
	     		</div>
		 	</div>

		 	<div class="col s12 m12 l12">
				<div class="row">
					<div class="col s12 m3 l3 input-field">
						<label for="paypal-email">
							<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
							Credit Card Number
						</label>
						<input type="text" id="paypal-number" name="paypal-card-number" class="registration-txtbx-1" placeholder="Card Number" value="" required autocomplete="off"/>
					</div>
					<div class="col s12 m3 l2 input-field">
						<select name="paypal-card-type" class="icons">
							<option value="Visa" 		id="visa" 		class="circle">Visa</option>
							<option value="MasterCard"  id="mastercard" class="circle">Master Card</option>
							<option value="Amex"  		id="amex" 		class="circle">American Express</option>
							<option value="Discover" 	id="discover" 	class="circle">Discover</option>
						</select>
						<label for="paypal-email">
							<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
							Credit Card Type
						</label>
					</div>
					<div class="col s12 m2 l2 input-field">
						<label for="cvv2">CVV</label>
						<input type="text" name="cvv2" id="cvv2" maxlength="3" minglength="3" placeholder="CVV" value="" required/>
					</div>
				</div>
			</div>

			<div class="col s12 m12 l12">
				<div class="row">
					<div class="col s12"></div>
					<div class="col s12 m2 l2 input-field">
						<label for="paypal-month">
							<font style="color: #f00; font-style: normal; font-size: 13px;">*</font>
							Expiration Date
						</label>
						<input type="text" name="paypal-month" id="paypal-month"  class="payment-input" maxlength="2" placeholder="MM" value="" required/>
						<!-- <input type="text" id="paypal-month"  class="payment-input" maxlength="2" placeholder="MM" value="" /> -->
					</div>
					<div class="col s12 m2 l2 input-field">
						<label for="paypal-email"></label>
						<input type="text" name="paypal-year" class="registration-txtbx-1" maxlength="4" minglength="4" placeholder="YYYY" value="" required/>
					</div>
					<input type="hidden" name="paypal-card-id" value="">
					<input type="hidden"  name="paypal-payer-id" value="">
				</div>
			</div>
		</div>

		<div class="input-field row">
			<input type="submit" id="update-payment-info" class="btn ourscene-btn-1 pull-right col s12 m4 l4" value="SAVE"/>
		</div>
		<div class="row remove-payment-info" style="display:none">
			<div class="col s12">
				<a id="remove-account-btn" class="btn" href="javascript:void(0);">Remove this Payment Account</a>
			</div>
		</div>

		{!! Form::close() !!}

		{!! Form::open(
				array(
					'url'		=> action('PaypalController@removePaymentAccount'),
					'method'	=> 'POST',
					'id'		=> 'remove-account-form'
				)
			)
		!!}
			<input type="hidden" value="{{Session::get('id')}}" name="id"/>
		{!! Form::close() !!}
	</div>
</div>
<script type="text/javascript">
	var hasVault = false;

	// Check if user has an vault paypal account
	function checkVault(){
		$("#paypal-payment-update-form :input.registration-txtbx-1").prop("disabled",true)
		$.ajax({
				 url: "{{ action('PaypalController@checkHasVault')}}",
				 type: "GET", 
				 dataType: "json",
				 success: function(data){
				 	console.log("SUCCESS");
					 console.log(data)
					if (data == "false") {
						$.ajax({
							 url: "{{ action('PaypalController@ajaxGetVault')}}",
							 type: "GET", 
							 dataType: "json", 
							 success: function(data){
								//  Autofill the paypal information
								console.log(data)
								 $("#paypal-payment-update-form :input,#paypal-payment-update-form select").prop("disabled",true)
								 $('#paypal-first-name').val(data.first_name)
								 $('#paypal-last-name').val(data.last_name)
								 $('input[name="paypal-email"]').val(data.payer_id)
								 $('input[name="paypal-card-number"]').val(data.number)
								 $('input[name="paypal-month"]').val(data.expire_month)
								 $('input[name="paypal-year"]').val(data.expire_year)
								 $("#"+data.type).prop("selected","selected")
								 $('input[name="cvv2"]').val("xxx")
								 $('.remove-payment-info').show();
								 $('#update-payment-info').hide();
							 }
						 }).done(function(data){

						 }).fail(function(data){
							 console.log("FAIL")
							 console.log(data)
							 $("#paypal-payment-update-form :input,#paypal-payment-update-form select").prop("disabled",false)
							 $('#update-payment-info').show();
						 }).always(function(){
							 console.log("completed")
						 })
					}else {
						@if(User::where('_id',Session::get('id'))->first()['paypal_info']['card_id'] == "")
							$('input[name="paypal-email"]').val("{{User::where('_id',Session::get('id'))->first()['paypal_info']['email']}}")
						@endif
						$("#paypal-payment-update-form :input,#paypal-payment-update-form select").prop("disabled",false)
					}
				 }
		 }).done(function(data){

		 }).fail(function(data){
			 console.log(data)
		 }).always(function(){
			 console.log("completed")
		 })
	}
	checkVault()

	// Paypal Info Update button event
	$('#paypal-payment-update-form').submit(function(e){
		
		e.preventDefault();

		var $submit_btn = $('#update-payment-info');

		$submit_btn.css({"opacity":".6"})
		$submit_btn.prop("disabled",true)

		$.ajax({
				 url: "{{ action('PaypalController@postStoreCreditCard')}}",
				 type: "POST",
				 data: $('#paypal-payment-update-form').serialize(),
				 dataType: "json",
				 success: function(data){
					console.log(data)
				 }
		 }).done(function(data){
			 $submit_btn.css({"opacity":"1"})
			 $submit_btn.prop("disabled",false)
			 $("#success-credit").show().delay(5000).fadeOut()
			 checkVault()
		 }).fail(function(data){
			 console.log(data);
			 if (data.responseJSON == "invalid") {
				$("#invalid-credit").show().delay(5000).fadeOut()
			 }
			 else if (data.responseJSON == "failed") {
				$("#failed-credit").show().delay(5000).fadeOut()
			 }
			 else{
				 $("#invalid-credit").show().delay(5000).fadeOut()
			 }
			 $submit_btn.css({"opacity":"1"})
			 $submit_btn.prop("disabled",false)
		 }).always(function(){
			 console.log("completed")
			 $submit_btn.css({"opacity":"1"})
		 })
	});

	$('#remove-account-btn').on('click',function(){
	  $('#remove-account-form').submit()
	});

</script>