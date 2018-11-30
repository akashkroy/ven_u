<?php namespace OurScene\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class Message extends Eloquent {

	protected $connection = 'mongodb';

	protected $collection = 'messages';

	public function scopeSenderId($query, $id){

		return $query->where('sender_id', '=', $id);

	}

	public function scopeRecipientId($query, $id){

		return $query->where('recipient_id', '=', $id);

	}

	public function scopeGetMessages($query, $id){

		$condition = array('participants' => array('$in' => array($id)));
		
		return $query->where($condition)->get();
	}

	public function scopeGetMessagesRequest($query, $userid, $threadid, $participants){
		$condition = array('_id' => $threadid,'participants' => array('$in' => array($userid)));
		return $query->where($condition)->first();
	}

	public static function scopeGetConversationWithParticipants($query,$participants){
		$participation_condition = array(
			'participants'	=> array('$size' => count($participants),'$all' => $participants)
		);
		return $query->where($participation_condition)->first();
	}

	public function scopeGetConversation($query, $participants){
		$participation_condition = array(
			'participants'	=> array('$size' => count($participants),'$all' => $participants)
		);
		return $query->where($participation_condition)->get();
	}

  // Get message using id
	public static function getMessagesByUserId($id){
		$all = Message::orderBy('updated_at','desc')->get();
		$query= [];
		foreach ($all as $result) {
			foreach ($result['participants'] as $deep_result) {
				if ($deep_result == $id) {
					array_push($query,$result);
					break;
				}
			}
		}
		return $query;
	}

	public function isReadByUser($user_id){
		$group_status = $this->read_status;

		foreach($group_status as $user_status){
			if($user_status['participant'] == $user_id && $user_status['status'] == "read"){
				return true;
			}
		}
		return false;
	}

	public static function getNumberOfUnreadConversation($user_id){
		$user_messages = self::getMessagesByUserId($user_id);
		$unread = 0;

		foreach ($user_messages as $key => $message) {
			if (!$message->isReadByUser($user_id)){
				$unread++;
			}
		}	
		return $unread;
	}

	public function getLastMessage(){
		$msg_count = count($this->messages);
		$message_index = $msg_count;
		if ( $msg_count > 0){
			$message_index--;
		}
		return $this->messages[$message_index];
	}

}
