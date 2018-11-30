<?php namespace OurScene\Models;

use Jenssegers\Mongodb\Model as Eloquent;
use OurScene\Models\User;

class Payment extends Eloquent {

	//
	protected $connection = 'mongodb';

	protected $collection = 'payment';

	public $timestamps = false;

	protected $appends = array('sender_name', 'recipient_name');

	public function getSenderNameAttribute(){

		$sender = User::find($this->sender_id);
		
		if($sender)
			return $sender->name;

		return '';
    }

    public function getRecipientNameAttribute(){

		if($this->recipient_id){
			$recipient = User::find($this->recipient_id);
		
			if($recipient)
				return $recipient->name;	
		}

		return 'OurScene';
    }

    public static function addTransferToPaymentHistory($sender_id, $recipient_id, $amount, $transaction_date, $paypal_response){

    	$payment = new Payment;

		$payment->type = 'transfer';
		$payment->sender_id = $sender_id;
		$payment->recipient_id = $recipient_id;
		$payment->amount = $amount;
		$payment->transaction_date = $transaction_date;
		$payment->paypal_response = $paypal_response;
		$payment->save();
    }

    public static function addPaymentToPaymentHistory($sender_id, $transaction_date){

    	$payment = new Payment;

    	$payment->type = 'payment';
		$payment->sender_id = $sender_id;
		$payment->amount = Payment::getRequestPayment();
		$payment->transaction_date = $transaction_date;
		$payment->save();
    }

    public static function getRequestPayment(){

    	return 1.00;
    }
}