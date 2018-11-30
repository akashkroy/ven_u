<?php namespace OurScene\Models;

use Jenssegers\Mongodb\Model as Eloquent;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Promotion extends Eloquent {

	protected $connection = 'mongodb';

	protected $collection = 'promotion';

	use SoftDeletes;
	protected $dates = ['deleted_at'];

	public $timestamps = false;

	public static function getPromotionsByUserId($id, $start_datetime=null, $end_datetime=null){

		if($start_datetime!=null && $end_datetime!=null){

			$condition = array(
				'$and'	=> array(
					array(
						'start_datetime' => array(
							'$gt'	=>	$start_datetime,
							'$lt'	=>	$end_datetime
						)
					),
					array(
						'user_id'	=> $id
					)
				)
			);

			return Promotion::where($condition);
		}
		else
			return Promotion::where('user_id', $id);
	}

}
