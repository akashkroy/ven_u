<?php
namespace OurScene\Helpers;

use Session;
use Log;
use Redirect;

use OurScene\Models\User;


use Google_Client;
use Google_Service_Calendar;
use Google_Service_Exception;
use Google_Service_Calendar_Event;
use Google_Auth_AssertionCredentials;
use Google_Auth_AppIdentity;
use Google_Service_Calendar_EventDateTime;

const CALENDAR_SCOPE = "https://www.googleapis.com/auth/calendar";

class GoogleCalendarHelper{

	public static function isNewlyAuthorized(){
		return isset($_GET['code']);
	}

	public static function setUpNewClient(){
		$client = new Google_Client();
		$client->setApplicationName("Our Scene");
		$client->setRedirectUri('http://staging.codesignate.com/dev/authenticate-gc');
		$client->setScopes(['https://www.googleapis.com/auth/calendar','https://www.googleapis.com/auth/userinfo.email']);
		$client->setAccessType("offline");
		$client->setApprovalPrompt("force");
		$client->setAuthConfigFile(base_path() . '/resources/assets/local-client-secret.json');
		return $client;
	}

	public static function getUserGoogleCalendarConfigFromClient($client){
		//NOTE: client->accesstoken = [accestoken, refreshtoken, created, etc...]
		$user = User::find(Session::get('id'));
		$gcalendar = $user->gcalendar;
		$gcalendar['token'] = $client->getAccessToken();
		$gcalendar['allow'] = true;
		return $gcalendar;
	}

	public static function getClient(){
		
		$client = self::setUpNewClient();
		$user = User::find(Session::get('id'));

		if (self::isNewlyAuthorized()){

		  $client->authenticate($_GET['code']);
		  $user->gcalendar = self::getUserGoogleCalendarConfigFromClient($client);
		  $user->save();

		}

		//Check if there is a previous access token saved.
		if($user->gcalendar['token'] != null){

		  	$user_google_token = $user->gcalendar['token'];
		  	$client->setAccessToken($user_google_token);

			if ($client->isAccessTokenExpired()) {

				try{
				$refreshToken = $user_google_token['refresh_token'];
			    $client->refreshToken($refreshToken);

			    $newAccessToken = $client->getAccessToken();
			    //$newAccessToken['refresh_token'] = $refreshToken;

			    $gcalendar = $user->gcalendar;
			    $gcalendar['token']['access_token'] = $newAccessToken['access_token'];
			    $gcalendar['allow'] = true;
			  
			    $user->gcalendar = $gcalendar;
			    $user->save();

				}catch(Exception $e){

				}
			}
		}

		return $client;
	}

	public static function getAuthenticatedUserEmail(){
		$client = self::getClient();
		$q = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$client->getAccessToken()['access_token'];
		$json = file_get_contents($q);
		$userInfoArray = json_decode($json,true);
		$googleEmail = $userInfoArray['email'];
		return $googleEmail;
	}

	// Create google calendar event if API is intergrated
	public static function insertGoogleCalendarEvent($event,$start_datetime,$end_datetime){

		$client = GoogleCalendarHelper::getClient();
		$service = new Google_Service_Calendar($client);
		$google_event = new Google_Service_Calendar_Event();


		$start = new Google_Service_Calendar_EventDateTime();
		$end = new Google_Service_Calendar_EventDateTime();

		$start->setDateTime($start_datetime);
		$end->setDateTime($end_datetime);

		$google_event->setSummary($event['title']);
		$google_event->setStart($start);
		$google_event->setEnd($end);
		$google_event->setLocation($event['venue']['name']);

		$google_event = $service->events->insert('primary', $google_event);
		$event->google_event_id = $google_event['id'];
		$event->save();

	}

	// Update intergrated google calendar
	public static function updateGoogleCalendarEvent($event,$start_datetime,$end_datetime){
		

		if($event['google_event_id'] == null){
			self::insertGoogleCalendarEvent($event,$start_datetime,$end_datetime);
			return;
		}

		$client 	= GoogleCalendarHelper::getClient();
		$service 	= new Google_Service_Calendar($client);
		$google_event = $service->events->get('primary', $event['google_event_id']);

		// Google Calendar Date time
		$start 	= new Google_Service_Calendar_EventDateTime();
		$end 	= new Google_Service_Calendar_EventDateTime();

		$start->setDateTime($start_datetime);
		$end->setDateTime($end_datetime);

		$google_event->setSummary($event['title']);
		$google_event->setStart($start);
		$google_event->setEnd($end);
		$google_event->setLocation($event['venue']['name']);

		$google_event = $service->events->update('primary', $google_event->getId(), $google_event);

	}

	// Create google calendar
	public static function createGoogleCalendarEvent(){

		$client = GoogleCalendarHelper::getGoogleClient();

		// Load previously authorized credentials from a file.
		$credentialsPath = (CREDENTIALS_PATH);
		if (file_exists($credentialsPath)) {
			$accessToken = json_decode(file_get_contents($credentialsPath), true);
			$accessToken = $accessToken['access_token'];
			return '/gc?token='.$accessToken;
		} else {
			// Request authorization from the user.
			$authUrl = $client->createAuthUrl();
			return $authUrl;
		}
	}
}
?>
