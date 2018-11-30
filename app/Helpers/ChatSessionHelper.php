<?php


namespace OurScene\Helpers;

use OurScene\Models\User;
use OurScene\Models\Event;
use Session;

class ChatSessionHelper{

  function getCurrentSession(){

  	dd(Session::get('id'));
  }
}
