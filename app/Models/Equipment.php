<?php namespace OurScene\Models;

use Jenssegers\Mongodb\Model as Eloquent;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Equipment extends Eloquent {

	use SoftDeletes;

    protected $dates = ['deleted_at'];

	protected $connection = 'mongodb';

	protected $collection = 'equipment';

	public $timestamps = false;

	public function scopeIsDefault($query){

		return $query->where('type', '=', 'default');

	}

	public function scopeIsOthers($query){

		return $query->where('type', '=', 'others');

	}

	public function scopeUser($query, $user_id){

		return $query->where('user_id', '=', $user_id);
	}
}
