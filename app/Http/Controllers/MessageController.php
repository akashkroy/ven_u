<?php namespace OurScene\Http\Controllers;

use Session;
use View;
use Input;
use Redirect;
use Hash;
use App;
use MongoDate;
use Log;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\Filesystem\Filesystem;

use OurScene\Models\User;
use OurScene\Models\Message;
use OurScene\Models\Chats;

class MessageController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Message Controller
	|--------------------------------------------------------------------------
	|
	| This controller manages all private and group chat messages
	|
	*/

	/**
	 *
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth.login', ['except' => ['index', 'getUsers', 'getMessage', 'createMessage', 'groupChat']]);
	}

	/**
	 *
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	// Message view
	public function index(){
		
		if(Session::has('id')){
			$my_user_id = Session::get('id');
			$user_messages = Message::getMessagesByUserId($my_user_id,true);
			$conversations = $this->getMessagesOfUser($my_user_id,true);

			return View::make('messages.messages')->with('user_id', Session::get('id'))->with('conversations',$conversations);
		}
		else
			return View::make('404');
	}

	public function getLastMessageOfThreadFromDB($id){
		$message_index = count($result['messages']);
		if (count($result['messages']) > 0){
			$message_index--;
		}
	}

	

	public function getMessagesOfUser($id,$onlyHeaders=false){
		$user_messages = Message::getMessagesByUserId($id);
		$conversations = [];
		foreach ($user_messages as $key => $thread) {
			if($thread['title'] == ""){
				$title = "";
				foreach($thread['participants'] as $key => $participant ){
					if(count($thread['participants']) - 1 > $key ){
						$title .= User::where('_id',$participant)->select('name')->first()['name'] . ", ";
					}else{
						$title .= User::where('_id',$participant)->select('name')->first()['name'];
					}
				}
				$thread['title'] = $title;
			}
			$image = "";
			foreach($thread['participants'] as $key => $participant ){
				if( User::where('_id',$participant)->first()['image'] !== null && $participant != $id){
					$image = User::where('_id',$participant)->first()['image'];
					break;
				}
			}
			$thread['image'] = $image;

			if($onlyHeaders){
				$last_msg = $thread->getLastMessage();
				$last_msg['time'] = $last_msg['time']->sec;
				$thread['last_msg'] = $last_msg;
				$thread['is_read'] = $thread->isReadByUser($id);
				unset($thread['messages']);
				unset($thread['participants']);

				unset($thread['read_status']);
			}
			$conversations[] = $thread;
		}
		return $conversations;
	}

	public function getAjaxHeaderMessagesOfUser($id){
		$conversations = $this->getMessagesOfUser($id,true);
		return response()->json($conversations);
	}

	public function getAjaxNumberOfUnreadMessages(){
		$unread = Message::getNumberOfUnreadConversation(Session::get('id'));
		return $unread;
	}


	// Get messsage by ID
	public function getMessage($id){
		if(Session::has('id')){
			$messages = Message::where('_id',$id)->first();
			return View::make('messages.message')->with('conversation', $messages);
		}
		else
			return View::make('404');
	}

	// Get the conversation base from the participants
	public function getConversation(){
		if (Input::get('thread')) {
			$id = Input::get('thread');
			$messages = Message::where('_id',$id)->first();
			return response()->json($messages);
		}
		else{
			return response()->json(['data' => null]);
		}
	}


	// Reply or Start a message via AJAX
	public function createMessage(){
		$input = Input::all();
		$participants = explode(",",$input['participants']);

		// Get message based from the users
		$thread = Message::getConversationWithParticipants($participants);

		$message = $input['message'];
		$user = User::find(Session::get('id'));
		$messages = array(); // message to be passed

		if($user->image !== null){
			$messsage_container['image'] = $user->image;
		}

		$messsage_container['sender_id'] = $user->id;
		$messsage_container['sender_name'] = $user->name;
		$messsage_container['body'] = $message;
		$messsage_container['time'] = new MongoDate();


		if (count($thread->get()) < 1) { // create new message
			$thread = new Message;
			$thread->creator_id = $user->id;
			$thread->participants = $participants;
			$thread->messages = array($messsage_container);

			$title = "";
			foreach($participants as $key => $participant ){
				if(count($participants) - 1 > $key ){
					$title .= User::where('_id',$participant)->select('name')->first()['name'] . ", ";
				}else{
					$title .= User::where('_id',$participant)->select('name')->first()['name'];
				}
			}

			$thread->title= $title;

			$read_status_array = array();
			foreach ($participants as $item) {
				if($item == $user->id){
					array_push($read_status_array,[
						"participant" => $item,
						"status" => 'read'
					]);
				}
				else{
					array_push($read_status_array,[
						"participant" => $item,
						"status" => 'unread'
					]);
				}
			}
			$thread->read_status = $read_status_array;
		}
		else{																															// reply to existing message
			$oldmessages = $thread->messages;
			array_push($oldmessages,$messsage_container);
			$thread->messages = $oldmessages;
		}
		if ($thread->save()) {
			return response()->json(['status' => 'success', "thread" => $thread->id], 201); // save message and return as success json with success http code
		}
		else{
			return response()->json(['status' => 'error on saving message data'], 500); // return error http code
		}

	}


	// get conversation by ID
	public function getMessageConversationWithUser($id){
		$participants = array(Session::get('id'),$id);
		$thread = Message::getConversationWithParticipants($participants);

		 if (count($thread->get()) < 1) {
		 	return view('messages.create_message')->with('user', $id);
		 }else{
		 	return $this->getMessage($thread->_id);
		}
	}


	// get all conversation of the user
	public function checkConversation(){
		$input = Input::all();
		$input['participants'] = explode(",",$input['participants']);
		$thread = Message::getConversation($input['participants']);
		return response()->json(['data' => $thread]);
	}

	// Create new message view
	public function getNewMessage(){
		return view('messages.create_message')->with('user',null);
	}

	public function groupChat(){
		return Input::all();
	}
}
