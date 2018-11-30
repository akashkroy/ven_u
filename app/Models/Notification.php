<?php namespace OurScene\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class Notification extends Eloquent {

	protected $connection = 'mongodb';

	protected $collection = 'notifications';

	public $timestamps = false;

	public function scopeSenderId($query, $id){

		return $query->where('sender_id', '=', $id);

	}

	public function scopeRecipientId($query, $id){

		return $query->where('recipient_id', '=', $id);

	}

	public function scopeEventId($query, $id){

		return $query->where('event_id', '=', $id)->first();

	}

	public function scopeIsApproved($query){

		return $query->where('status', '=', 'approved');

	}

	public function scopeIsDeclined($query){

		return $query->where('status', '=', 'declined');

	}

	public function scopeIsPending($query){

		return $query->where('status', '=', 'pending');

	}

	public function scopeIsRead($query){

		return $query->where('is_read', '=', true);

	}

	public function scopeIsNotRead($query){

		return $query->where('is_read', '=', false);

	}

}
