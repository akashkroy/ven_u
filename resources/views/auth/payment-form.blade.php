<div class="row input-field">
	<div class="col s12 m6 l6">
		<label for="" class="active">Payment information</label>
	</div>
</div>

<div class="row">
	<div class="col s12 m6 l6 input-field">
		<label for="paypal-email">First Name 
			<font style="color: #f00; font-style: normal; font-size: 13px;display:none;" class="payment_required">*</font>
			<span style="font-size:.8em; color: #aaa;">(name used in credit card)</span>
		</label>
		<input type="text" id="paypal-first-name" name="paypal-email" class="registration-txtbx-1 payment-input" placeholder="First Name" value="{!! old('paypal-email') !!}" />
	</div>
	<div class="col s12 m6 l6 input-field">
		<label for="paypal-email">Last Name
			<font style="color: #f00; font-style: normal; font-size: 13px;display:none;" class="payment_required">*</font>
			<span style="font-size:.8em; color: #aaa;">(name used in credit card)</span>
		</label>
		<input type="text" id="paypal-last-name" name="paypal-email" class="registration-txtbx-1 payment-input" placeholder="Last Name" value="{!! old('paypal-email') !!}" />
	</div>
	<div class="col s12 m6 l6 input-field">
		<label for="paypal-email">Email address
			<font style="color: #f00; font-style: normal; font-size: 13px;display:none;" class="payment_required">*</font>
		</label>
		<input type="email" id="paypal-email" name="paypal-email" class="registration-txtbx-1" placeholder="Email Address" value="{!! old('paypal-email') !!}" />
	</div>
	<div class="col s12 m6 l6 input-field">
		<select id="paypal-card-type" name="paypal-card-type" class="icons payment-input">
			<option value="visa" class="circle">Visa</option>
			<option value="mastercard"  class="circle">Master Card</option>
			<option value="amex"  class="circle">American Express</option>
			<option value="discover"  class="circle">Discover</option>
		</select>
		<label for="paypal-email">Credit Card Type
			<font style="color: #f00; font-style: normal; font-size: 13px;display:none;" class="payment_required">*</font>
		</label>
	</div>
	<div class="col s12"></div>
	<div class="col s12 m6 l6 input-field">
		<label for="paypal-email">Credit Card Number
			<font style="color: #f00; font-style: normal; font-size: 13px;display:none;" class="payment_required">*</font>
		</label>
		<input type="text" id="paypal-number" name="paypal-number" class="registration-txtbx-1 payment-input" placeholder="Card Number" value="" autocomplete="off"/>
		<input type="hidden" id="card-id" name="paypal-card-id">
	</div>
	<div class="col s12 m3 l3 input-field">
		<label for="paypal-month" style="margin-top: -25px;">Expiration Date
			<font style="color: #f00; font-style: normal; font-size: 13px;display:none;" class="payment_required">*</font>
		</label>
		<select id="paypal-month" class="payment-input">
		<?php for($i=1; $i<=12; $i++){
			$value = str_pad($i, 2, '0', STR_PAD_LEFT);
		?>
			<option value="{{ $value }}">{{ $value }}</option>
		<?php } ?>
		</select>
	</div>
	<div class="col s12 m3 l3 input-field">
		<label for="paypal-year"></label>

		<select id="paypal-year" class="payment-input">
		<?php
			$current_year = intval(date("Y"));
			for($i=$current_year; $i<=($current_year+100); $i++){
				$value = str_pad($i, 2, '0', STR_PAD_LEFT);
		?>
			<option value="{{ $value }}">{{ $value }}</option>
		<?php } ?>
		</select>
	</div>
</div>