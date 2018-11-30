<?php 

namespace OurScene\Http\Controllers;

use Log;
use Input;
use Session;
use Response;
use MongoDate;
use Mail;
use Redirect;

use OurScene\Models\Notification;
use OurScene\Models\Service;
use OurScene\Models\Event;
use OurScene\Models\User;
use OurScene\Models\Equipment;

use OurScene\Http\Requests;
use OurScene\Http\Controllers\Controller;

use Illuminate\Http\Request;

use OurScene\Helpers\EmailSender;
use OurScene\Helpers\DatetimeUtils; 

class ServiceController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function __construct(){

		$this->middleware('auth.login');
		$this->middleware('auth.venue',
			['only' => 
				['postConfirmRequestForPerformance', 'getRejectRequestForPerformance', 'getCancelRequestForService']
			]
		);
		$this->middleware('auth.artist',
			['only' => 
				['postRequestForPerformance', 'postConfirmRequestForService', 'getRejectRequestForService']
			]
		);
	}


	/* Root */

	public function getIndex(){
		
	}

	/* Request for performance */

	public function postRequestForPerformance($id){

		//get event
		$event = Event::find($id);

		if(!$event)
			abort(404);

		//get artist
		$artist = User::artists()->find(Session::get('id'));

		if(!$artist)
			abort(404);

		//get venue
		$venue = User::venues()->find($event->venue['id']);

		if(!$venue)
			abort(404);

		//trim and sanitize all inputs
		Input::merge(array_map_recursive('trim', Input::all()));
		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);

		//get and format inputs

		$user_id = Session::get('id');
		$user_name = Session::get('name');
		$user_type = Session::get('user_type');

		$start_date = $input['start_date'];
		$end_date = $input['end_date'];

		$start_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['start_time']);
		$end_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['end_time']);

		$start_datetime = new MongoDate(strtotime($start_date.$start_time));
		$end_datetime = new MongoDate(strtotime($end_date.$end_time));		

		$start_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($start_datetime);
		$end_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($end_datetime);

		//check permissions

		if(DatetimeUtils::datetimeGreaterThan(new MongoDate(), $event->end_datetime))
			return Redirect::back()->with('error', 'Sorry, you cannot request a performance in a past event.');

		//validate inputs

		$errors = [];

		if(DatetimeUtils::datetimeGreaterThan($start_datetime, $end_datetime)){
			$errors[] = "The start datetime should be earlier than the end datetime.";
		}

		if(! DatetimeUtils::datetimeInRange($event->start_datetime, $event->end_datetime, $start_datetime)
			|| ! DatetimeUtils::datetimeInRange($event->start_datetime, $event->end_datetime, $end_datetime)){
			$errors[] = "The performance should occur only in the event duration.";
		}

		if(count($errors)){
			$response['error'] = $errors;
			return Redirect::back()->with('success',$errors[0]);
			// return $response;
		}

		//get equipments

		$equipments = [];

		if(isset($input['equipments'])){
			$equipment_ids = $input['equipments'];

			foreach($equipment_ids as $equipment_id){
				
				$equipment = Equipment::find($equipment_id);
				
				if($equipment){
					
					//add to equipments

					$equipments[] = array(
						'user_id' => $equipment['user_id'],
						'equipment_id' => $equipment_id,
						'name' => $equipment['name'],
						'owner' => $equipment['owner'],
						'qty' => $equipment['qty'],
						'inclusion' => $equipment['inclusion']
					);
				}
			}
		}

		//reinitialize pay ourscene transaction session
		
		Session::forget('pay_ourscene_action');

		$pay_ourscene_action = array(
			'type'			=> 'request for performance',
			'event_id'	=> $id,
			'input'			=> $input,
			'before_payment_message'	=> 'Request for performance'
		);

		Session::put('pay_ourscene_action', $pay_ourscene_action);

		//redirect to payment
		return Redirect::to(action('PaypalController@getPayOurscene'));
	}

	public static function paidRequestForPerformance($id, $input){

		//get event
		$event = Event::find($id);

		if(!$event)
			abort(404);

		//get artist
		$artist = User::artists()->find(Session::get('id'));

		if(!$artist)
			abort(404);

		//get venue
		$venue = User::venues()->find($event->venue['id']);

		if(!$venue)
			abort(404);

		//get and format inputs

		$user_id = Session::get('id');
		$user_name = Session::get('name');
		$user_type = Session::get('user_type');

		$start_date = $input['start_date'];
		$end_date = $input['end_date'];

		$start_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['start_time']);
		$end_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['end_time']);

		$start_datetime = new MongoDate(strtotime($start_date.$start_time));
		$end_datetime = new MongoDate(strtotime($end_date.$end_time));

		$start_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($start_datetime);
		$end_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($end_datetime);

		//get equipments

		$equipments = [];

		if(isset($input['equipments'])){
			$equipment_ids = $input['equipments'];

			foreach($equipment_ids as $equipment_id){
				
				$equipment = Equipment::find($equipment_id);
				
				if($equipment){
					
					//add to equipments

					$equipments[] = array(
						'user_id' => $equipment['user_id'],
						'equipment_id' => $equipment_id,
						'name' => $equipment['name'],
						'owner' => $equipment['owner'],
						'qty' => $equipment['qty'],
						'inclusion' => $equipment['inclusion']
					);
				}
			}
		}

		//create service

		$service = new Service;
			
		$service->type = 'performance';
		$service->event_id = $event->_id;
		$service->sender_id = $user_id;
		$service->receiver_id = $event->venue['id'];
		$service->artist = array(
			'id'	=> $user_id,
			'name'	=> $user_name
		);
		$service->start_datetime = $start_datetime;
		$service->end_datetime = $end_datetime;
		$service->status = 'pending';
		$service->request_date = new MongoDate();
		$service->payment_status = 'unpaid';
		$service->equipments = $equipments;

		$service->save();

		//send email to venue
		EmailSender::requestForPerformance($event, $service, $venue, $artist);

		$response['success'] = true;
		$response['redirect_url'] = action('EventController@getEvent', array('id' => $id));
		$response['message'] = 'Your request for performance was succesfully submitted.';

		return $response;
	}

	/* Confirm service (request for performance) */

	public function postConfirmRequestForPerformance($id){

		//trim and sanitize all inputs
		Input::merge(array_map_recursive('trim', Input::all()));
		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);

		//get and format inputs

		$user_id = Session::get('id');
		$user_name = Session::get('name');
		$user_type = Session::get('user_type');

		$start_date = $input['start_date'];
		$end_date = $input['end_date'];

		$start_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['start_time']);
		$end_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['end_time']);


		$start_datetime = new MongoDate(strtotime($start_date.$start_time));
		$end_datetime = new MongoDate(strtotime($end_date.$end_time));

		$start_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($start_datetime);
		$end_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($end_datetime);

		//get service

		$service = Service::performance()->find($id);

		if(!$service)
			abort(404);

		//get event
		$event = Event::find($service->event_id);

		if(!$event)
			abort(404);

		//get artist
		$artist = User::artists()->find($service['artist.id']);
		
		if(!$artist)
			abort(404);

		//get venue
		$venue = User::venues()->find($event->venue['id']);

		if(!$venue)
			abort(404);

		//check permissions

		if(Session::get('id') != $event['venue']['id'])
			abort(401);

		if($service->status == 'confirmed')
			return Redirect::back()->with('error', 'Sorry, this request for performance was already confirmed.');

		if($service->status == 'rejected')
			return Redirect::back()->with('error', 'Sorry, this request for performance was already rejected.');

		if($service->status == 'cancelled')
			return Redirect::back()->with('error', 'Sorry, this request for performance was already cancelled.');

		//validate inputs

		$errors = [];

		if(DatetimeUtils::datetimeGreaterThan($start_datetime, $end_datetime)){
			$errors[] = "The start datetime should be earlier than the end datetime.";
		}

		if(! DatetimeUtils::datetimeInRange($event->start_datetime, $event->end_datetime, $start_datetime)
			|| ! DatetimeUtils::datetimeInRange($event->start_datetime, $event->end_datetime, $end_datetime)){
			$errors[] = "The performance should occur only in the event duration.";
		}

		if(count($errors)){
			$response['error'] = $errors;
			return Redirect::back()->with('success',$errors[0]);
			// return $response;
		}

		if($event->user_id == Session::get('id')){
			$response = ServiceController::paidConfirmRequestForPerformance($id, $input);

			return Redirect::to($response['redirect_url'])->with('success', $response['message']);
		}
		else{

			//reinitialize pay ourscene transaction session
			
			Session::forget('pay_ourscene_action');

			$pay_ourscene_action = array(
				'type'			=> 'confirm request for performance',
				'service_id'	=> $id,
				'input'			=> $input,
				'before_payment_message'	=> 'Accept request for performance'
			);

			Session::put('pay_ourscene_action', $pay_ourscene_action);

			//redirect to payment
			return Redirect::to(action('PaypalController@getPayOurscene'));
		}
	}

	public static function paidConfirmRequestForPerformance($id, $input){

		$response = array();
		
		$now = new MongoDate();

		//get and format inputs

		$user_id = Session::get('id');
		$user_name = Session::get('name');
		$user_type = Session::get('user_type');

		$start_date = $input['start_date'];
		$end_date = $input['end_date'];
		

		$start_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['start_time']);
		$end_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['end_time']);

		$start_datetime = new MongoDate(strtotime($start_date.$start_time));
		$end_datetime = new MongoDate(strtotime($end_date.$end_time));

		$start_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($start_datetime);
		$end_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($end_datetime);

		//get service

		$service = Service::performance()->find($id);

		if(!$service)
			abort(404);

		//get event
		$event = Event::find($service->event_id);

		if(!$event)
			abort(404);

		//get artist
		$artist = User::artists()->find($service['artist.id']);
		
		if(!$artist)
			abort(404);

		//get venue
		$venue = User::venues()->find($event->venue['id']);

		if(!$venue)
			abort(404);
			
		//update service

		$service->status = 'confirmed';
		$service->confirmation_date = $now;
		$service->start_datetime = $start_datetime;
		$service->end_datetime = $end_datetime;

		$service->save();

		//update event

		if($event->status == 'pending'){	
			$event->status = 'confirmed';
			$event->confirmation_date = $now;
		}

		//add artist to event services line up

		$services_lineup = $event->services_lineup;

		array_push($services_lineup, array(
			"service_id" => $service->_id,
			"artist_id" => $artist->_id,
			"artist_name" => $artist->name,
			"start_datetime" => $start_datetime,
			"end_datetime" => $end_datetime,
			"equipments" => $service->equipments,
		));

		$event->services_lineup = $services_lineup;

		$event->save();

		//send email to venue
		EmailSender::confirmRequestForPerformance($event, $service, $venue, $artist);
		
		$response['success'] = true;
		$response['redirect_url'] = action('EventController@getEvent', ['id' => $service->event_id]);
		$response['message'] = 'Request for performance was successfully confirmed.';

		return $response;
	}

	/* Reject service (request for performance) */

	public function getRejectRequestForPerformance($id){

		//get service

		$service = Service::performance()->find($id);

		if(!$service)
			abort(404);

		//get event
		$event = Event::find($service->event_id);

		if(!$event)
			abort(404);

		//get artist
		$artist = User::artists()->find($service['artist.id']);
		
		if(!$artist)
			abort(404);

		//get venue
		$venue = User::venues()->find($event->venue['id']);

		if(!$venue)
			abort(404);

		//check permissions

		if(Session::get('id') != $event['venue']['id'])
			abort(401);

		if($service->status == 'confirmed')
			return Redirect::back()->with('error', 'Sorry, this request for performance was already confirmed.');

		if($service->status == 'rejected')
			return Redirect::back()->with('error', 'Sorry, this request for performance was already rejected.');

		$now = new MongoDate();
		
		//update service

		$service->status = 'rejected';
		$service->rejection_date = $now;

		$service->save();

		if($event->status == 'pending'){
			
			//update event

			$event->status = 'rejected';
			$event->rejection_date = $now;

			$event->save();
		}

		//send email to venue
		EmailSender::rejectRequestForPerformance($event, $service, $venue, $artist);
		
		return Redirect::back()->with('success', 'Request for performance was successfully rejected.');
	}

	/* Cancel service (request for performance) */

	public function getCancelRequestForPerformance($id){

		//get service

		$service = Service::performance()->find($id);

		if(!$service)
			abort(404);

		//get event
		$event = Event::find($service->event_id);

		if(!$event)
			abort(404);

		//get artist
		$artist = User::artists()->find($service['artist.id']);
		
		if(!$artist)
			abort(404);

		//get venue
		$venue = User::venues()->find($event->venue['id']);

		if(!$venue)
			abort(404);

		//check permissions

		if(Session::get('id') != $service['sender_id'])
			abort(401);

		if($service->status == 'confirmed')
			return Redirect::back()->with('error', 'Sorry, this request for performance was already confirmed.');

		if($service->status == 'rejected')
			return Redirect::back()->with('error', 'Sorry, this request for performance was already rejected.');

		if($service->status == 'cancelled')
			return Redirect::back()->with('error', 'Sorry, this request for performance was already cancelled.');

		$now = new MongoDate();
		
		//update service

		$service->status = 'cancelled';
		$service->cancellation_date = $now;

		$service->save();

		//update event

		if(Session::get('id') == $event->user_id && $event->status == 'pending'){
			$event->status = 'cancelled';
			$event->cancellation_date = $now;

			$event->save();
		}

		//send email to artist
		EmailSender::cancelRequestForPerformance($event, $service, $venue, $artist);
		
		return Redirect::back()->with('success', 'Request for performance was successfully cancelled.');
	}

	/* Confirm service (request for service) */

	public function postConfirmRequestForService($id){

		//trim and sanitize all inputs
		Input::merge(array_map_recursive('trim', Input::all()));
		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);

		//get service

		$service = Service::service()->find($id);

		if(!$service)
			abort(404);

		//get event
		$event = Event::find($service->event_id);

		if(!$event)
			abort(404);

		//get artist
		$artist = User::artists()->find($service['artist.id']);
		
		if(!$artist)
			abort(404);

		//get venue
		$venue = User::venues()->find($event->venue['id']);

		//get equipments

		$equipments = [];

		if(isset($input['equipments'])){
			$equipment_ids = $input['equipments'];

			foreach($equipment_ids as $equipment_id){
				
				$equipment = Equipment::find($equipment_id);
				
				if($equipment){
					
					//add to equipments

					$equipments[] = array(
						'user_id' => $equipment['user_id'],
						'equipment_id' => $equipment_id,
						'name' => $equipment['name'],
						'owner' => $equipment['owner'],
						'qty' => $equipment['qty'],
						'inclusion' => $equipment['inclusion']
					);
				}
			}
		}

		//check permissions

		if(Session::get('id') != $service['receiver_id'])
			abort(401);

		if($service->status == 'confirmed')
			return Redirect::back()->with('error', 'Sorry, this request for service was already confirmed.');

		if($service->status == 'rejected')
			return Redirect::back()->with('error', 'Sorry, this request for service was already rejected.');

		if($service->status == 'cancelled')
			return Redirect::back()->with('error', 'Sorry, this request for service was already cancelled.');
			
		//reinitialize pay ourscene transaction session
		
		Session::forget('pay_ourscene_action');

		$pay_ourscene_action = array(
			'type'			=> 'confirm request for service',
			'service_id'	=> $id,
			'input'			=> $input,
			'before_payment_message'	=> 'Accept request of service'
		);

		Session::put('pay_ourscene_action', $pay_ourscene_action);

		//redirect to payment
		return Redirect::to(action('PaypalController@getPayOurscene'));
	}

	public static function paidConfirmRequestForService($id, $input){

		$response = array();
		
		$now = new MongoDate();

		//get service

		$service = Service::service()->find($id);

		if(!$service)
			abort(404);

		//get event
		$event = Event::find($service->event_id);

		if(!$event)
			abort(404);

		//get artist
		$artist = User::artists()->find($service['artist.id']);
		
		if(!$artist)
			abort(404);

		//get venue
		$venue = User::venues()->find($event->venue['id']);

		//get equipments

		$equipments = [];

		if(isset($input['equipments'])){
			$equipment_ids = $input['equipments'];

			foreach($equipment_ids as $equipment_id){
				
				$equipment = Equipment::find($equipment_id);
				
				if($equipment){
					
					//add to equipments

					$equipments[] = array(
						'user_id' => $equipment['user_id'],
						'equipment_id' => $equipment_id,
						'name' => $equipment['name'],
						'owner' => $equipment['owner'],
						'qty' => $equipment['qty'],
						'inclusion' => $equipment['inclusion']
					);
				}
			}
		}

		//update service

		$service->status = 'confirmed';
		$service->confirmation_date = $now;

		$service->save();

		//add artist to event services line up

		$services_lineup = $event->services_lineup;

		array_push($services_lineup, array(
			"service_id" => $service->_id,
			"artist_id" => $artist->_id,
			"artist_name" => $artist->name,
			"start_datetime" => $service->start_datetime,
			"end_datetime" => $service->end_datetime,
			"equipments" => $equipments,
		));

		$event->services_lineup = $services_lineup;

		$event->save();

		//send email to venue
		EmailSender::confirmRequestForService($event, $service, $venue, $artist);

		$response['success'] = true;
		$response['redirect_url'] = action('EventController@getEvent', ['id' => $service->event_id]);
		$response['message'] = 'Request for service was successfully confirmed.';

		return $response;
	}

	/* Reject service (request for service) */

	public function getRejectRequestForService($id){

		//get service

		$service = Service::service()->find($id);

		if(!$service)
			abort(404);

		//get event
		$event = Event::find($service->event_id);

		if(!$event)
			abort(404);

		//get artist
		$artist = User::artists()->find($service['artist.id']);
		
		if(!$artist)
			abort(404);

		//get venue
		$venue = User::venues()->find($event->venue['id']);

		if(!$venue)
			abort(404);

		//check permissions

		if(Session::get('id') != $service['receiver_id'])
			abort(401);

		if($service->status == 'confirmed')
			return Redirect::back()->with('error', 'Sorry, this request for service was already confirmed.');

		if($service->status == 'rejected')
			return Redirect::back()->with('error', 'Sorry, this request for service was already rejected.');

		if($service->status == 'cancelled')
			return Redirect::back()->with('error', 'Sorry, this request for service was already cancelled.');

		$now = new MongoDate();
		
		//update service

		$service->status = 'rejected';
		$service->rejection_date = $now;

		$service->save();

		//send email to venue
		EmailSender::rejectRequestForService($event, $service, $venue, $artist);
		
		return Redirect::back()->with('success', 'Request for service was successfully rejected.');
	}

	/* Cancel service (request for service) */

	public function getCancelRequestForService($id){
		
		//get service

		$service = Service::service()->find($id);

		if(!$service)
			abort(404);

		//get event
		$event = Event::find($service->event_id);

		if(!$event)
			abort(404);

		//get artist
		$artist = User::artists()->find($service['artist.id']);
		
		if(!$artist)
			abort(404);

		//get venue
		$venue = User::venues()->find($event->venue['id']);

		if(!$venue)
			abort(404);

		//check permissions

		if(Session::get('id') != $service['sender_id'])
			abort(401);

		if($service->status == 'confirmed')
			return Redirect::back()->with('error', 'Sorry, this request for service was already confirmed.');

		if($service->status == 'rejected')
			return Redirect::back()->with('error', 'Sorry, this request for service was already rejected.');

		if($service->status == 'cancelled')
			return Redirect::back()->with('error', 'Sorry, this request for service was already cancelled.');

		$now = new MongoDate();
		
		//update service

		$service->status = 'cancelled';
		$service->cancellation_date = $now;

		$service->save();

		//send email to artist
		EmailSender::cancelRequestForService($event, $service, $venue, $artist);
		
		return Redirect::back()->with('success', 'Request for service was successfully cancelled.');
	}

	/* Delete service */

	public function getDeleteService($id){

		//get service

		$service = Service::performance()->find($id);

		if(!$service)
			abort(404);

		//get event
		$event = Event::find($service->event_id);

		if(!$event)
			abort(404);

		//check permissions

		if(Session::get('id') != $service->sender_id)
			abort(401);

		if($service->status == 'confirmed')
			return Redirect::back()->with('error', 'Sorry, this request was already confirmed.');
		
		//delete service
		$service->delete();

		if(Session::get('id') == $event->user_id && $event->status != 'confirmed')
			$event->delete();

		return Redirect::to(action('EventController@getMyEventsEvents'))->with('success', 'Your request was successfully deleted.');
	}

	/* Unused functions */

	public function postAjaxCreateService(){

		// Fetch input
		$input = Input::all();

		if(isset($input['artist_id']))
			$artist = User::find($input['artist_id']);
		else{
			$data['error'] = true;
			return $data;
		}

		// Parsing date and time into MongoDate
		$start_date = $input['start_date'];
		// $start_time = $input['start_time'];

		$end_date = $input['end_date'];
		// $end_time = $input['end_time'];

		$start_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['start_time']);
		$end_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['end_time']);

		$start_datetime = new MongoDate(strtotime($start_date . $start_time));
		$end_datetime = new MongoDate(strtotime($end_date . $end_time));
		$date_requested = new MongoDate();

		$m_start_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($start_datetime);
		$m_end_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($end_datetime);
		$m_date_requested = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($date_requested);

		if (strtotime($start_date . $start_time) > strtotime($end_date . $end_time)){
			$data['error'] = "Invalid Time";
			return $data;
		}

		// Create new Service
		$service = new Service;
		$service->artist_id = $input['artist_id'];
		$service->artist_name = $artist['name'];
		$service->start_datetime = $m_start_datetime;
		$service->end_datetime = $m_end_datetime;
		$service->status = 'draft';
		$service->date_requested = $m_date_requested;
		$service->date_confirmed = '';
		$service->price = $input['price'];
		$service->payment_status = 'unpaid';

		$service->save();

		// Ready response for return

		return Response::json($service);

	}

	public function postAjaxRequestPerformance(){

		// Fetch input
		$input = Input::all();

		$artist = User::find($input['artist_id']);
		// Parsing date and time into MongoDate
		$start_date = $input['start_date'];
		// $start_time = $input['start_time'];

		$end_date = $input['end_date'];
		// $end_time = $input['end_time'];

		$start_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['start_time']);
		$end_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['end_time']);

		$start_datetime = new MongoDate(strtotime($start_date . $start_time));
		$end_datetime = new MongoDate(strtotime($end_date . $end_time));
		$date_requested = new MongoDate();

		$m_start_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($start_datetime);
		$m_end_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($end_datetime);
		$m_date_requested = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($date_requested);

		// Create new Performance(Service)
		$service = new Service;
		$service->event_id = $input['event_id'];
		$service->artist_id = $input['artist_id'];
		$service->artist_name = $artist['name'];
		$service->start_datetime = $m_start_datetime;
		$service->end_datetime = $m_end_datetime;
		$service->status = 'pending';
		$service->date_requested = $m_date_requested;
		$service->date_confirmed = '';
		$service->price = $input['price'];
		$service->payment_status = 'unpaid';

		$service->save();

		// Save service to event
		$event = Events::find($input['event_id']);
		$event_service = $event->service_lineup;

		$service_container = array();
		
		if(isset($event_service)){
			foreach ($event_service as $_service) {
				array_push($service_container, $_service);
			}
		}

		$service_brief['service_id'] = $service->id;
		$service_brief['artist_id'] = $input['artist_id'];
		$service_brief['artist_name'] = $artist['name'];
		$service_brief['status'] = 'pending';
		$service_brief['start_datetime'] = $m_start_datetime;
		$service_brief['end_datetime'] = $m_end_datetime;
		$service_brief['price'] = $input['price'];
		$service_brief['payment_status'] = 'unpaid';
		$service_brief['date_confirmed'] = '';		
		$service_brief['date_requested'] = $m_date_requested;

		$service_holder['service'] = $service_brief;

		array_push($service_container, $service_holder);

		$event->service_lineup = $service_container;
		$event->save();

		// Create request performance notification
		$event = Events::find($service->event_id);
		$creator = User::find($event->creator);
		$notification = new Notification;
		$notification->sender_id = $service->artist_id;
		$notification->sender_name = $service->artist_name;
		$notification->recipient_id = $creator->id;
		$notification->recipient_name = $creator->name;
		$notification->event_id = $event->id;
		$notification->title = $event->title;
		$notification->status = 'pending';
		$notification->type = 'performance';
		$notification->body = $service->artist_name.' requested to '.$creator->name.' to perform at '.$event->title;
		$notification->sent_at = new MongoDate();
		$notification->is_read = false;
		$notification->save();

		// Email venue for the performance
		$data['artist'] = User::find($input['artist_id']);
		$data['event'] = $event;
		$venue_id = $event->venue['id'];

		if($venue_id != '')
			$event_host = User::find($event->venue);
		else
			$event_host = User::find($event->creator);

		$data['event_host'] = $event_host;

		EmailSender::emailRequestPerformanceNotification($data);

		// Ready response for return
		return Response::json($service);

	}

	public function postAjaxUpdateService(){

		$input = Input::all();

		// Check if post has service_id then proceede into updatating the posted _id
		if(isset($input['service_id'])){
			$_id = $input['service_id'];
			$service = Service::find($_id);
			
			// $start_time = $input['start_time'];
			$start_date = $input['start_date'];

			// $end_time = $input['end_time'];
			$end_date = $input['end_date'];

			$start_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['start_time']);
			$end_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['end_time']);

			$start_datetime = $start_date . $start_time;
			$end_datetime = $end_date . $end_time;

			$start_datetime = new MongoDate(strtotime($start_datetime));
			$end_datetime = new MongoDate(strtotime($end_datetime));

			$m_start_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($start_datetime);
			$m_end_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($end_datetime);

			if(isset($input['artist_id'])){
				$service->artist_id = $input['artist_id'];
				$artist = User::find($input['artist_id']);
				$service->artist_name = $artist['name'];
			}

			$service->start_datetime = $m_start_datetime;
			$service->end_datetime = $m_end_datetime;

			if(isset($input['date_requested']))
				$service->date_requested = $input['date_requested'];

			if(isset($input['price']))
				$service->price = $input['price'];

			if(isset($service->event_id)){
				$event = Events::find($service->event_id);
				$service_lineup = $event->service_lineup;
				$new_service_lineup = array();

				foreach ($service_lineup as $service_container) {
					$service_holder = 	$service_container['service'];

					if($service_holder['service_id'] == $service->_id){
						$service_holder['artist_id'] = $service->artist_id;
						$service_holder['artist_name'] = $service->artist_name;
						$service_holder['start_datetime'] = $service->start_datetime;
						$service_holder['end_datetime'] = $service->end_datetime;
						$service_holder['price'] = $service->price;
						$service_holder['status'] = $service->status;

						$service_container['service'] = $service_holder;
						array_push($new_service_lineup, $service_container);
					}
					else{
						array_push($new_service_lineup, $service_container);
					}
				}

				$event->service_lineup = $new_service_lineup;
				$event->save();
			}

			$service->save();
			
			return Response::json($service);
		}

	}

	public function postAjaxDeleteService(){
		$input = Input::all();

		// Check if service_id exists then delete
		if(isset($input['service_id'])){
			$_id = $input['service_id'];
			$service = Service::find($_id);

			if(isset($service->event_id)){
				$event = Events::find($service->event_id);
				$event_service = $event->service_lineup;
				
				$service_container = array();

				foreach($event_service as $service_holder){
					$_service = $service_holder['service'];

					if($_service['service_id'] != $_id)
						array_push($service_container, $service_holder);
				}

				$event->service_lineup = $service_container;
				$event->save();

				// Notify venue when artist declined its performance request
				$event = Events::find($service->event_id);
				$creator = User::find($event->creator);
				$notification = new Notification;
				$notification->sender_id = $service->artist_id;
				$notification->sender_name = $service->artist_name;
				$notification->recipient_id = $event->venue['id'];
				$notification->recipient_name = $event->venue['name'];
				$notification->event_id = $event->id;
				$notification->title = $event->title;
				$notification->status = 'declined';
				$notification->type = 'performance';
				$notification->body = $service->artist_name.' declined to perform at '.$event->title;
				$notification->sent_at = new MongoDate();
				$notification->is_read = false;
				$notification->save();

			}

			$service->delete();
		}

	}

	public function postAjaxServiceLookup(){

		$input = Input::all();

		if(isset($input['_id'])){
			$service = Service::find($input['_id']);
			$result = Response::json($service);
		}
		else
			$result['error'] = true;

		return $result;

	}

	public function postAjaxFetchServiceByEventsId(){

		$input = Input::all();
		$event_id = $input['id'];
		$user_id = Session::get('id');

		$data['services'] = Service::getServices($event_id)->get();
		$data['user_id'] = $user_id;

		return $data;

	}

	public function postAjaxFetchPublicServiceByEventsId(){

		$input = Input::all();
		$event_id = $input['id'];

		return Service::getPublicServices($event_id)->get();

	}

}