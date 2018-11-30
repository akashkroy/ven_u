<?php namespace OurScene\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class Materials extends Eloquent {

	protected $connection = 'mongodb';

	protected $collection = 'materials';

	protected $fillable = ['user_id', 'order', 'title', 'url'];

	public $timestamps = false;

	public function scopeUserId($query, $id){

		return $query->where('user_id', '=', $id);

	}

}
