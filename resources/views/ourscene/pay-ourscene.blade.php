<?php
use OurScene\Models\Payment;
use OurScene\Models\User;
?>

@extends('ourscene/layouts.main')

@section('head')

@endsection

@section('content')

<div id="pay-ourscene" class="card">
	<div class="card-action title">
		<img src="{{ asset('images/icons/payment-purple.svg') }}"/>
		Pay &nbsp; | &nbsp;&nbsp; &#36;{{ number_format(Payment::getRequestPayment(), 2) }}
	</div>

	<div id="pay-ourscene-container" class="card-action">

		<div id="before-payment-message">
			<span id="pending-action">{{ Session::get('pay_ourscene_action')['before_payment_message'] }}</span>
			<br/><br/>
			Please choose one payment method before you can proceed.
		</div>

		<!-- Payment via credit card -->

		<div class="payment-method">Pay via credit card</div>

		@if(Session::has('card_error'))
			<div class="row">
			<div id="check-current-password-error" class="error-field col s12 m6 l5">
				{{ Session::get('card_error') }}
			</div>
			</div>
		@endif
		

		<!-- <img width="130" src="{{ asset('images/icons/paypal.svg') }}"/> -->
		<br/>
		<div class="row">
			@if($user_card_info)
			{!! Form::open(array(
				'url'		=> action('PaypalController@postPayToOurScene'),
				'method'	=> 'POST',
				'id'		=> 'paypal-money-transfer-form'
			)) !!}
				<div class="col s12 m6 l6 input-field">
					<label for="paypal-email">Credit card number</label>
					<input type="text" id="paypal-number" name="paypal-number" class="registration-txtbx-1" placeholder="Card Number" value="{{$user_card_info->number}}" disabled/>
				</div>

				<div class="col s12"></div>
				<div class="col s12 m3 l2 input-field">
					<label for="paypal-email">Expiration date</label>
					<input type="text" id="paypal-month"  class="registration-txtbx-1" maxlength="2" placeholder="Month" value="{{$user_card_info->expire_month}}" disabled/>
				</div>
				<div class="col s12 m3 l2 input-field">
					<label for="paypal-email"></label>
					<input type="text" id="paypal-year" class="registration-txtbx-1" maxlength="4" minglength="4" placeholder="Year" value="{{$user_card_info->expire_year}}" disabled/>
				</div>

				<input type="hidden" 	name="paypal-card-id" 	value="{{$user_card_info->id}}">
				<input type="hidden"  	name="paypal-payer-id" 	value="{{$user_card_info->payer_id}}">
				<div class="input-field col s12 m2 l2">
					<button style="width: 100%;" type="submit" class="btn btn-primary pull-right">PAY </button>
				</div>
			{!! Form::close() !!}
			@else
			{!! Form::open(array(
				'url'		=> action('PaypalController@postPayToOurSceneCredit'),
				'method'	=> 'POST',
				'id'		=> 'paypal-money-transfer-form'
			)) !!}
				<div class="col s12 m6 l3 input-field">
					<label for="paypal-email">Credit card number</label>
					<input type="text" id="paypal-number" name="paypal-card-number" class="registration-txtbx-1" placeholder="Card Number" value="" required/>
				</div>
				<div class="col s12 m6 l2 input-field">
					<img width="" src="{{ asset('images/icons/cards.gif') }}" style="display:block;width:80%;top: -10px; position: relative; float:right;"/>
				</div>
				<div class="col s12 m6 l3 input-field">
					<select name="paypal-card-type" class="icons">
					    <option value="visa" class="circle">Visa</option>
					    <option value="mastercard"  class="circle">Master Card</option>
					    <option value="american express"  class="circle">American Express</option>
						<option value="discover"  class="circle">Discover</option>
				    </select>
					<label for="paypal-email">Credit Card Type</label>
				</div>

				<div class="col s12"></div>
				<div class="col s12 m3 l2 input-field">
					<label for="paypal-email">Expiration date</label>
					<input type="text" name="paypal-month"  class="registration-txtbx-1" maxlength="2" placeholder="MM" value="" required/>
				</div>
				<div class="col s12 m3 l2 input-field">
					<label for="paypal-email"></label>
					<input type="text" name="paypal-year" class="registration-txtbx-1" maxlength="4" minglength="4" placeholder="YYYY" value="" required/>
				</div>

				<input type="hidden" 	name="paypal-card-id" 	value="">
				<input type="hidden"  	name="paypal-payer-id" 	value="">
				<div class="input-field col s12 m2 l2">
					<button style="width: 100%;" type="submit" class="btn btn-primary pull-right">PAY </button>
				</div>
			{!! Form::close() !!}

			@endif
		</div>

		<br/>

		<!-- Payment via Paypal account -->

		<div class="payment-method">Pay via Paypal account</div>

		@if(env("PAYPAL_MODE")=='TEST')
			@include('forms/payments/sandbox-pay-via-paypal-button')
		@elseif(env("PAYPAL_LIVE_ENV") == 'PREPROD')
			@include('forms/payments/pre-prod-pay-via-paypal-button')
		@else
			@include('forms/payments/prod-pay-via-paypal-button')
		@endif

		<br/>

	</div>
</div>

@endsection

@section('scripts')

@endsection
