<?php namespace OurScene\Http\Controllers;

use Log;
use Session;
use View;
use Input;
use Redirect;
use App;
use Response;
use MongoDate;

use OurScene\Models\User;
use OurScene\Models\Payment;

use OurScene\Helpers\PaypalHelper;

class PaypalController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Paypal Controller
	|--------------------------------------------------------------------------
	|
	| This controller manages all Paypal transactions.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth.login');
	}


	/* Store credit card */
	public function postStoreCreditCard(){
		Input::merge(array_map('trim', Input::all()));
		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);
		$user = User::find(Session::get('id'));
		
		$token_result = PaypalHelper::getToken();
		if($token_result['success']){
			$token = $token_result['access_token'];
		}else{
			return Response::json("failed",$token_result['status_code']);
		}

		$credit = array(
			"number" => $input['paypal-card-number'],
			"type" => $input['paypal-card-type'],
			"expire_month" => $input['paypal-month'],
			"expire_year" => $input['paypal-year'],
			"payer_id" => $input['paypal-email'],
			"first_name" => $input['paypal-first-name'],
			"last_name" => $input['paypal-last-name'],
			"cvv2" => $input['cvv2']
		);

		$vault_card_types = array("MasterCard"=>"mastercard","Visa"=>"visa","Amex"=>"amex","Discover"=>"discover");

		$verification_result = PaypalHelper::verifyCreditCard($credit);

		if($verification_result){
			if($verification_result->CVV2Code != "M"){
				return Response::json("cvv unmatch",500);	
			}
			$credit['type'] = $vault_card_types[$credit['type']];
			
			$result = json_decode(PaypalHelper::registerToVault($token,$credit));
			if ($result) {

				$card_id = $result->id;
				$user->paypal_info = array(
					"email" => $input['paypal-email'],
					"card_id" => $card_id
				);

				if ($user->save()) {
					$user_card_info = PaypalHelper::getUserVault($token,$card_id);
					return Response::json(array('status' => "success",'user_card_info' => $user_card_info));
				}
				else{
					return Response::json("failed",500);
				}
			}else{
				return Response::json("invalid",500);
			}

		}else{
			return Response::json("Problem with authentication",500);
		}
	}

	public function postStoreCreditCardOLD(){

		Input::merge(array_map('trim', Input::all()));
		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);
		$user = User::find(Session::get('id'));
		
		$token_result = PaypalHelper::getToken();
		if($token_result['success']){
			$token = $token_result['access_token'];
		}else{
			return Response::json("failed",$token_result['status_code']);
		}

		$credit = array(
			"number" => $input['paypal-card-number'],
			"type" => $input['paypal-card-type'],
			"expire_month" => $input['paypal-month'],
			"expire_year" => $input['paypal-year'],
			"payer_id" => $input['paypal-email'],
			"first_name" => $input['paypal-first-name'],
			"last_name" => $input['paypal-last-name'],
			//"cvv2" => $input['cvv2']
		);
		$result = json_decode(PaypalHelper::registerToVault($token,$credit));

		if ($result) {
			$card_id = $result->id;
			$user->paypal_info = array(
				"email" => $input['paypal-email'],
				"card_id" => $card_id
			);

			if ($user->save()) {
				// Get secure token for transparent redirect
				$user_card_info = PaypalHelper::getUserVault($token,$card_id);
				return Response::json(array('status' => "success",'user_card_info' => $user_card_info));
			}
			else{
				return Response::json("failed",500);
			}
		}else{
			return Response::json("invalid",500);
		}
	}

	/* Vault helpers */

	public function checkHasVault(){
		$user = User::find(Session::get('id'))->paypal_info;
		if ($user['card_id'] == "") {
			return Response::json("true");
		}else {
			return Response::json("false");
		}

	}

	public function ajaxGetVault(){
		
		$token_result = PaypalHelper::getToken();
		if($token_result['success']){
			$token = $token_result['access_token'];
		}else{
			return Response::json("failed",$token_result['status_code']);
		}

		$card_id = User::where('_id',Session::get('id'))->select('paypal_info')->first()['paypal_info']['card_id'];

		// Get secure token for transparent redirect
		$user_card_info = PaypalHelper::getUserVault($token,$card_id);
		return Response::json($user_card_info);
	}

	/* Pay OurScene */

	public function getPayOurscene(){

		//check permissions
		if(! Session::has('pay_ourscene_action')){
			abort(401);
		}

		$user_card_info = null;

		if (User::where('_id',Session::get('id'))->first()['paypal_info']['card_id'] == "") {

			return View::make('ourscene.pay-ourscene')->with('user_card_info',false);

		}else{
			$token_result = PaypalHelper::getToken();
			
			if($token_result['success']){
				$token = $token_result['access_token'];
			}else{
				return Response::json("failed",$token_result['status_code']);
			}

			$card_id = User::where('_id',Session::get('id'))->select('paypal_info')->first()['paypal_info']['card_id'];
			// Get secure token for transparent redirect
			$user_card_info = PaypalHelper::getUserVault($token,$card_id);
		}

		return View::make('ourscene.pay-ourscene', compact('user_card_info'));
	}

	/* Pay OurScene via auto-populated credit card information from vault */

	public function postPayToOurScene(){

		Input::merge(array_map('trim', Input::all()));

		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);
		
		$token_result = PaypalHelper::getToken();
		if($token_result['success']){
			$token = $token_result['access_token'];
		}else{
			return Redirect::back()->with('error', 'There was a problem autenticating with Paypal. Try again later.');
		}

		$paypal_payer_id	= $input['paypal-payer-id'];
		$paypal_card_id		= $input['paypal-card-id'];

		$payment_result = PaypalHelper::postPayToOurScene($token,$paypal_card_id,$paypal_payer_id);
		if ($payment_result['success']){

			$response = PaypalController::payOurSceneSuccess();

			if($response['success'])
				return Redirect::to($response['redirect_url'])->with('success', $response['message']);
			else
				return Redirect::back()->with('error', $response['message']);
		}else{
			return Redirect::back()->with('error', 'There was an error in processing your transaction. Please try again.');
		}
	}

	/* Pay OurScene via manual input of credit card information */

	public function postPayToOurSceneCredit(){

		Input::merge(array_map('trim', Input::all()));

		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);
		
		$token_result = PaypalHelper::getToken();
		if($token_result['success']){
			$token = $token_result['access_token'];
		}else{
			return Redirect::back()->with('error', 'There was a problem autenticating with Paypal. Try again later. Error:'.$token_result['status_code']);
		}


		$credit = array(
			"number" => str_replace("-","",$input['paypal-card-number']),
			"type" => $input['paypal-card-type'],
			"expire_month" => $input['paypal-month'],
			"expire_year" => $input['paypal-year'],
			// "cvv2" => $input['cvv2'],
		);

		$payment_result = PaypalHelper::postPayToOurSceneCredit($token, $credit);

		if($payment_result['success']){

			//successful payment

			$response = PaypalController::payOurSceneSuccess();

			if($response['success'])
				return Redirect::to($response['redirect_url'])->with('success', $response['message']);
			else
				return Redirect::back()->with('error', $response['message']);
		}
		else{
			//error in payment
			return Redirect::back()->with('error', $payment_result['message'])->with('card_error',$payment_result['message']);
			// return Redirect::back()->with('error', 'There was an error in processing your transaction. Please try again.');
		}
	}

	/* Pay OurScene via Paypal account > Endpoints */

	public function getPayMerchantSuccess(){

		//successful payment

		$response = PaypalController::payOurSceneSuccess();

		if($response['success'])
			return Redirect::to($response['redirect_url'])->with('success', $response['message']);
		else
			return Redirect::back()->with('error', $response['message']);
	}

	public function getPayMerchantCancel(){

		return Redirect::to(action('PaypalController@getPayOurscene'))->with('success', 'The previous transaction was cancelled. Please continue again to proceed.');
	}

	public function getPayMerchantError(){
		return "There was an error in processing your payment.";
	}

	/* Pay OurScene > Actions */

	public static function payOurSceneSuccess(){

		$response = array();

		if(! Session::has('pay_ourscene_action')){
			$response['success'] = false;
			$response['message'] = 'There is no pending action that will be completed after payment.';
		}

		$action = Session::get('pay_ourscene_action');

		$type = $action['type'];
		$input = $action['input'];

		switch($type){
			case 'create event':
				$response = EventController::paidCreateEvent($input);
				break;
			case 'confirm request for service':
				$response = ServiceController::paidConfirmRequestForService($action['service_id'], $input);
				break;
			case 'confirm request for performance':
				$response = ServiceController::paidConfirmRequestForPerformance($action['service_id'], $input);
				break;
			case 'request for performance':
				$response = ServiceController::paidRequestForPerformance($action['event_id'], $input);
				break;
			case 'request for service from edit event':
				$response = EventController::paidEditEvent($action['artists'], $action['event'], $action['venue']);
				break;
			default:
				$response['success'] = false;
				$response['message'] = 'The pending action type to be completed after payment is invalid.';
				break;
		}

		if($response['success']){

			//add payment to payment history
			Payment::addPaymentToPaymentHistory(
				Session::get('id'),
				new MongoDate()
			);
		}

		//delete pay ourscene transaction session
		Session::forget('pay_ourscene_action');

		return $response;
	}

	public static function payOurSceneCancelled(){}

	public static function payOurSceneError(){}

	/* Get users with Paypal account URL (for autocomplete) */

	public function getAutocompleteUsersWithPaypalAccount(){

		$term = Input::get('term');

		$results = [];

		$query = User::select('id', 'name', 'paypal_info.email')
			->usersWithPaypalAccount()
			->where('name', 'LIKE', '%'.$term.'%');

		if(Input::has('except_self'))
			$query->notUserId(Session::get('id'));

		$users = $query->get();

		foreach($users as $user)
			$results[] = ['user_id' => $user['id'], 'value' => $user['name']];

		return Response::json($results);
	}

	/* Unused functions */

	public function removePaymentAccount(){

		$user = User::find(Input::get('id'));
		$email = $user->paypal_info['email'];
		$user->paypal_info = [
			"email" => "",
			"card_id" => ""
		];
		$user->save();
		return Redirect::to('/settings#payments');
	}

	/* Paypal Money Transfer */

	public function getPayment(){

		$payments = Payment::where('sender_id', Session::get('id'))
			->orWhere('recipient_id', Session::get('id'))
			->orderBy('transaction_date', 'desc')
			->get();

		return View::make('ourscene.payment', compact('payments'));
	}

	public function postPaypalMoneyTransfer(){

		//trim and sanitize all inputs
		Input::merge(array_map('trim', Input::all()));
		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);

		$sender_user_id = Session::get('id');
		$receiver_user_id = $input['receiver_user_id'];
		$amount = $input['amount'];

		//get receiver email
		$receiver = User::select('paypal_info.email')
			->usersWithPaypalAccount()
			->userId($receiver_user_id)
			->first();

		if(!$receiver)
			return Redirect::back()->with("error", "Your requested receiver does not exist or does not have/provide a Paypal account yet.");

		$email = $receiver['paypal_info.email'];

		// Set up payment - Call Paypal API

		$response = json_decode(PaypalHelper::setUpMoneyTransfer($email, $amount), true);

		if($response['responseEnvelope']['ack'] != 'Success') //error in processing request
			return Redirect::back()->with("error", "There was an error in setting up the payment to Paypal.");

		// Redirect the user to PayPal for Authorization

		//get pay key
		$pay_key = $response['payKey'];

		//add paykey to Sessions
		Session::put('paypal_money_transfer', array(
				'pay_key' => $pay_key,
				'sender_user_id' => $sender_user_id,
				'recipient_user_id' => $receiver_user_id,
				'amount' => $amount
			));

		//redirect to authorization url
		$paypal_authorization_url = PaypalHelper::getAuthorizationURL($pay_key);
		return Redirect::away($paypal_authorization_url);
	}

	public function getUserAuthorizesMoneyTransfer(){

		if(Session::get('paypal_money_transfer') != null){
			$pay_key = Session::get('paypal_money_transfer')['pay_key'];

			$response = json_decode(PaypalHelper::getPaymentDetails($pay_key), true);

			if($response['responseEnvelope']['ack'] != 'Success') //error in processing request
				return Redirect::to('/payment')->with('error', 'You did not authorize your attempted payment.');

			//add payment

			Payment::addTransferToPaymentHistory(
				Session::get('paypal_money_transfer')['sender_user_id'],
				Session::get('paypal_money_transfer')['recipient_user_id'],
				Session::get('paypal_money_transfer')['amount'],
				new MongoDate(time()),
				$response
			);

			//destroy paypal money transfer session
			Session::forget('paypal_money_transfer');
		}

		return Redirect::to('/payment')->with('success', 'The payment was sucessfully processed.');
	}

	public function getUserCancelsMoneyTransfer(){

		return Redirect::to('/payment')->with('warning', 'The payment was sucessfully canceled.');
	}

}
