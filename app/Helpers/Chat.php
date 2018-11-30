<?php namespace OurScene\Helpers;

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
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use OurScene\Models\User;
use OurScene\Models\Message;
use Ratchet\Session\SessionProvider;

class Chat implements MessageComponentInterface {

  protected $clients;

  public function __construct() {
      $this->clients = new \SplObjectStorage;
  }

  public function onOpen(ConnectionInterface $conn) {
    $this->clients->attach($conn);
    echo "New connection! ({$conn->resourceId})\n";
  }

  public function onMessage(ConnectionInterface $from, $msg) {
    $response = json_decode($msg);

    if ($response->type == "message") {
      $user_id  = $response->user_id;
      $user     = User::find($user_id);
      $message  = Message::find($response->message_id);
      $conversation = array();
      $oldmessages = $message['messages'];
      $messsage_container;

      if($user->image !== null){
  		  $messsage_container['image'] = $user->image;
  	  }

      $messsage_container['message_id'] = $response->message_id;
  	  $messsage_container['sender_id'] = $user->id;
  	  $messsage_container['sender_name'] = $user->name;
  	  $messsage_container['body'] = $response->message;
  	  $messsage_container['time'] = new MongoDate();
      array_push($oldmessages,$messsage_container);
      $message['messages'] = $oldmessages;
      $msgresult;

      $old_status = $message->read_status;
      $new_status = array();
      foreach ($old_status as $status) {
          $status['status'] = "unread";
          $new_status[] = $status;
      }
      
      $message->read_status = $new_status;


      if ($message->save()) {
        $messsage_container['status'] = 'success';
        $msgresult = $messsage_container;
      }else{
        $msgresult = ["status" => 'failed',
          "body" => $response->message
        ];
      }

      //send the message to all the other clients except the one who sent.
      foreach ($this->clients as $client) {
          // if ($from !== $client) {
              $client->send(json_encode($msgresult));
          // }
      }
    
    }elseif ($response->type == "read") {

      $user_id = $response->user_id;

      $message = Message::find($response->message_id);

      $status = $message->read_status;

      for ($i=0; $i < count($status); $i++) {
        if ($status[$i]['participant'] == $user_id) {
          $status[$i]['last_read'] = $response->message_id;
          $status[$i]['status'] = "read";
        }
      }

      $message->read_status = $status;
      $message->save();

    }
  }

  public function onClose(ConnectionInterface $conn) {
    $this->clients->detach($conn);
     echo "Connection {$conn->resourceId} has disconnected\n";
  }

  public function onError(ConnectionInterface $conn, \Exception $e) {
    echo "An error has occurred: {$e->getMessage()}\n";
    $conn->close();
  }
}
