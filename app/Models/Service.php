<?php namespace OurScene\Models;

use Jenssegers\Mongodb\Model as Eloquent;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Service extends Eloquent {

	protected $connection = 'mongodb';

	protected $collection = 'service';

	use SoftDeletes;
	protected $dates = ['deleted_at'];

	public $timestamps = false;

	public function scopeConfirmed($query){	

		return $query->where('status', "confirmed");
	}

	public function scopeRejected($query){	

		return $query->where('status', "rejected");
	}

	public function scopePending($query){	

		return $query->where('status', "pending");
	}

	public function scopePerformance($query){	

		return $query->where('type', "performance");
	}

	public function scopeService($query){	

		return $query->where('type', "service");
	}

	public function scopeServicesBySenderId($query, $sender_id){

		return $query->where('sender_id', $sender_id);
	}

	public function scopeServicesByReceiverId($query, $receiver_id){

		return $query->where('receiver_id', $receiver_id);
	}

	public function scopeServicesByEventId($query, $event_id){

		return $query->where('event_id', $event_id);
	}

	public function scopeServicesByArtistId($query, $artist_id){

		return $query->where('artist.id', $artist_id);
	}

	public static function getServices($event_id){
		// pending is for performance
		return Service::where('event_id', $event_id)->whereIn('status', ['draft', 'confirmed']);
	}

	public static function getPublicServices($event_id){
		return Service::where('event_id', $event_id)->where('status', '=', 'confirmed');
	}

	public static function getPendingRequestsCountFor($user_id){
		return	count(self::servicesByReceiverId($user_id)->pending()->get());
	}
	
}