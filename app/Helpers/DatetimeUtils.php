<?php

namespace OurScene\Helpers;
use DateTimeZone;
use MongoDate;
use Session;
use DateTime;

class DatetimeUtils{

	public static function datetimeGreaterThan($datetime1, $datetime2){
	
		return $datetime1 > $datetime2;
	}

	public static function datetimeInRange($datetime1, $datetime2, $datetime){
		return $datetime >= $datetime1 && $datetime <= $datetime2;
	}

	public function convertUTCtoLocalDate(){
		
	}

	public static function formatDateFromBackendToFrontEnd($sec){
		
		return date("m/d/Y", $sec);
	}

	public static function formatTimeFromFrontendToBackend($time){
		
		return date("H:i", strtotime($time));
	}

	public static function formatTimeFromBackendToFrontend($sec){
		
		return date("h:i A", $sec);
	}

	public static function getTimezoneNameFromOffset(){

		$offset = (Session::get('timezone_offset') / -60) * 3600;

		$timezone_list = require('TimezoneList.php');

		return $timezone_list[$offset];
	}

	public static function convertMongoUTCDatetimeToMongoClientDatetime($utc_datetime){
		
		$offset = Session::get('timezone_offset') / -60;
		$is_daylight_saving_time = false;
		$timezone_name = timezone_name_from_abbr('', $offset * 3600, $is_daylight_saving_time);
		
		if(!$timezone_name){
			$timezone_name = DatetimeUtils::getTimezoneNameFromOffset();	
		}

		$client_timezone = new DateTimeZone($timezone_name);

		//$client_timezone = DateTime::createFromFormat('O', getTimeZoneFromJSOffset(Session::get('timezone_offset')))->getTimezone();

		$adjusted_datetime = new DateTime($utc_datetime->toDateTime()->format('Y-m-d H:i:s'), new DateTimeZone('UTC'));
		$adjusted_datetime = $adjusted_datetime
			->setTimeZone($client_timezone)
			->format('Y-m-d H:i:s');

		$client_datetime = new MongoDate(strtotime($adjusted_datetime));
		
		return $client_datetime;
	}

	public static function generateMongoUTCDatetime($date, $militaryTime, $timezone_offset){

		$tz_offset = isset($timezone_offset) ? $timezone_offset : Session::get('timezone_offset');
		$offset = self::getTimeZoneFromJSOffset($tz_offset);

		$epoch = self::getEpochWithTimezone($date, $militaryTime, $offset);

		$utc_datetime = new MongoDate($epoch);

		return $utc_datetime;
	}

	public static function convertMongoClientDatetimeToMongoUTCDatetime($client_datetime){

		$offset = Session::get('timezone_offset') / -60;
		$is_daylight_saving_time = false;
		$timezone_name = timezone_name_from_abbr('', $offset * 3600, $is_daylight_saving_time);
		
		if(!$timezone_name){
			$timezone_name = DatetimeUtils::getTimezoneNameFromOffset();	
		}

		$client_timezone = new DateTimeZone($timezone_name);

		//$client_timezone = DateTime::createFromFormat('O', getTimeZoneFromJSOffset(Session::get('timezone_offset')))->getTimezone();

		$adjusted_datetime = new DateTime($client_datetime->toDateTime()->format('Y-m-d H:i:s'), $client_timezone);
		$adjusted_datetime = $adjusted_datetime
			->setTimeZone(new DateTimeZone('UTC'))
			->format('Y-m-d H:i:s');
		
		$utc_datetime = new MongoDate(strtotime($adjusted_datetime));

		return $utc_datetime;
	}

	public static function getTimeZoneFromJSOffset($offset){
	    $hrs = $offset / -60 ;
	    $mins = $offset % 60;

	    $timezone = "";
	    if($hrs<10){
	        $timezone = "+0";
	    }
	    $timezone = $timezone . $hrs . ":";
	    if($mins<10){
	        $timezone = $timezone . "0";
	    }
	    $timezone = $timezone . $mins;

	    return $timezone;
	}

	public static function getEpochWithTimezone($date, $militaryTime,$offset){

	    $date_epoch = strtotime($date." 00:00:00");
	    $date = new \DateTime();
	    $date->setTimestamp($date_epoch);

	    $date_string = $date->format('d/M/Y');

	    $string_to_convert = $date_string . ":" . $militaryTime . ":00 " . $offset;

	    return strtotime($string_to_convert);
	}

	public static function getUTCDateTime($date, $time, $timezone_offset){
	    $date_string = $date." ".$time." ".self::getTimeZoneFromJSOffset($timezone_offset);
	    return date('Y-m-d\TH:i:sP', strtotime($date_string));
	}
}

?>