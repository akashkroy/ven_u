<?php
namespace OurScene\Http\Controllers;
use Session;
use Log;
use Redirect;

use OurScene\Models\User;

class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(){
		$this->middleware('auth.login',
			['except' =>
				['getPrivacyPolicy','getCopyrightPolicy','getTermsOfService','getFAQ','getAboutUs']
			]
		);
	}


	public function getIndex(){

		$code = $message = '';

		if(Session::has('success')){
			$code = 'success';
			$message = Session::get('success');
		}

		if(Session::has('error')){
			$code = 'error';
			$message = Session::get('error');
		}

		return Redirect::action('SearchController@getSearch')->with($code, $message);
	}

	public function getPublicCalendar($id){

		$public_user = User::find($id);
		$user_type = $public_user['user_type'];
		$user_name = $public_user['name'];
		$is_artist = $user_type == 'artist';

		return view('publicuser')->with('user_id', $id)->with('is_artist', $is_artist)->with('name', $user_name);

	}

	public function getPrivacyPolicy(){
		return view('ourscene/legal/privacy-policy');
	}

	public function getCopyrightPolicy(){
		return view('ourscene/legal/copyright-policy');
	}

	public function getTermsOfService(){
		return view('ourscene/legal/terms-of-service');
	}

	public function getAboutUs(){
		if(Session::has('id'))
			return view('ourscene/static/about-us-page');
		return view('auth/about-us-page');

	}
	public function getFAQ(){
		if(Session::has('id'))
			return view('ourscene/static/faq-page');
		return view('auth/faq-page');
	}

	public function index(){

		return view('auth.login');

	}

}
