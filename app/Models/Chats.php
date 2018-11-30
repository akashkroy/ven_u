<?php namespace OurScene\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class Chats extends Eloquent {

	protected $connection = 'mongodb';

	protected $collection = 'chats';

}
