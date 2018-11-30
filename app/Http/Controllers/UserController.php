<?php namespace OurScene\Http\Controllers;

use Log;
use Session;
use View;
use Input;
use Redirect;
use Hash;
use App;
use MongoInt32;
use Response;
use Request;


use Illuminate\Contracts\Filesystem\Filesystem;

use OurScene\Models\User;
use OurScene\Models\Materials;
use OurScene\Models\Equipment;

use OurScene\Helpers\EmailSender;

class UserController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| User Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's page for users.
	| This will manage all user related information. (login, register, forgot password, update profile)
	|tw

	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth.login',
			['except' =>
				['getRegister', 'getRegisterAsArtist', 'postRegisterAsArtist', 'getRegisterAsVenue', 'postRegisterAsVenue',
				'getLogin', 'postLoginArtist', 'postLoginVenue', 'getLogout', 'getForgotPassword', 'postForgotPassword',
				'getValidateEmail']
			]
		);

		$this->genres = json_decode(file_get_contents(base_path()."/resources/assets/genres.json"));
		$this->venue_types = json_decode(file_get_contents(base_path()."/resources/assets/venue_types.json"),true);
	}


	protected $genres;
	protected $venue_types;

	/* Registration */

	public function getRegister(){

		return View::make('auth.register');

	}

	public function getRegisterAsArtist(){

		return View::make('auth.artist-register')->with('genres', $this->genres);

	}

	public function postRegisterAsArtist(){

		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);

		$paypal_info = array(
			'email' 	=> $input['paypal-email'],
			'card_id' 	=> $input['paypal-card-id']
		);

		$social_media = array(
			'fb' 			=> $input['facebook_account'],
			'twitter' 		=> $input['twitter_account'],
			'soundcloud' 	=> $input['soundcloud_account'],
			'bandcamp' 		=> $input['bandcamp_account']
		);

		$address = array(
			'city' 		=> $input['city'],
			'zipcode' 	=> $input['zipcode'],
		);

		$user = new User;

		$user->user_type 	= 'artist';
		$user->status 		= 'active';
		$user->name 		= $input['name'];
		$user->password 	= Hash::make($input['password']);
		$user->description 	= $input['description'];
		$user->ages 	= $input['ages'];
		$user->phone_number = $input['phone_number'];
		$user->address 		= $address;
		$user->paypal_info 	= $paypal_info;
		$user->social_media = $social_media;

		$user->updateArtistGenreWithInput(Input::get('genre'));

		$filter = filter_var($input['register-email'], FILTER_VALIDATE_EMAIL);
		if($filter){
			$user->email = $input['register-email'];
		}else{
			Input::flash();
			return Redirect::to('/artist-register')->with('error', 'Please enter a valid email address.');
		}

		if(sizeof(User::email($input['register-email'])->get()) > 0){
			Input::flash();
			return Redirect::to('/artist-register')->with('error', 'The given email address is already in use.');
		}else{
			$user->save();
			if(Input::hasFile('profile-picture')){
				$file = Input::file('profile-picture');
				$user->updateProfilePicture($file);
			}
			$user->save();
			$user->updateSessionWithUserDetails();
			Session::put('timezone_offset', $input['timezone_offset']);

			return Redirect::to('/artist-register#materials')->with('success', 'You have successfully registered.');
		}

	}

	public function postRegisterMaterials(){

		if (! Request::isMethod('post')){
			return Redirect::to('/artist-register#materials');
		}

		$user = User::find(Session::get('id'));

		for($i=1; $i<=5; $i++){

			if(Input::hasFile('materials-images-'.$i)){
				$file = Input::file('materials-images-'.$i);
				$user->addArtistImage($file);
			}

			if(Input::hasFile('materials-songs-'.$i)){
				$file = Input::file('materials-songs-'.$i);
				$user->addArtistSong($file);
			}

			if(Input::hasFile('materials-videos-'.$i)){
				$file = Input::file('materials-videos-'.$i);
				$user->addArtistVideo($file);
			}
		}

		return Redirect::to(action('HomeController@getIndex'));

	}

	public function getRegisterMaterials(){
    	return Redirect::to('/artist-register');
    }


	public function getRegisterAsVenue(){

		return View::make('auth.venue-register')->with('venue_types', $this->venue_types);

	}

	public function postRegisterAsVenue(){

		//trim and sanitize all inputs
		Input::merge(array_map('trim', Input::all()));
		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);

		$user = new User;

		$paypal_info = array(
			'email' 	=> $input['paypal-email'],
			'card_id' 	=> $input['paypal-card-id']
		);

		$social_media = array(
			'fb' 		=> $input['facebook_account'],
			'twitter' 	=> $input['twitter_account']
		);

		$address = array(
			'unit_street' 	=> $input['unit_street'],
			'city' 			=> $input['city'],
			'zipcode' 		=> $input['zipcode'],
			'state' 		=> $input['state'],
			'country' 		=> $input['country']
		);

		$user->user_type 	= 'venue';
		$user->status 		= 'active';
		$user->name 		= $input['name'];
		$user->password 	= Hash::make($input['password']);

		$user->description 	= $input['description'];
		$user->address 		= $address;
		$user->phone_number = $input['phone_number'];
		$user->email 		= $input['register-email'];
		$user->paypal_info 	= $paypal_info;

		//VENUE: Get venue types
		$venue_type = array();
		foreach ($this->venue_types as $key => $value) {
			if(Input::get($key) === 'on') array_push($venue_type, $key);
		}

		if(Input::get('other_venue_type', '') != ''){
			array_push($venue_type, $input['other_venue_type']);
		}

		$user->venue_type = $venue_type;

		if (!isset($input['operating_hrs_open']) || !isset($input['operating_hrs_close'])) {
			Input::flash();
			return Redirect::to('/venue-register')->with('error', 'Please provide the operating hours.');
		}

		$user->operating_hrs_open 	= $input['operating_hrs_open'];
		$user->operating_hrs_close 	= $input['operating_hrs_close'];
		$user->seating_capacity 	= $input['seating_capacity'];

		$user->serves_alcohol 	= false;
		$user->serves_food 		= false;
		if(Input::get('serves_alcohol') === 'on') {
			$user->serves_alcohol = true;
		}
		if(Input::get('serves_food') === 'on'){
			$user->serves_food = true;
		}

		$user->social_media = $social_media;

		if(sizeof(User::email($input['register-email'])->get()) > 0){
			Input::flash();
			return Redirect::to('/venue-register')->with('error', 'The given email address is already in use.');
		}else{

			$user->save();

			if(Input::hasFile('profile-picture')){
				$file = Input::file('profile-picture');
				$user->updateProfilePicture($file);
			}

			$user->save();
			$user->updateSessionWithUserDetails();
			Session::put('timezone_offset', $input['timezone_offset']);

			return Redirect::to(action('EventController@getMyEventsCalendar'))->with('success', 'You have successfully registered.');
		}

	}

	public function getValidateEmail(){

		$response = ['error' => false];

		//trim and sanitize all inputs

		Input::merge(array_map('trim', Input::all()));
		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);

		$email = $input['email'];

		//check if email already exists
		if(sizeof(User::email($email)->get()) > 0){
			$response['error'] = true;
		}

		return Response::json($response);
	}

	/* Update Profile */

	public function postUpdateProfile(){

		//trim and sanitize all inputs
		$input = Input::all();
		$user = User::find(Session::get('id'));

		if(Input::hasFile('input-profile-picture')){
			$file = Input::file('input-profile-picture');
			$user->updateProfilePicture($file);
		}

		$user->name = $input['name'];
		$user->email = $input['email'];
		$user->description = $input['description'];

		if($user['user_type'] === 'artist'){
			$user->ages = $input['ages'];
			$user->updateArtistGenreWithInput(Input::get('genre'));
		}
		elseif ($user['user_type'] === 'venue') {
			$venue_type = array();
			foreach ($this->venue_types as $key => $value) {
				if(Input::get($key) === 'on') array_push($venue_type, $key);
			}
			if(Input::get('other_venue_type', '') != '') array_push($venue_type, $input['other_venue_type']);
			$user->venue_type = $venue_type;
		}

		//GET ADDRESS
		if($user->user_type === 'artist'){
			$user->address = array(
				'city'		=> $input['city'],
				'zipcode'	=> $input['zipcode'],
			);
		}else{
			$user->address = array(
				'unit_street'	=> $input['unit_street'],
				'city'			=> $input['city'],
				'zipcode'		=> $input['zipcode'],
				'state'			=> $input['state'],
				'country'		=> $input['country']
			);
		}

		$user->phone_number = $input['phone_number'];

		//GET VENUE OPERATIONS DETAILS
		if($user['user_type'] === 'venue'){
			if(isset($input['operating_hrs_open'])) $user->operating_hrs_open = $input['operating_hrs_open'];

			if(isset($input['operating_hrs_close'])) $user->operating_hrs_close = $input['operating_hrs_close'];

			$user->seating_capacity = (int)$input['seating_capacity'];

			if(Input::get('serves_alcohol') === 'on')
				$user->serves_alcohol = true;
			else
				$user->serves_alcohol = false;

			if(Input::get('serves_food') === 'on')
				$user->serves_food = true;
			else
				$user->serves_food = false;
		}

		$social_media = $user->social_media;

		$social_media['fb'] = Input::get('facebook_account');
		$social_media['twitter'] = Input::get('twitter_account');

		if($user['user_type'] === 'artist'){
			$social_media['soundcloud'] = Input::get('soundcloud_account');
			$social_media['bandcamp'] = Input::get('bandcamp_account');
		}

		$user->social_media = $social_media;

		if(User::notUserId($user->id)->where('email', '=', $input['email'])->exists()){
			Input::flash();
			return Redirect::to('/settings')->with('warning', 'The given email address is already in use.');
		}
		$email = $input['email'];

		if(filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $email)){
			$user->save();
			Session::put('name', $user->name);
			return Redirect::to('/settings')->with('success', 'You have successfully updated your profile.');
		}else{
			Input::flash();
			return Redirect::to('/settings')->with('warning', 'Your email address is invalid.');
		}

	}

	/* Display Profile */

	public function getPublicProfile($user_id){

		$user = User::find($user_id);
		$equipments = Equipment::user($user_id)->get();

		if(empty($user))
			abort(404);

		if($user->status == 'inactive')
			abort(404);

		if($user->user_type == 'artist'){
			$songs  = Materials::userId($user_id)->where('type','=','song')->get();
			$images = Materials::userId($user_id)->where('type','=','image')->get();
			$videos = Materials::userId($user_id)->where('type','=','video')->get();

			return View::make('ourscene.profile', compact('user_id'))->with('user',$user)->with('equipments',$equipments)->with('songs',$songs)->with('images',$images)->with('videos',$videos);
		}

		return View::make('ourscene.profile', compact('user_id'))->with('user',$user)->with('equipments',$equipments)->with('venue_types', $this->venue_types);

	}

	public function getUserProfile(){

		$user_id = Session::get('id');
		$user 	 = User::find($user_id);

		if($user->user_type == 'artist'){
			$songs  = Materials::userId($user_id)->where('type','=','song')->get();
			$images = Materials::userId($user_id)->where('type','=','image')->get();
			$videos = Materials::userId($user_id)->where('type','=','video')->get();

			return View::make('ourscene.profile', compact('user_id'))->with('user',$user)->with('equipments',Equipment::user($user_id)->get())->with('songs',$songs)->with('images',$images)->with('videos',$videos);
		}

		return View::make('ourscene.profile', compact('user_id'))->with('user',$user)->with('equipments',Equipment::user($user_id)->get())->with('venue_types', $this->venue_types);
	}

	/* Profile Settings */

	public function getProfileSettings(){
		$id = Session::get('id');
		$user = User::find($id);
		$equipments = Equipment::user($id)->get();

		if(empty($user))
			return Redirect::to('/login');

		if($user->user_type === 'artist'){
			$songs 	= Materials::userId($id)->where('type','=','song')->get();
			$images = Materials::userId($id)->where('type','=','image')->get();
			$videos = Materials::userId($id)->where('type','=','video')->get();
			return View::make('ourscene.settings')->with('user', $user)->with('songs',$songs)->with('images',$images)->with('videos',$videos)->with('equipments',$equipments)->with('genres',$this->genres);
		}elseif($user->user_type === 'venue') {
			$full_address = $user->address['unit_street'].', '.$user->address['city'].', '.$user->address['state'].', '.$user->address['country'].' '.$user->address['zipcode'];
			$open = date('g:i A', strtotime($user->operating_hrs_open));
			$close = date('g:i A', strtotime($user->operating_hrs_close));
			return View::make('ourscene.settings')->with('user', $user)->with('full_address', $full_address)->with('open', $open)->with('close', $close)->with('venue_types',$this->venue_types)->with('equipments',$equipments);
		}
	}

	public function getAjaxMaterials(){
		$id = Session::get('id');
		$images = Materials::userId($id)->where('type','=','image')->get();
		return Response::json($images);
	}

	/* Login */

	public function getLogin(){

		if(Session::has('id'))
			return Redirect::to(action('HomeController@getIndex'));

		return View::make('auth.login');
	}

	public function proceedWithLogin($user, $password, $user_type,$offset){
		if($user){
			if(Hash::check($password, $user['password'])){
				$user->updateSessionWithUserDetails();
				Session::put('timezone_offset', $offset);
				if($user->status == 'inactive'){
					$user->status = 'active';
					$user->save();
					Session::put('status', $user['status']);
					return Redirect::to('/')->with('success', 'Your account has been reactivated.');
				}
				return Redirect::to('/');
			}
		}

		return Redirect::to('/login')
			->with('error', 'The email or password you entered is incorrect.')
			->with('attempt_login_as', $user_type);
	}

	public function postLoginArtist(){

		//trim and sanitize all inputs
		Input::merge(array_map('trim', Input::all()));
		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);

		$email = $input['email'];
		$password = $input['password'];
		$remember_me = isset($input['remember_me']);
		$offset = $input['timezone_offset'];

    	//add query to get user details
		$user = User::artists()->email($email)->first();

		return $this->proceedWithLogin($user, $password, 'artist', $offset);
	}

	public function postLoginVenue(){

		Input::merge(array_map('trim', Input::all()));
		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);

		$email 		= $input['email'];
		$password 	= $input['password'];
		$remember_me = isset($input['remember_me']);
		$offset 	= $input['timezone_offset'];

		$user = User::venues()->email($email)->first();

		return $this->proceedWithLogin($user, $password, 'venue', $offset);
	}

	/* Logout */

	public function getLogout(){

		Session::flush();

		return Redirect::to('/login');
	}

	/* Change Password */

	public function getChangePassword(){

		return View::make('auth.change-password');
	}

	public function postChangePassword(){
		$input = Input::all();
		$password = $input['password'];

		//get current user
		$user = User::userId(Session::get('id'))->first();

		if(empty($user))
			abort(404);

		$user->password = Hash::make($password);
		$user->save();

		return Redirect::to('/settings#account-info')->with('success', 'Your password was successfully updated.');
	}

	/* Update Payment Info */

	public function postAjaxUpdatePaymentInfo(){

		$data = Input::all();

		$paypal_info = array(
			'email' => $data['paypal-email']
		);

		$user = User::find(Session::get('id'));
        $user->paypal_info = $paypal_info;
        $user->update();

	}

	/* Validate current password */

	public function getValidateCurrentPassword(){

		$response = ['error' => true];

		$input = Input::all();

		//get current user
		$user = User::userId(Session::get('id'))->first();

		if(empty($user))
			abort(404);

		//check if passwords match
		if(Hash::check($input['password'], $user['password'])){
			$response['error'] = false;
		}

		return Response::json($response);
	}

	/* Deactivate Account */

	public function getDeactivateAccount(){

		$user = User::find(Session::get('id'));
		$user->status = 'inactive';
		$user->save();

		Session::flush();

		return Redirect::to(action('UserController@getLogin'))
			->with('success', 'Your account is successfully deactivated.')
			->with('attempt_login_as', $user->user_type);
	}

	/* Reactivate Account */

	public function getReactivateAccount(){

		$user = User::find(Session::get('id'));
		$user->status = 'active';
		$user->save();

		Session::put('status', $user['status']);

		return Redirect::to(action('UserController@getProfileSettings').'#account-info')->with('success', 'Your account is successfully reactivated.');
	}

	/* Forgot Password */

	public function getForgotPassword(){

		if(Session::has('id'))
			return Redirect::to('/');

		return View::make('ourscene.forgot-password');

	}

	public function postForgotPassword(){

		//trim and sanitize all inputs
		Input::merge(array_map('trim', Input::all()));
		$input = filter_var_array(Input::all(), FILTER_SANITIZE_STRIPPED);

		//generate random password
		$password = bin2hex(openssl_random_pseudo_bytes(5));

		$email = $input['email'];

		//get user
		$user = User::where('email', '=', $email)->first();

		if(empty($user)){
			$response['error'] = 'The email address you entered is not registered.';
			return $response;
		}

		//add user password token (this is for the link only)
		$user->password = Hash::make($password);
		$user->save();

		$data['user'] 		= $user;
		$data['password'] 	= $password;

		//send email for temporary password
		EmailSender::emailTemporaryPassword($data);

		$response['success'] = 'A temporary password has been sent to your email.';
		return $response;
	}

	/* Artist material */
	public function updateArtistMaterials(){
		
		$user = User::find(Session::get('id'));

		for($i=1; $i<=5; $i++){

			if(Input::hasFile('materials-images-'.$i)){
				$file = Input::file('materials-images-'.$i);
				$user->addArtistImage($file);
			}

			if(Input::hasFile('materials-songs-'.$i)){
				$file = Input::file('materials-songs-'.$i);
				$user->addArtistSong($file);
			}

			if(Input::hasFile('materials-videos-'.$i)){
				$file = Input::file('materials-videos-'.$i);
				$user->addArtistVideo($file);
			}
		}

		return Redirect::to('settings#equipment');
	}

	public function deleteMaterial($id){

		$material = Materials::find($id);

		Session::put('material_id', $id);

		return View::make('auth.delete-material')->with('material', $material);

	}

	public function postAjaxDeleteMaterial(){

		$data = Input::all();
		$material_id = $data['material_id'];

		$material = Materials::find($material_id);
		$return_data['success'] = false;
		$return_data['message'] = "Deletion of material failed";

		if(empty($material))
			return json_encode($return_data);

		$material_url = str_replace('https://s3.amazonaws.com/'.getenv('S3_BUCKET'), '', $material->url);
		$s3 = \Storage::disk('s3');

		if(!is_null($material) && $s3->exists($material_url)){
			$s3->delete($material_url);
			$material->status = "deleted";
			$material->delete();
			$return_data['success'] = true;
			$return_data['message'] = "Material successfuly removed";
		}else{
			$material->delete();
			$return_data['message'] = "Material does not exist";
		}

		return json_encode($return_data);

	}

	/* Autocomplete */

	public function getAutocompleteArtists(){

		$term = Input::get('term');

		$results = [];

		$query = User::artists()
			->where('name', 'LIKE', '%'.$term.'%');

		$artists = $query->get();

		foreach($artists as $artist)
			$results[] = ['artist_id' => $artist['id'], 'value' => $artist['name']];

		return Response::json($results);
	}

	public function getAutocompleteVenues(){

		$term = Input::get('term');

		$results = [];

		$query = User::venues()->where('name', 'LIKE', '%'.$term.'%');

		$venues = $query->get();

		foreach($venues as $venue){
			$results[] = ['venue_id' => $venue['id'], 'value' => $venue['name']];
		}

		return Response::json($results);
	}


	public function ajaxUpdateEquipment(){

		//get inputs

		$input = Input::all();

		$id = $input['id'];
		$name = $input['name'];
		$inclusion = explode(",",$input['inclusion']);

		$type = ($input['type'] == 'default')? 'default' : 'others';

		$equipment = Equipment::where('_id', $id)->first();

		//delete equipment

		if($equipment)
			$equipment->delete();
		else
			return Response::json("error",501);

		//create new equipment

		$user = User::find(Session::get('id'));

		$equipment = new Equipment;

		$equipment->user_id = $user->id;
		$equipment->owner 	= $user->name;
		$equipment->type 	= $type;
		$equipment->name 	= $name;
		$equipment->inclusion = $inclusion;

		if($equipment->save()){
			return Response::json("save",201);
		}else{
			return Response::json("error",501);
		}

	}

	public function ajaxDeleteEquipment(){
		$input = Input::all();
		$id = $input['id'];
		$equipment = Equipment::where('_id',$id)->first();
		if ($equipment->delete()) {
			return Response::json("deleted",201);
		}else {
			return Response::json("error",501);
		}
		return $equipment;
	}

	public function ajaxGetEquipment(){
		$user = User::find(Session::get('id'))->_id;
		$equipments = Equipment::where('user_id',Session::get('id'))->get();
		return Response::json($equipments);
	}
}
