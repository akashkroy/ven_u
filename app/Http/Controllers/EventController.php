<?php

namespace OurScene\Http\Controllers;

use Input;
use Log;
use Redirect;
use Session;
use View;
use MongoDate;
use DB;
use URL;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

use OurScene\Models\User;
use OurScene\Models\Event;
use OurScene\Models\Equipment;
use OurScene\Models\Notification;
use OurScene\Models\Promotion;
use OurScene\Models\Service;

use OurScene\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\Filesystem;

use OurScene\Helpers\GoogleCalendarHelper;
use OurScene\Helpers\EmailSender;
use OurScene\Helpers\DatetimeUtils;

use OurScene\Services\GoogleCalendar;
use Illuminate\Support\Facades\Config;


class EventController extends Controller {


	public function __construct(){
		$this->middleware('auth.login');

		$this->middleware('auth.venue',
			['only' =>
				['getEditEvent', 'postEditEvent']
			]
		);

		$this->middleware('auth.artist',
			['only' =>
				['getMyEventsEvents']
			]
		);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	/* Get event */

	public function getEvent($id){

		$event = Event::find($id);

		if(!$event)
			abort(404);

		$event_venue = User::find($event['venue']['id']);

		$service_requests = Service::servicesByEventId($event['_id'])->pending()->service()->get();
		$performance_requests = Service::servicesByEventId($event['_id'])->pending()->performance()->get();

		$default_equipments = Equipment::isDefault()->user(Session::get('id'))->get();

		$all_equipments = Equipment::user(Session::get('id'))->get();
		
		$all_equipments_with_trashed = Equipment::user(Session::get('id'))->withTrashed()->get();

		return View::make('ourscene.view-event', compact('event', 'event_venue', 'service_requests', 'performance_requests', 'default_equipments', 'all_equipments', 'all_equipments_with_trashed'));
	}

	/* Create event */

	public function getCreateEvent(){

		$start_date = $start_time = '';
		$end_date = $end_time = '';

		if(Session::has('event_form_input')){

			$event_form_input = Session::get('event_form_input');

			$start_date	= $event_form_input['start_date'];
			$start_time	= $event_form_input['start_time'];
			$end_date	= $event_form_input['end_date'];
			$end_time	= $event_form_input['end_time'];

			Session::forget('event_form_input');
		}

		$equipments = Equipment::isDefault()->user(Session::get('id'))->get();

		$all_equipments = Equipment::user(Session::get('id'))->get();

		$form_action = "add";

		return View::make('ourscene.event-form', compact('start_date', 'start_time', 'end_date', 'end_time', 'equipments', 'all_equipments', 'form_action'));
	}

	public function postCreateEvent(){

		//trim inputs
		//NOTE: do not sanitize all inputs because this method accepts JSON
		Input::merge(array_map_recursive('trim', Input::all()));
		$input = Input::all();

		//get, format, validate inputs

		$errors = [];

		$user_id = Session::get('id');
		$user_name = Session::get('name');
		$user_type = Session::get('user_type');

		$title = $input['title'];

		$start_date = $input['start_date'];
		$end_date = $input['end_date'];

		$start_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['start_time']);
		$end_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['end_time']);

		$opening_time = $input['opening_time'];
		if(isset($input['opening_time'])){
			$opening_time = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime(new MongoDate(strtotime($start_date.$input['opening_time'])));
			 
		}

		$timezone_offset = $input['timezone_offset'] ;

		// $start_datetime = DatetimeUtils::generateMongoUTCDatetime($start_date, $start_time, $timezone_offset);
		// $end_datetime = DatetimeUtils::generateMongoUTCDatetime($end_date, $end_time, $timezone_offset);

		$start_client_datetime = new MongoDate(strtotime($start_date.$start_time));
		$end_client_datetime = new MongoDate(strtotime($end_date.$end_time));

		$start_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($start_client_datetime);
		$end_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($end_client_datetime);

		if(isset($input['other_type']))
			$event_type = $input['other_type'];
		else
			$event_type = $input['type'];

		$venue_id = $input['venue_id'];
		$age_requirements = $input['age_requirements'];
		$description = $input['description'];
		$cover_charge = $input['cover_charge'];


		if(isset($input['pay_to_play']))
			$pay_to_play = true;
		else
			$pay_to_play = false;

		if($user_type == 'venue'){
			$status = 'confirmed';
			$confirmation_date = new MongoDate();
		}
		else{
			$status = 'pending';
			$confirmation_date = null;
		}

		if($user_type == 'venue'){

			//get requested artists

			$artists = [];

			if(isset($input['artists'])){
				$input_artists = json_decode($input['artists'], true);

				foreach($input_artists as $input_artist){

					$artist = array();

					$artist = User::artists()->find($input_artist['artist']['id']);

					if($artist){
						$performance_time = $input_artist['performance_time'];

						$artist_start = new MongoDate(strtotime($performance_time['start_date'].$performance_time['start_time']));
						$artist_end = new MongoDate(strtotime($performance_time['end_date'].$performance_time['end_time']));

						$artist['start_datetime'] = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($artist_start);;
						$artist['end_datetime'] = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($artist_end);


						array_push($artists, $artist);
					}
				}
			}

		}

