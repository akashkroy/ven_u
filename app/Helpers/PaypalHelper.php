<?php

namespace OurScene\Helpers;

use Redirect;

use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\EBLBaseComponents\AddressType;
use PayPal\EBLBaseComponents\CreditCardDetailsType;
use PayPal\EBLBaseComponents\DoDirectPaymentRequestDetailsType;
use PayPal\EBLBaseComponents\PayerInfoType;
use PayPal\EBLBaseComponents\PaymentDetailsType;
use PayPal\EBLBaseComponents\PersonNameType;
use PayPal\PayPalAPI\DoDirectPaymentReq;
use PayPal\PayPalAPI\DoDirectPaymentRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use PayPal\Exception\PPConnectionException;

class PaypalHelper{


	public static function getToken(){

		// Get Token for Paypal Vault API
		$ch = curl_init();

		// header for curl requiest
		curl_setopt($ch, CURLOPT_URL, env("PAYPAL_REST_API_BASE_URL")."/v1/oauth2/token");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,  "grant_type=client_credentials");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_USERPWD, env("PAYPAL_CLIENT_ID"). ":" . env("PAYPAL_SECRET_KEY"));
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);

		$headers = array();
		$headers[] = "Accept: application/json";
		$headers[] = "Accept-Language: en_US";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
		    echo 'Error:' . curl_error($ch);
		}
		
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ($ch);
		$response_result = [];
		if ($httpcode == 200) {
			$response_result['access_token'] = json_decode($result)->access_token;
			$response_result['success'] = true;
		}else{
			$response_result['success'] = false;
			$response_result['status_code'] = $httpcode;
		}

		return $response_result;
	}

	public static function getPaymentDetails($pay_key){

		// Initialize input parameters

		$input_parameters = array(
				"payKey"		=>	$pay_key,
				"requestEnvelope"	=>	array(
						"errorLanguage"		=>	"en_US",	//language used to display errors
						"detailLevel"		=>	"ReturnAll"	//error detail level
					)
			);

		// Send request

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => env('PAYPAL_PAYMENT_DETAILS_URL'),
			CURLOPT_HTTPHEADER => array(

				//API credentials for the API Caller account
				'X-PAYPAL-SECURITY-USERID : '.getenv('PAYPAL_API_USER_ID'),
				'X-PAYPAL-SECURITY-PASSWORD : '.getenv('PAYPAL_API_PASSWORD'),
				'X-PAYPAL-SECURITY-SIGNATURE : '.getenv('PAYPAL_API_SIGNATURE'),

				//input and output formats
				'X-PAYPAL-REQUEST-DATA-FORMAT : JSON',
				'X-PAYPAL-RESPONSE-DATA-FORMAT : JSON',

				//application ID
				'X-PAYPAL-APPLICATION-ID : '.getenv('PAYPAL_APPLICATION_ID'),
			),
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => json_encode($input_parameters)
		));

		return curl_exec($curl);
	}

	public static function postPayToOurScene($token,$card_id,$payer_id){

		// Initialize user parameters
		$post_parameters = array(
			"intent" => "sale",
			"payer" => array(
				"payment_method" => "credit_card",
				"funding_instruments" => array(
					array(
						"credit_card_token" => array(
							"credit_card_id" => $card_id ,
	            			"payer_id" => $payer_id
						)
					)
				)
			),
			"transactions" => array(
				array(
					"amount" => array(
						"total" => "1",
						"currency" => "USD"
					),
					"description" => "Payment to Ourscene for events"
				)
			)
		);

		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => env("PAYPAL_REST_API_BASE_URL")."/v1/payments/payment" ,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSLVERSION => 6,
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => json_encode($post_parameters),
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
				"Authorization: Bearer " . $token
			)
		));
		$result = curl_exec($ch);

		if (curl_errno($ch)) {
				return false;
		}
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ($ch);

		if ($httpcode == 201) {
			return array('success'=>true);
		}else {
			return array('success'=>false, 'status_code' => $httpcode);
		}

		return array('result' => curl_exec($ch), 'status_code' =>$httpcode);
	}

	public static function postPayToOurSceneCredit($token,$credit){

		// Credit card information with app token parameters
		$post_parameters = array(
			"intent" => "sale",
			"payer" => array(
				"payment_method" => "credit_card",
				"funding_instruments" => array(
					array(
						"credit_card" => $credit
					)
				)
			),
			"transactions" => array(
				array(
					"amount" => array(
						"total" => "1",
						"currency" => "USD"
					),
					"description" => "This is the payment transaction description."
				)
			)
		);

		// cURL request
		$ch = curl_init();

		curl_setopt_array($ch, array(
			CURLOPT_URL => env("PAYPAL_REST_API_BASE_URL")."/v1/payments/payment" ,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSLVERSION => 6,
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => json_encode($post_parameters),
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
				"Authorization: Bearer " . $token
			)
		));
		$result = curl_exec($ch);


		// Send request
		if (curl_errno($ch)) {
		    // echo 'Error:' . curl_error($ch);
				return false;
		}

		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ($ch);
		if ($httpcode == 201) {
			# code...
			return array('success'=>true);
		}
		else {
			if(json_decode($result)->name=="VALIDATION_ERROR"){
				return array('success'=>false, 'status_code' => $httpcode,'message'=>"Invalid credit card.");
			}
			return array('success'=>false, 'status_code' => $httpcode,'message'=>"Transaction Error.");
		}
		return array('result' => curl_exec($ch), 'status_code' =>$httpcode);
	}


	public static function registerToVault($token,$info){

		// Register user to vault
		$ch = curl_init();

		// User paramaters with app token
		curl_setopt_array($ch, array(
			CURLOPT_URL => env("PAYPAL_REST_API_BASE_URL")."/v1/vault/credit-card" ,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSLVERSION => 6,
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => json_encode($info),
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
				"Authorization: Bearer " . $token
			)
		));
		$result = curl_exec($ch);

		if (curl_errno($ch)) {
		    // echo 'Error:' . curl_error($ch);
			return false;
		}

		// Validate request using http code of curl request
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ($ch);

		if ($httpcode == 201) {
			# code...
			return $result;
		}
		else {
			return false;
		}
	}

	public static function getUserVault($token,$card_id){
		// Get Vault info using app token and credit-card id from vault
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => env("PAYPAL_REST_API_BASE_URL")."/v1/vault/credit-cards/". $card_id,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSLVERSION => 6,
			CURLOPT_HTTPHEADER => array(
				"Accept: application/json",
				"Authorization: Bearer " . $token
			)
		));
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
		    echo 'Error:' . curl_error($ch);
		}
		curl_close ($ch);
		return json_decode($result);
	}


	//DoDirect Payments
	public static function getConfig()
	{
		$config = array(
			// values: 'sandbox' for testing
			//		   'live' for production
			'mode' => "live",
	        'log.LogEnabled' => true,
	        'log.FileName' => storage_path().'/PayPal.log',
	        'log.LogLevel' => 'FINE'
	
		);
		return $config;
	}
	
	// Creates a configuration array containing credentials and other required configuration parameters.
	public static function getAcctAndConfig()
	{
		$config = array(
				// Signature Credential
				"acct1.UserName" => env("PAYPAL_API_USER_ID"),
				"acct1.Password" => env("PAYPAL_API_PASSWORD"),
				"acct1.Signature" => env("PAYPAL_API_SIGNATURE"),		
				);
		
		return array_merge($config, self::getConfig());
	}

	public static function verifyCreditCard($card_details){

		$address = new AddressType();
		$address->Name = $card_details['first_name'] . " " . $card_details['last_name'];
		$address->Street1 = "Street";
		$address->Street2 = "Street2";
		$address->CityName = "Los Angeles";
		$address->StateOrProvince = "State";
		$address->PostalCode = "90001";
		$address->Country = "CA";
		
		$paymentDetails = new PaymentDetailsType();
		$paymentDetails->ShipToAddress = $address;

		$paymentDetails->OrderTotal = new BasicAmountType('USD', '0');

		$personName = new PersonNameType();
		$personName->FirstName = $card_details['first_name'];
		$personName->LastName = $card_details['last_name'];
		
		$payer = new PayerInfoType();
		$payer->PayerName = $personName;
		$payer->Address = $address;
		$payer->PayerCountry = "CA";

		$cardDetails = new CreditCardDetailsType();
		$cardDetails->CreditCardNumber = $card_details['number'];
		$cardDetails->CreditCardType = $card_details['type'];
		$cardDetails->ExpMonth = $card_details['expire_month'];
		$cardDetails->ExpYear = $card_details['expire_year'];
		$cardDetails->CVV2 = $card_details['cvv2'];
		$cardDetails->CardOwner = $payer;

		$ddReqDetails = new DoDirectPaymentRequestDetailsType();
		$ddReqDetails->CreditCard = $cardDetails;
		$ddReqDetails->PaymentDetails = $paymentDetails;
		$ddReqDetails->PaymentAction = "Authorization";

		$doDirectPaymentReq = new DoDirectPaymentReq();
		$doDirectPaymentReq->DoDirectPaymentRequest = new DoDirectPaymentRequestType($ddReqDetails);

		$paypalService = new PayPalAPIInterfaceServiceService(self::getAcctAndConfig());
		try {
			/* wrap API method calls on the service object with a try catch */
			$doDirectPaymentResponse = $paypalService->DoDirectPayment($doDirectPaymentReq);
		} catch (PPConnectionException $pce) {
		    // Don't spit out errors or use "exit" like this in production code
		    return false;
		    exit;
		}catch (Exception $ex) {
			return false;
			exit;
		}

		if(isset($doDirectPaymentResponse)) {
			return $doDirectPaymentResponse;
		}
	}

	//Paypal Money transfer to other user

	public static function getAuthorizationURL($pay_key){
		return env('PAYPAL_AUTHORIZATION_BASE_URL').'?cmd=_ap-payment&paykey='.$pay_key;
	}

	public static function getAuthorizationURLWithPreapprovalKey($preapproval_key){
		return env('PAYPAL_AUTHORIZATION_BASE_URL').'?cmd=_ap-preapproval&preapprovalkey='.$preapproval_key;
	}

	public static function setUpMoneyTransfer($receiver_paypal_email, $amount){

		// Initialize input parameters

		$input_parameters = array(
				"actionType"		=>	"PAY",
				"currencyCode"		=>	env('PAYPAL_CURRENCY_CODE'),	//payment currency code
				"receiverList"		=>
					array(
						"receiver"		=>
							array(
								array(
									"amount"	=> $amount,	//payment amount
									"email"		=> $receiver_paypal_email	//payment Receiver's email address
								)
							)
					),
				"returnUrl"			=>	action('PaypalController@getUserAuthorizesMoneyTransfer'),	//where to redirect the Sender following a successful payment approval
				"cancelUrl"			=>	action('PaypalController@getUserCancelsMoneyTransfer'),	//where to redirect the Sender following a canceled payment
				"requestEnvelope"	=>	array(
						"errorLanguage"		=>	"en_US",	//language used to display errors
						"detailLevel"		=>	"ReturnAll"	//error detail level
					)
			);

		// Send request

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => env('PAYPAL_ADAPTIVE_PAYMENT_URL'),
			CURLOPT_HTTPHEADER => array(

				//API credentials for the API Caller account
				'X-PAYPAL-SECURITY-USERID : '.getenv('PAYPAL_API_USER_ID'),
				'X-PAYPAL-SECURITY-PASSWORD : '.getenv('PAYPAL_API_PASSWORD'),
				'X-PAYPAL-SECURITY-SIGNATURE : '.getenv('PAYPAL_API_SIGNATURE'),

				//input and output formats
				'X-PAYPAL-REQUEST-DATA-FORMAT : JSON',
				'X-PAYPAL-RESPONSE-DATA-FORMAT : JSON',

				//application ID
				'X-PAYPAL-APPLICATION-ID : '.getenv('PAYPAL_APPLICATION_ID'),
			),
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => json_encode($input_parameters)
		));

		return curl_exec($curl);
	}
}
