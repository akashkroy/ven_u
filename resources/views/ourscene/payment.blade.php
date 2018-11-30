<?php
use OurScene\Models\User;
?>

@extends('ourscene/layouts.main')

@section('head')

<script>
	var AJAX_AUTOCOMPLETE_GET_USERS_WITH_PAYPAL_ACCOUNT_URL = "{{ action('PaypalController@getAutocompleteUsersWithPaypalAccount') }}";
</script>

<!-- Utils -->
<script src="{{ asset('js/utils/url_utils.js') }}"></script>
<style media="screen">
	li.tab a{
		border: 0;
		color: #333 !important;
	}
	#profile-navigation > div.indicator{
		height: 4px;
    background-color: #534d93;
	}
</style>
<script type="text/javascript">



</script>
@endsection

@section('content')
<ul id="reciever-dropdown" class="dropdown-content autocomplete">

</ul>
<div id="payment" class="card">
	<div class="card-action title">
		<img src="{{ asset('images/icons/payment-purple.svg') }}"/>
		Payment
	</div>

	<div class="row">
		<div class="col s12 m12 l5">
			<ul class="tabs" id="payment-navigation">
				<li class="tab col s3"><a class="active" href="#payment-details">Payment Details</a></li>
				<li class="tab col s3"><a  href="#payment-history">Payment History</a></li>
			</ul>
		</div>
	</div>
	<div class="row">
		<div class="col l12 m12">
			<!-- Paypal Money Transfer Form -->
			<div id="payment-details" class="col s12">
				<div style="overflow: hidden;">
					<h4>Paypal Money Transfer</h4>
					<br/>
					<img src="{{ asset('images/icons/paypal.svg') }}" style="height: 35px; "/>

					{!! Form::open(array(
						'url'		=> action('PaypalController@postPaypalMoneyTransfer'),
						'method'	=> 'POST',
						'id'		=> 'paypal-money-transfer-form'
					)) !!}
					<div class="row" style="margin-top: 1em;">
						<div class="col s12 m12 l12">
							<div class="input-field col s12 m6 l6">
								<input type="text" name="receiver" autocomplete="off" id="receiver"  required/>
								<input type="hidden" name="receiver_user_id" id="receiver-user-id" value="">
								<label for="last_name">To</label>
			        </div>
							<div class="input-field col s12 m4 l4">
			          <input id="amount" name="amount" placeholder="Amount" min="0" step=".01" required type="number" class="validate">
				        <label for="first_name">Amount</label>
							</div>
							<div class="input-field col s12 m2 l2">
			          <input style="width: 100%;" type="submit" class="btn btn-primary pull-right" value="Pay"/>
							</div>
						</div>
					</div>

					{!! Form::close() !!}
				</div>
			</div>

			<!-- Payment History -->

			<div id="payment-history" class="col s12">
				<h4>Payment History</h4>
				<table class="table">
					<thead>
						<tr>
							<th>Transaction date</th>
							<th>Sender</th>
							<th>Recipient</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>

					@if (count($payments))
						@foreach($payments as $payment)
						<tr>
							<td>{{ $payment['transaction_date']->toDateTime()->format('M d, Y H:i A') }}</td>
							<td>{{ $payment['sender_name'] }}</td>
							<td>{{ $payment['recipient_name'] }}</td>
							<td>{{ $payment['amount'] }}</td>
						</tr>
						@endforeach
					@else
						<tr>
							<td colspan="4">The payment history is empty.</td>
						</tr>
					@endif
					</tbody>
				</table>
			</div>
			</div>
		</div>
	</div>
</div>


@endsection

@section('scripts')
<script>
	function removePaypalReceiver(){

		$paypal_receiver = $('#receiver');
		$remove_paypal_receiver_btn = $('#remove-receiver-btn');
		$paypal_receiver_user_id = $('#receiver-user-id');

		//enable and clear paypal receiver
		$paypal_receiver.val("");
		$paypal_receiver.removeAttr("readonly");

		$paypal_receiver_user_id.val("");

		//hide remove button
		$remove_paypal_receiver_btn.hide();
	}

	$(document).ready(function() {

		/* Form validation */

		var paypal_money_transfer_form_validated=false;

		$('#paypal-money-transfer-form').submit(function(e){

			if(!paypal_money_transfer_form_validated){
				e.preventDefault();

				$paypal_receiver_user_id = $('#receiver-user-id');
				if ($paypal_receiver_user_id.val()) {
					paypal_money_transfer_form_validated=true;

					//submit form
					$(this).submit();
				}
				else{
					alert('Please select a recipient.');
				}
			}

		});

		/* Autocomplete */
		$('#receiver').customAutoComplete()
		$(document).on('click','#reciever-dropdown li',function(){
			$(this).parent().hide()
			var id = $(this).find('.id').val()
			var name = $(this).find('.name').val()
			$('#receiver').val(name)
			$('#receiver-user-id').val(id)
		})

	});
</script>

@endsection