		if(DatetimeUtils::datetimeGreaterThan($start_datetime, $end_datetime)){
			$errors[] = "The start datetime should be earlier than the end datetime.";
		}

		if(count($errors)){
			$response['error'] = $errors;
			return $response;
		}

		$services_lineup =  [];

		//get venue

		$venue = User::find($venue_id);

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


		//create event first then tsaka prompt them to pay it.


		if($user_type == 'venue' && count($artists) == 0){
			$response = EventController::paidCreateEvent($input);

			return Redirect::to($response['redirect_url'])->with('success', $response['message']);
		}
		else{
			//reinitialize pay ourscene transaction session

			Session::forget('pay_ourscene_action');

			$pay_ourscene_action = array(
				'type'			=> 'create event',
				'input'			=> $input,
				'before_payment_message'	=> 'Create event'
			);

			Session::put('pay_ourscene_action', $pay_ourscene_action);


			//redirect to payment
			return Redirect::to(action('PaypalController@getPayOurscene'));
		}

	}

	public static function paidCreateEvent($input){

		//get and format inputs
		// date_default_timezone_set('Asia/Manila');
		$user_id = Session::get('id');
		$user_name = Session::get('name');
		$user_type = Session::get('user_type');

		$title = $input['title'];

		$start_date = $input['start_date'];
		$end_date = $input['end_date'];
		$start_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['start_time']);
		$end_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['end_time']);

		$opening_time = $input['opening_time'];
		if(isset($input['opening_time']) && $input['opening_time']!="" ){
			$opening_time = new MongoDate(strtotime($start_date . DatetimeUtils::formatTimeFromFrontendToBackend($input['opening_time'])));
			$opening_time = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($opening_time);
		}else{
			$opening_time = ""; 
		}

		$timezone_offset = $input['timezone_offset'] ;

		// $start_datetime = DatetimeUtils::generateMongoUTCDatetime($start_date, $start_time, $timezone_offset);
		// $end_datetime = DatetimeUtils::generateMongoUTCDatetime($end_date, $end_time, $timezone_offset);

		$start_client_datetime = new MongoDate(strtotime($start_date.$start_time));
		$end_client_datetime = new MongoDate(strtotime($end_date.$end_time));

		$start_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($start_client_datetime);
		$end_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($end_client_datetime);

		$g_start_datetime = getUTCDateTime($start_date, $start_time, $timezone_offset);
		$g_end_datetime   = getUTCDateTime($end_date, $end_time, $timezone_offset);

		if(isset($input['other_type']))
			$event_type = $input['other_type'];
		else
			$event_type = $input['type'];

		$venue_id = $input['venue_id'];
		$age_requirements = $input['age_requirements'];
		$description = $input['description'];
		$cover_charge = $input['cover_charge'];

		if(isset($input['pay_to_play']))
			$pay_to_play = true;
		else
			$pay_to_play = false;

		if($user_type == 'venue'){
			$status = 'confirmed';
			$confirmation_date = new MongoDate();
		}
		else{
			$status = 'pending';
			$confirmation_date = null;
		}

		if($user_type == 'venue'){

			//get requested artists

			$artists = [];

			if(isset($input['artists'])){
				$input_artists = json_decode($input['artists'], true);

				foreach($input_artists as $input_artist){

					$artist = array();

					$artist = User::artists()->find($input_artist['artist']['id']);

					if($artist){
						$performance_time = $input_artist['performance_time'];

						$artist_start = new MongoDate(strtotime($performance_time['start_date'].$performance_time['start_time']));
						$artist_end = new MongoDate(strtotime($performance_time['end_date'].$performance_time['end_time']));

						$artist['start_datetime'] = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($artist_start);;
						$artist['end_datetime'] = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($artist_end);

						array_push($artists, $artist);
					}
				}
			}
		}

		$services_lineup =  [];

		//get venue

		$venue = User::find($venue_id);

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

		//create event

		$event = new Event;

		$event->title = $title;
		$event->start_datetime = $start_datetime;
		$event->end_datetime = $end_datetime;
		$event->opening_time = $opening_time;
		$event->event_type = $event_type;
		$event->age_requirements = $age_requirements;
		$event->description = $description;
		$event->cover_charge = $cover_charge;
		$event->user_id = $user_id;
		$event->venue = array(
			'id'		=> $venue['id'],
			'name'		=> $venue['name'],
			'address'	=> $venue['address']
		);
		$event->services_lineup = $services_lineup;
		$event->pay_to_play = $pay_to_play;

		if($confirmation_date)
			$event->confirmation_date = $confirmation_date;

		$event->status = $status;

		//house equipments can be fetched from venue only
		if($user_type == 'artist')
			$event->equipments = [];
		else if($user_type == 'venue')
			$event->equipments = $equipments;

		$event->save();

		if($user_type == 'artist'){

			//create service (request for performance) with pending status and paid payment status

			$service = new Service;

			$service->type = 'performance';
			$service->event_id = $event->_id;
			$service->sender_id = $user_id;
			$service->receiver_id = $venue_id;
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

			//get artist
			$artist = User::artists()->find($user_id);
			//send email to venue
			EmailSender::requestForPerformance($event, $service, $venue, $artist);
		}
		else if($user_type == 'venue'){

			foreach($artists as $artist){

				//create service (request for service) from requested artists

				$service = new Service;

				$service->type = 'service';
				$service->event_id = $event->_id;
				$service->sender_id = $user_id;
				$service->receiver_id = $artist['_id'];
				$service->artist = array(
					'id'	=> $artist['_id'],
					'name'	=> $artist['name']
				);
				$service->start_datetime = $artist['start_datetime'];
				$service->end_datetime = $artist['end_datetime'];
				$service->status = 'pending';
				$service->request_date = new MongoDate();
				$service->payment_status = 'unpaid';
				$service->equipments = [];

				$service->save();

				//send email to requested artistsshnaka
				EmailSender::requestForService($event, $service, $venue, $artist);
			}

		}

		if(User::where('_id',Session::get('id'))->first()['gcalendar'] !== null){

			try{
				if (User::where('_id',Session::get('id'))->first()['gcalendar']['allow']) {
					GoogleCalendarHelper::insertGoogleCalendarEvent($event,$g_start_datetime,$g_end_datetime);
				}
			}catch(Exception $e){
				Log::error('Error inserting to Google Calendar',['event'=>$event->id]);
			}
		}

		$response['success'] = true;
		$response['redirect_url'] = action('EventController@getMyEventsCalendar');
		$response['message'] = 'The event was successfully created.';

		return $response;
	}

	public function changePaymentStatus(){

	}

	public function postCreateEventFromDragAndDrop(){

		$start_date = $start_time = $end_time = $end_date = '';

		if (Input::has('start_datetime')){
			$in_start_datetime = Input::get('start_datetime')/1000;
			$start_date = date('m/d/Y', $in_start_datetime);
			$start_time = DatetimeUtils::formatTimeFromBackendToFrontend($in_start_datetime);
		}

		if (Input::has('end_datetime')){
			$in_end_datetime = Input::get('end_datetime')/1000;
			$end_date = date('m/d/Y', $in_end_datetime);
			$end_time = DatetimeUtils::formatTimeFromBackendToFrontend($in_end_datetime);
		}

		$event_form_input = array(
			'start_date'	=> $start_date,
			'start_time'	=> $start_time,
			'end_date'	=> $end_date,
			'end_time'	=> $end_time
		);

		return Redirect::to(action('EventController@getCreateEvent'))
			->with('event_form_input', $event_form_input);
	}

	/* Edit event */

	public function getEditEvent($id){

		$event = Event::find($id);

		if(!$event)
			abort(404);

		//check permissions

		if(Session::get('user_type') == 'venue' && $event->venue['id'] != Session::get('id'))
			abort(401);
		
		if(DatetimeUtils::datetimeGreaterThan(new MongoDate(), $event->end_datetime))
			return Redirect::to(action('EventController@getEvent', array('id' => $event['_id'])))->with('error', 'Sorry, you cannot edit a past event.');

		$start_datetime_local = DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($event->start_datetime);
		$start_date = date('m/d/Y', $start_datetime_local->sec);
		$start_time = DatetimeUtils::formatTimeFromBackendToFrontend($start_datetime_local->sec);


		$end_datetime_local = DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($event->end_datetime);
		$end_date = date('m/d/Y', $end_datetime_local->sec);
		$end_time = DatetimeUtils::formatTimeFromBackendToFrontend($end_datetime_local->sec);

		$opening_time;
		if($event->opening_time != null || $event->opening_time != ""){
			$opening_time = DatetimeUtils::formatTimeFromBackendToFrontend($event->opening_time->sec);
		}
		
		$equipments = [];

		foreach($event->equipments as $equipment){
			
			$eq = Equipment::withTrashed()->find($equipment['equipment_id']);

			if($eq)
				$equipments[] = $eq;

		}
			

		$all_equipments = Equipment::user(Session::get('id'))->get();

		$artist_lineup = Service::servicesByEventId($event->_id)
			->confirmed()
			->get();

		$invited_artists = Service::servicesByEventId($event->_id)
			->service()
			->pending()
			->get();

		$form_action = "edit";

		return View::make('ourscene.edit-regular-event', compact('event', 'start_date', 'start_time', 'end_date', 'end_time', 'opening_time', 'equipments', 'all_equipments', 'artist_lineup', 'invited_artists', 'form_action'));
	}

	public function postEditEvent($id){

		//get event
		$event = Event::find($id);

		if(!$event)
			abort(404);

		//check permissions

		if(Session::get('user_type') == 'venue' && $event->venue['id'] != Session::get('id'))
			abort(401);

		//trim inputs
		//NOTE: do not sanitize all inputs because this method accepts JSON
		Input::merge(array_map_recursive('trim', Input::all()));
		$input = Input::all();

		//get, validate, and format inputs

		$errors = [];

		$user_id = Session::get('id');
		$user_name = Session::get('name');
		$user_type = Session::get('user_type');

		$title = $input['title'];

		$start_date = $input['start_date'];
		$end_date = $input['end_date'];

		$start_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['start_time']);
		$end_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['end_time']);

		$timezone_offset = $input['timezone_offset'] ;

		// $start_datetime = DatetimeUtils::generateMongoUTCDatetime($start_date, $start_time, $timezone_offset);
		// $end_datetime = DatetimeUtils::generateMongoUTCDatetime($end_date, $end_time, $timezone_offset);

		$start_client_datetime = new MongoDate(strtotime($start_date.$start_time));
		$end_client_datetime = new MongoDate(strtotime($end_date.$end_time));

		$start_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($start_client_datetime);
		$end_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($end_client_datetime);

		$g_start_datetime = getUTCDateTime($start_date, $start_time, $timezone_offset);
		$g_end_datetime = getUTCDateTime($end_date, $end_time, $timezone_offset);

		$opening_time = $input['opening_time'];
		if(isset($input['opening_time']) && $input['opening_time']!="" ){
			$opening_time = new MongoDate(strtotime($start_date . DatetimeUtils::formatTimeFromFrontendToBackend($input['opening_time'])));
		}else{
			$opening_time = "";
		}

		if(isset($input['other_type']))
			$event_type = $input['other_type'];
		else
			$event_type = $input['type'];

		$venue_id = $input['venue_id'];
		$age_requirements = $input['age_requirements'];
		$description = $input['description'];
		$cover_charge = $input['cover_charge'];

		if(isset($input['pay_to_play']))
			$pay_to_play = true;
		else
			$pay_to_play = false;

		$now = new MongoDate();

		//get requested artists

		$artists = [];

		if(isset($input['artists'])){
			$input_artists = json_decode($input['artists'], true);

			foreach($input_artists as $input_artist){

				$artist = User::artists()->find($input_artist['artist']['id']);

				if($artist){
					$performance_time = $input_artist['performance_time'];

					$artist_start = new MongoDate(strtotime($performance_time['start_date'].$performance_time['start_time']));
					$artist_end = new MongoDate(strtotime($performance_time['end_date'].$performance_time['end_time']));

					$artist['start_datetime'] = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($artist_start);
					$artist['end_datetime'] = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($artist_end);
					array_push($artists, $artist);
				}
			}
		}

		//get artist lineup changes

		$artist_lineup_changes = [];

		$delete_artist_lineup_ids = [];

		if(isset($input['delete_artist_lineup_ids'])){

			$input_ids = json_decode($input['delete_artist_lineup_ids'], true);

			foreach($input_ids as $id){
				array_push($delete_artist_lineup_ids, $id);
			}
		}

		if(isset($input['artist_lineup'])){
			$input_artist_lineup = json_decode($input['artist_lineup'], true);

			foreach($input_artist_lineup as $input_service_id => $input_service){

				if(!in_array($input_service_id, $delete_artist_lineup_ids)){

					$service = array();

					$performance_time = $input_service['performance_time'];

					$service['id'] = $input_service_id;
					
					$service_start = new MongoDate(strtotime($performance_time['start_date'].$performance_time['start_time']));
					$service_end = new MongoDate(strtotime($performance_time['end_date'].$performance_time['end_time']));

					$service['start_datetime'] = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($service_start);;
					$service['end_datetime'] = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($service_end);

					$artist_lineup_changes[] = $service;
				}
			}
		}

		//get invited artists changes

		$invited_artists_changes = [];

		$delete_invited_artist_ids = [];

		if(isset($input['delete_invited_artist_ids'])){

			$input_ids = json_decode($input['delete_invited_artist_ids'], true);

			foreach($input_ids as $id){
				array_push($delete_invited_artist_ids, $id);
			}
		}

		if(isset($input['invited_artists'])){
			$input_invited_artists = json_decode($input['invited_artists'], true);

			foreach($input_invited_artists as $input_service_id => $input_service){

				if(!in_array($input_service_id, $delete_invited_artist_ids)){

					$service = array();

					$performance_time = $input_service['performance_time'];

					$service['id'] = $input_service_id;
					$service_start = new MongoDate(strtotime($performance_time['start_date'].$performance_time['start_time']));
					$service_end = new MongoDate(strtotime($performance_time['end_date'].$performance_time['end_time']));

					$service['start_datetime'] = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($service_start);;
					$service['end_datetime'] = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($service_end);

					$invited_artists_changes[] = $service;
				}
			}
		}

		//validate inputs

		if(DatetimeUtils::datetimeGreaterThan($start_datetime, $end_datetime)){
			$errors[] = "The start datetime should be earlier than the end datetime.";
		}

		if(count($errors)){
			$response['error'] = $errors;
			return $response;
		}

		//get venue

		$venue = User::find($venue_id);

		//get equipments

		$equipments = [];

		if(isset($input['equipments'])){
			$equipment_ids = $input['equipments'];

			foreach($equipment_ids as $equipment_id){

				$equipment = Equipment::withTrashed()->find($equipment_id);

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

		//update event

		$event->title = $title;
		$event->start_datetime = $start_datetime;
		$event->end_datetime = $end_datetime;
		$event->opening_time = $opening_time;
		$event->event_type = $event_type;
		$event->age_requirements = $age_requirements;
		$event->description = $description;
		$event->cover_charge = $cover_charge;
		$event->user_id = $user_id;
		$event->pay_to_play = $pay_to_play;
		$event->equipments = $equipments;

		$event->save();

		//send email to artists that event is updated

		$related_services = Service::servicesByEventId($event->_id)
			->where('status', '!=', 'rejected')
			->where('status', '!=', 'cancelled')
			->get();

		$artist_ids = [];

		foreach($related_services as $related_service){

			$artist_id = $related_service['artist.id'];

			if(!in_array($artist_id, $artist_ids)){

				//get artist
				$artist = User::find($artist_id);
				// dd($artist->email);?
				//send email to artist
				EmailSender::updateEvent($event, $related_service, $venue, $artist);

				$artist_ids[] = $artist_id;
			}
		}

		//update invited artists

		foreach($invited_artists_changes as $change_service){

			$service = Service::servicesByEventId($event->_id)
				->service()
				->pending()
				->find($change_service['id']);

			if($service){

				$has_change = false;

				$service = Service::find($change_service['id']);

				if($service->start_datetime != $change_service['start_datetime']){
					$service->start_datetime = $change_service['start_datetime'];
					$has_change = true;
				}

				if($service->end_datetime != $change_service['end_datetime']){
					$service->end_datetime = $change_service['end_datetime'];
					$has_change = true;
				}

				$service->save();

				if($has_change){

					//get artist
					$artist = User::artists()->find($service['artist.id']);

					if($artist){
						//send email to artist
						EmailSender::updatePerformanceTime($event, $service, $venue, $artist);
					}
				}

			}
		}

		foreach($delete_invited_artist_ids as $id){

			$service = Service::servicesByEventId($event->_id)
				->service()
				->pending()
				->find($id);

			if($service){

				$service->status = 'cancelled';
				$service->cancellation_date = $now;
				$service->save();

				//get artist
				$artist = User::artists()->find($service['artist.id']);

				if($artist){
					//send email to artist
					EmailSender::cancelRequestForService($event, $service, $venue, $artist);
				}
			}
		}

		//update artist lineup

		foreach($artist_lineup_changes as $change_service){

			$service = Service::servicesByEventId($event->_id)
				->confirmed()
				->find($change_service['id']);

			if($service){

				$has_change = false;

				$service = Service::find($change_service['id']);

				if($service->start_datetime != $change_service['start_datetime']){
					$service->start_datetime = $change_service['start_datetime'];
					$has_change = true;
				}

				if($service->end_datetime != $change_service['end_datetime']){
					$service->end_datetime = $change_service['end_datetime'];
					$has_change = true;
				}

				$service->save();

				if($has_change){

					//get artist
					$artist = User::artists()->find($service['artist.id']);

					if($artist){
						//send email to artist
						EmailSender::updatePerformanceTime($event, $service, $venue, $artist);
					}
				}

			}
		}

		foreach($delete_artist_lineup_ids as $id){

			$service = Service::servicesByEventId($event->_id)
				->confirmed()
				->find($id);

			if($service){

				$service->status = 'cancelled';
				$service->cancellation_date = $now;
				$service->save();

				//get artist
				$artist = User::artists()->find($service['artist.id']);

				if($artist){
					//send email to artist
					EmailSender::cancelPerformance($event, $service, $venue, $artist);
				}
			}
		}

		//update event service lineup

		$services_lineup = [];

		$new_services_lineup = Service::servicesByEventId($event->_id)
			->confirmed()
			->get();

		foreach($new_services_lineup as $service){

			array_push($services_lineup, array(
				"service_id" => $service->_id,
				"artist_id" => $service->artist['id'],
				"artist_name" => $service->artist['name'],
				"start_datetime" => $service->start_datetime,
				"end_datetime" => $service->end_datetime,
				"equipments" => $service->equipments
			));
		}

		$event->services_lineup = $services_lineup;

		$event->save();

		//check event services and artists to be invited

		$services = Service::servicesByEventId($event->_id)->get();

		if(count($services) == 0 && count($artists) > 0){

			//reinitialize pay ourscene transaction session

			Session::forget('pay_ourscene_action');

			$pay_ourscene_action = array(
				'type'			=> 'request for service from edit event',
				'input'			=> null,
				'artists'		=> $artists,
				'event'		=> $event,
				'venue'		=> $venue,
				'before_payment_message'	=> 'The event details was successfully updated but you are about to connect to an artist for the first time.'
			);

			Session::put('pay_ourscene_action', $pay_ourscene_action);

			//redirect to payment
			return Redirect::to(action('PaypalController@getPayOurscene'));
		}
		else{
			foreach($artists as $artist){

				//create service (request for service) from requested artists

				$service = new Service;

				$service->type = 'service';
				$service->event_id = $event->_id;
				$service->sender_id = $user_id;
				$service->receiver_id = $artist['_id'];
				$service->artist = array(
					'id'	=> $artist['_id'],
					'name'	=> $artist['name']
				);
				$service->start_datetime = $artist['start_datetime'];
				$service->end_datetime = $artist['end_datetime'];
				$service->status = 'pending';
				$service->request_date = new MongoDate();
				$service->payment_status = 'unpaid';
				$service->equipments = [];

				$service->save();

				//send email to requested artists
				EmailSender::requestForService($event, $service, $venue, $artist);
			}
		}
		if(User::where('_id',Session::get('id'))->first()['gcalendar'] !== null){
			try{
				if (User::where('_id',Session::get('id'))->first()['gcalendar']['allow']) {
					GoogleCalendarHelper::updateGoogleCalendarEvent($event,$g_start_datetime,$g_end_datetime);
				}
			}catch(Exception $e){
				Log::error('Error inserting to Google Calendar',['event'=>$event->id]);
			}
		}

		return Redirect::to(action('EventController@getEvent', array('id' => $event['_id'])))->with('success', 'The event was successfully updated.');
	}

	public static function paidEditEvent($artists, $event, $venue){

		foreach($artists as $artist){

			//create service (request for service) from requested artists

			$service = new Service;

			$service->type = 'service';
			$service->event_id = $event->_id;
			$service->sender_id = Session::get('id');
			$service->receiver_id = $artist['_id'];
			$service->artist = array(
				'id'	=> $artist['_id'],
				'name'	=> $artist['name']
			);
			$service->start_datetime = $artist['start_datetime'];
			$service->end_datetime = $artist['end_datetime'];
			$service->status = 'pending';
			$service->request_date = new MongoDate();
			$service->payment_status = 'unpaid';
			$service->equipments = [];

			$service->save();

			//send email to requested artists
			EmailSender::requestForService($event, $service, $venue, $artist);
		}

		$response['success'] = true;
		$response['redirect_url'] = action('EventController@getEvent', array('id' => $event->_id));
		$response['message'] = 'The event along with the requests were successfully updated.';

		return $response;
	}

	/* Cancel Event */

	public function getCancelEvent($id){

		//get event
		$event = Event::find($id);

		if(empty($event))
			abort(404);

		//get venue
		$venue = User::venues()->find($event->venue['id']);

		if(!$venue)
			abort(404);

		//check if user is creator of event
		if($event['user_id'] != Session::get('id'))
			abort(401);

		$now = new MongoDate();

		//update event

		$event->status = 'cancelled';
		$event->cancellation_date = $now;
		$event->save();

		$related_services = Service::servicesByEventId($event->_id)
			->where('status', '!=', 'rejected')
			->where('status', '!=', 'cancelled')
			->get();

		$artist_ids = [];

		foreach($related_services as $related_service){

			$artist_id = $related_service['artist.id'];

			if(!in_array($artist_id, $artist_ids)){

				//get artist
				$artist = User::artists()->find($artist_id);

				//send email to artist the event was cancelled
				EmailSender::cancelEvent($event, $related_service, $venue, $artist);

				$artist_ids[] = $artist_id;
			}
		}

		foreach($related_services as $related_service){

			//get service
			$service = Service::find($related_service->_id);

			//get artist
			$artist = User::artists()->find($service['artist.id']);

			if($artist){

				//update service and send email to artist

				if($service->type == 'service'){

					if($service->status == 'pending'){

						$service->status = 'cancelled';
						$service->cancellation_date = $now;
						$service->save();

						EmailSender::cancelRequestForService($event, $service, $venue, $artist);
					}
					else if($service->status == 'confirmed'){

						$service->status = 'cancelled';
						$service->cancellation_date = $now;
						$service->save();

						EmailSender::cancelPerformance($event, $service, $venue, $artist);
					}

				}
				else if($service->type == 'performance'){

					if($service->status == 'pending'){

						$service->status = 'rejected';
						$service->rejection_date = $now;
						$service->save();

						EmailSender::rejectRequestForPerformance($event, $service, $venue, $artist);
					}
					else if($service->status == 'confirmed'){

						$service->status = 'cancelled';
						$service->cancellation_date = $now;
						$service->save();

						EmailSender::cancelPerformance($event, $service, $venue, $artist);
					}

				}
			}

		}

		return Redirect::to(action('EventController@getMyEventsCalendar'))->with('success', 'The event was successfully cancelled.');
	}

	/* Delete Event */

	public function getDeleteEvent($id){

		$event = Event::find($id);

		if(empty($event))
			abort(404);

		//check if user is creator of event
		if($event['user_id'] != Session::get('id'))
			abort(401);

		$event->delete();

		return Redirect::to(action('EventController@getMyEventsCalendar'))->with('success', 'The event was successfully deleted.');
	}

	/* Events feed */

	public function getEventsFeedCalendar(){

		return 'Soon';
		//return View::make('ourscene.events-feed-calendar');
	}

	/* My events */

	public function getMyEventsEvents(){

		$user_id = Session::get('id');
		$artist_id = Session::get('id');

		$confirmed_events = Service::confirmed()
			->where(function ($query) use ($user_id, $artist_id){
                $query->servicesBySenderId($user_id)
                	->orWhere(function ($query) use ($artist_id){
                		$query->servicesByArtistId($artist_id);
                	});
            })
			->get();

		$pending_events = Service::servicesBySenderId($user_id)->pending()->get();
		$rejected_events = Service::servicesBySenderId($user_id)->rejected()->get();

		return View::make('ourscene.my-events-events', compact('confirmed_events', 'pending_events', 'rejected_events'));
	}

	public function getMyEventsCalendar(){

		return View::make('ourscene.my-events-calendar');
	}

	/* Requests */

	public function getRequests(){

		$pending_requests = Service::servicesByReceiverId(Session::get('id'))->pending()->get();

		$default_equipments = Equipment::isDefault()->user(Session::get('id'))->get();

		$all_equipments = Equipment::user(Session::get('id'))->get();

		return View::make('ourscene.requests', compact('pending_requests', 'default_equipments', 'all_equipments'));
	}

	/* Edit artist lineup equipment */

	public function postEditArtistLineupEquipment(){
		
		//trim and sanitize all inputs
		Input::merge(array_map_recursive('trim', Input::all()));
		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);

		//get and format inputs

		$service_id = $input['service_id'];
		
		//get equipments

		$equipments = [];

		if(isset($input['equipments'])){
			$equipment_ids = $input['equipments'];

			foreach($equipment_ids as $equipment_id){
				
				$equipment = Equipment::withTrashed()->find($equipment_id);
				
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

		//get service

		$service = Service::find($service_id);

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

		$service->equipments = $equipments;

		$service->save();

		$updated_services_lineup = [];

		foreach ($event->services_lineup as $service_lineup){

			if($service_lineup['service_id'] == $service_id){
				$service_lineup['equipments'] = $service->equipments;
			}

			array_push($updated_services_lineup, $service_lineup);
		}

		$event->services_lineup = $updated_services_lineup;

		$event->save();

		return Redirect::to(URL::previous().'#artist-line-up')->with('success', 'Your lineup equipment was successfully updated.');
	}

	public function invalidUpdateForm(){

		return Redirect::to(action('HomeController@getIndex'));

	}

	public function postAjaxFetchPrivateEventsByUserId(){

		$event_list = array(
			'events'	=> 	array(),
			'promotions'	=> array()
		);

		$user_id = Session::get('id');
		$user_type = Session::get('user_type');
		
		$start_datetime = new MongoDate(Input::get('start_datetime')/1000);
		$end_datetime = new MongoDate(Input::get('end_datetime')/1000);

		$start_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($start_datetime);
		$end_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($end_datetime);

		//get private events by user

		$events  = Event::getPrivateEventsByUserId($user_id, $start_datetime, $end_datetime)->get();

		foreach ($events as $event) {

			$event_summary['id'] = $event['_id'];
			$event_summary['title'] = $event['title'];

			$start_datetime_local = DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($event['start_datetime']);
			$end_datetime_local = DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($event['end_datetime']);
		
			$event_summary['start_datetime'] = $start_datetime_local;
			$event_summary['end_datetime'] = $end_datetime_local;

			$color_code = '';

			if($user_type == 'venue'){
				//venue created the event
				if($event['user_id'] == $user_id)
					$color_code = 'created-event-by-venue-color';
				else{
					//venue confirmed
					if($event['status'] == 'confirmed')
						$color_code = 'confirmed-request-of-venue-color';
					//venue confirmation is pending
					else if($event['status'] == 'pending')
						$color_code = 'pending-request-of-venue-color';
				}
			}
			else if($user_type == 'artist'){
				//artist created the event
				if($event['user_id'] == $user_id){
					//venue confirmed
					if($event['status'] == 'confirmed')
						$color_code = 'confirmed-request-of-venue-color';
					//venue rejected
					if($event['status'] == 'rejected')
						$color_code = 'rejected-request-of-venue-color';
					//venue confirmation is pending
					else if($event['status'] == 'pending')
						$color_code = 'pending-request-of-venue-color';
				}
				else{
					//venue confirmed
					if($event['status'] == 'confirmed')
						$color_code = 'confirmed-request-of-venue-color';
				}

			}

			$event_summary['color_code'] = $color_code;

			array_push($event_list['events'], $event_summary);
		}

		//get promotions by user

		$promotions  = Promotion::getPromotionsByUserId($user_id, $start_datetime, $end_datetime)->get();


		foreach ($promotions as $promotion) {

			$promotion_summary['id'] = $promotion['_id'];
			$promotion_summary['title'] = $promotion['title'];
			$promotion_summary['start_datetime'] = DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($promotion['start_datetime']);
			$promotion_summary['end_datetime'] = DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($promotion['end_datetime']);

			array_push($event_list['promotions'], $promotion_summary);
		}

		return json_encode($event_list);
	}

	public function postAjaxFetchPublicEventsByUserId(){

		$event_list = array(
			'events'	=> 	array(),
			'promotions'	=> array()
		);

		$user_id = Input::get('user_id');
		$user_type = User::find($user_id)->user_type;

		$start_datetime = new MongoDate(Input::get('start_datetime')/1000);
		$end_datetime = new MongoDate(Input::get('end_datetime')/1000);

		$start_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($start_datetime);
		$end_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($end_datetime);

		//get private events by user

		$events  = Event::getPublicEventsByUserId($user_id, $user_type, $start_datetime, $end_datetime)->get();

		foreach ($events as $event) {

			$event_summary['id'] = $event['_id'];
			$event_summary['title'] = $event['title'];

			$start_datetime_local = DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($event['start_datetime']);
			$end_datetime_local = DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($event['end_datetime']);

			$event_summary['start_datetime'] = $start_datetime_local;
			$event_summary['end_datetime'] = $end_datetime_local;

			$color_code = '';

			if($user_type == 'venue'){
				//venue created the event
				if($event['user_id'] == $user_id)
					$color_code = 'created-event-by-venue-color';
				else{
					//venue confirmed
					if($event['status'] == 'confirmed')
						$color_code = 'confirmed-request-of-venue-color';
					//venue confirmation is pending
					else if($event['status'] == 'pending')
						$color_code = 'pending-request-of-venue-color';
				}
			}
			else if($user_type == 'artist'){
				//artist created the event
				if($event['user_id'] == $user_id){
					//venue confirmed
					if($event['status'] == 'confirmed')
						$color_code = 'confirmed-request-of-venue-color';
					//venue rejected
					if($event['status'] == 'rejected')
						$color_code = 'rejected-request-of-venue-color';
					//venue confirmation is pending
					else if($event['status'] == 'pending')
						$color_code = 'pending-request-of-venue-color';
				}
				else{
					//venue confirmed
					if($event['status'] == 'confirmed')
						$color_code = 'confirmed-request-of-venue-color';
				}

			}

			$event_summary['color_code'] = $color_code;

			array_push($event_list['events'], $event_summary);
		}

		//get promotions by user

		$promotions  = Promotion::getPromotionsByUserId($user_id, $start_datetime, $end_datetime)->get();


		foreach ($promotions as $promotion) {

			$promotion_summary['id'] = $promotion['_id'];
			$promotion_summary['title'] = $promotion['title'];
			$promotion_summary['start_datetime'] = DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($promotion['start_datetime']);
			$promotion_summary['end_datetime'] = DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($promotion['end_datetime']);


			array_push($event_list['promotions'], $promotion_summary);
		}

		return json_encode($event_list);
	}

	public function postAjaxAddEquipment(){

		$input = Input::all();
		$event_id = $input['event_id'];

		$event = Event::find($event_id);
		$equipments = $input['equipments'];
		$equipments = array_unique($equipments);
		$equipment_container = $event->equipment;

		foreach ($equipments as $equipment_id) {
			$equipment = Equipment::find($equipment_id);
			$_equipment['user_id'] = $equipment['user_id'];
			$_equipment['equipment_id'] = $equipment_id;
			$_equipment['name'] = $equipment['name'];
			$_equipment['owner'] = $equipment['owner'];
			$_equipment['qty'] = $equipment['qty'];
			$_equipment['inclusion'] = $equipment['inclusion'];

			$equipment_holder['equipment'] = $_equipment;
			array_push($equipment_container, $equipment_holder);
		}

		$event->equipment = $equipment_container;
		$event->save();

	}

	public function postAjaxRemoveEquipmentFromEvent(){

		$input = Input::all();
		$event_id = $input['event_id'];
		$equipment_id = $input['equipment_id'];

		$event = Event::find($event_id);
		$equipments = $event['equipment'];
		$new_equipments = array();

		foreach($equipments as $equipment_holder) {
			$equipment = $equipment_holder['equipment'];

			if($equipment['equipment_id'] != $equipment_id)
				array_push($new_equipments, $equipment_holder);
		}

		$event->equipment = $new_equipments;
		$event->save();
	}

	/* Google calendar */

	public function authenticateGoogleCalendar(){

		$client = GoogleCalendarHelper::getClient();
		$user = User::find(Session::get('id'));

		//The user has not authenticated. Redirect ot google's authorization page.
		if (!$client->getAccessToken() && $user->gcalendar['token'] == null) {
		  $authUrl = $client->createAuthUrl();
		  echo "<script> console.log('authenticate');</script> ";
		  return redirect()->away($authUrl);
		}

		return redirect('settings#account_info');
	}

	public function getDisableIntegrateGoogleCalendar(){

		//get user

		$user = User::find(Session::get('id'));

		if(!$user)
			abort(404);

		//update user

		$user->gcalendar = array(
				'allow' => false,
				'token' => null
			);
		$user->save();

		return Redirect::to(action('UserController@getProfileSettings').'#account-info')->with('success', 'The integration with Google calendar was successfully disabled.');
	}

	public function getGoogleEvent(){
		$service = new Google_Service_Calendar(GoogleCalendarHelper::getClient());

		$event = $service->events->get('primary', "435kpddl850k7ecj34lm4n6g4g");
		echo $event->getSummary();

	}

}
