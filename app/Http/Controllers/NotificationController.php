<?php namespace OurScene\Http\Controllers;

use Session;
use View;
use Input;
use Redirect;
use Hash;
use App;

use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\Filesystem;

use OurScene\Models\Notification;
use OurScene\Models\User;

class NotificationController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Notification Controller
	|--------------------------------------------------------------------------
	|
	| This controller manages all notifications within OurScene
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth.login', ['except' => ['index', 'markAllAsRead']]);
	}
	
	/* Default page */

	public function index(){

		$user = User::find(Session::get('id'));

		if(empty($user))
			return View::make('404');
		else{
			$not_read = Notification::recipientId($user->id)->isNotRead()->get();
			$notifications = Notification::recipientId($user->id)->orderBy('sent_at', 'desc')->get();
			
			return View::make('ourscene.notifications')->with('notifications', $notifications)->with('not_read', $not_read);
		}

	}

	/* Mark all notifications as read */

	public function markAllAsRead(){

		$notifications = Notification::all();

		foreach($notifications as $notification){
			$notification->is_read = true;
			$notification->save();
		}

		return Redirect::to('/notifications')->with('success', 'All notifications are marked as read.');

	}

}
