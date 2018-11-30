<?php namespace OurScene\Http\Controllers;

use Session;
use View;
use Input;
use Redirect;
use Hash;
use App;
use DateTime;
use MongoDate;

use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\Filesystem;

use OurScene\Models\Notification;
use OurScene\Models\Promotion;
use OurScene\Models\User;

use OurScene\Helpers\DatetimeUtils;


class PromotionController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Promotion Controller
	|--------------------------------------------------------------------------
	|
	| This controller manages all promotions linked to venues.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(){

		$this->middleware('auth.login');
		$this->middleware('auth.venue',
			['except' => 
				['getPromotion']
			]
		);
	}

	/* Root */

	public function getIndex(){

	}

	/* View All Promotions */

	public function getPromotions(){

		$user = User::find(Session::get('id'));

		if(empty($user)||($user->user_type === 'artist'))
			return View::make('404');
		else{
			$promotions = Promotion::creator($user->id)->get();
			return View::make('ourscene.promotions')->with('user', $user)->with('promotions', $promotions);
		}

	}

	/* Promotion */

	public function getPromotion($id){

		$promotion = Promotion::find($id);

		$promotion_creator = User::find($promotion['user_id']);

		if(empty($promotion))
			abort(404);

		return View::make('ourscene.view-promotion', compact('promotion', 'promotion_creator'));
	}

	/* Create promotion */

	public function postCreatePromotion(){

		//trim and sanitize all inputs
		Input::merge(array_map_recursive('trim', Input::all()));
		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);

		//get and format inputs

		$user_id = Session::get('id');
		$user_name = Session::get('name');
		$user_type = Session::get('user_type');

		$title = $input['title'];
		
		$start_date = $input['start_date'];
		$end_date = $input['end_date'];
		$start_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['start_time']);
		$end_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['end_time']);

		$start_datetime = new MongoDate(strtotime($start_date.$start_time));
		$end_datetime = new MongoDate(strtotime($end_date.$end_time));

		$start_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($start_datetime);
		$end_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($end_datetime);

		if(isset($input['other_type']))
			$promotion_type = $input['other_type'];
		else
			$promotion_type = $input['type'];

		$venue_id = $input['venue_id'];
		$age_requirements = $input['age_requirements'];
		$description = $input['description'];

		//validate inputs

		$errors = [];

		if($start_datetime > $end_datetime){
			$errors[] = "The start datetime should be earlier than the end datetime.";
		}

		if(count($errors)){
			$response['error'] = $errors;
			return $response;
		}

		//get venue

		$venue = User::find($venue_id);
		
		//create promotion

		$promotion = new Promotion;

		$promotion->title = $title;
		$promotion->start_datetime = $start_datetime;
		$promotion->end_datetime = $end_datetime;
		$promotion->promotion_type = $promotion_type;
		$promotion->age_requirements = $age_requirements;
		$promotion->description = $description;
		$promotion->user_id = $user_id;
		$promotion->venue = array(
			'id'		=> $venue['id'],
			'name'		=> $venue['name'],
			'address'	=> $venue['address']
		);
		$promotion->save();
		
		return Redirect::to(action('EventController@getMyEventsCalendar'))->with('success', 'The promotion was successfully created.');
	}

	/* Edit Promotion */

	public function getEditPromotion($id){

		$promotion = Promotion::find($id);

		if(empty($promotion))
			App::abort(404);

		//check if user is creator of promotion
		if($promotion['user_id'] != Session::get('id'))
			App::abort(401);

		$start_date = date('m/d/Y', $promotion['start_datetime']->sec);
		$end_date = date('m/d/Y', $promotion['end_datetime']->sec);
		
		$start_time = DatetimeUtils::formatTimeFromBackendToFrontend(
						DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($promotion['start_datetime'])
					->sec);
		$end_time = DatetimeUtils::formatTimeFromBackendToFrontend(
						DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($promotion['end_datetime'])
					->sec);

		$form_action = 'edit';

		return View::make('ourscene.edit-promotion', compact('promotion', 'start_date', 'start_time', 'end_date', 'end_time', 'form_action'));
	}

	public function postEditPromotion($id){

		$promotion = Promotion::find($id);

		if(empty($promotion))
			abort(404);

		//check if user is creator of promotion
		if($promotion['user_id'] != Session::get('id'))
			App::abort(401);

		//trim and sanitize all inputs
		Input::merge(array_map_recursive('trim', Input::all()));
		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);

		//get and format inputs

		$user_id = Session::get('id');
		$user_name = Session::get('name');
		$user_type = Session::get('user_type');

		$title = $input['title'];
		
		$start_date = $input['start_date'];
		$end_date = $input['end_date'];
		$start_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['start_time']);
		$end_time = DatetimeUtils::formatTimeFromFrontendToBackend($input['end_time']);

		$start_datetime = new MongoDate(strtotime($start_date.$start_time));
		$end_datetime = new MongoDate(strtotime($end_date.$end_time));

		$start_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($start_datetime);
		$end_datetime = DatetimeUtils::convertMongoClientDatetimeToMongoUTCDatetime($end_datetime);

		if(isset($input['other_type']))
			$promotion_type = $input['other_type'];
		else
			$promotion_type = $input['type'];

		$venue_id = $input['venue_id'];
		$age_requirements = $input['age_requirements'];
		$description = $input['description'];

		//validate inputs

		$errors = [];

		if($start_datetime > $end_datetime){
			$errors[] = "The start datetime should be earlier than the end datetime.";
		}

		if(count($errors)){
			$response['error'] = $errors;
			return $response;
		}

		//get venue

		$venue = User::find($venue_id);
		
		//update promotion

		$promotion->title = $title;
		$promotion->start_datetime = $start_datetime;
		$promotion->end_datetime = $end_datetime;
		$promotion->promotion_type = $promotion_type;
		$promotion->age_requirements = $age_requirements;
		$promotion->description = $description;
		$promotion->user_id = $user_id;
		$promotion->venue = array(
			'id'		=> $venue['id'],
			'name'		=> $venue['name'],
			'address'	=> $venue['address']
		);
		
		$promotion->save();

		return Redirect::to(action('PromotionController@getPromotion', array('id' => $id)))->with('success', 'The promotion was successfully updated.');
	}

	/* Delete Promotion */

	public function getDeletePromotion($id){

		$promotion = Promotion::find($id);

		if(empty($promotion))
			abort(404);

		//check if user is creator of promotion
		if($promotion['user_id'] != Session::get('id'))
			App::abort(401);

		$promotion->delete();

		return Redirect::to(action('EventController@getMyEventsCalendar'))->with('success', 'The promotion was successfully deleted.');
	}

	public function postAjaxFetchPromotions(){

		$promotion_array = array();
		$promotion = new Promotion;

		$user_id = Session::get('id');
		$promotions = Promotion::all();

		foreach ($promotions as $promotion) {

			$promotion_summary['title'] = $promotion['title'];
			$promotion_summary['age_requirements'] = $promotion['age_requirements'];
			$promotion_summary['event_type'] = $promotion['event_type'];
			$promotion_summary['start_datetime'] = $promotion['start_datetime'];
			$promotion_summary['end_datetime'] = $promotion['end_datetime'];
			$promotion_summary['door_opening_time'] = $promotion['door_opening_time'];
			$promotion_summary['id'] = $promotion['_id'];
			$promotion_summary['description'] = $promotion['description'];

			array_push($promotion_array, $promotion_summary);
		}

		return json_encode($promotion_array);

	}

}