<?php
use OurScene\Models\User;
use OurScene\Models\Materials;
use Illuminate\Contracts\Filesystem\Filesystem;


function array_map_recursive($callback, $array){
	foreach ($array as $key => $value) {
		if (is_array($array[$key])) {
			$array[$key] = array_map_recursive($callback, $array[$key]);
		} else {
			$array[$key] = call_user_func($callback, $array[$key]);
		}
	}

	return $array;
}

function clean($string) {
   // $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\.-]/', '', $string); // Removes special chars.
}

function getProfilePicture($id){
	$user = User::find($id);

	if($user == null || $user->image == null){
		return asset('images/icons/profile-pic.png');
	}

	return $user->image;
}

function getHyperLink($url){
	$return_url = "";
	if( (strlen($url) < 7) || ((substr( $url , 0, 7 ) != 'http://') && (substr( $url , 0, 8 ) != 'https://')) ){
		$return_url = 'http://';
	}
	$return_url = $return_url . $url;

	return $return_url;
}

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) 
    {
        case 'g':
        $val *= 1024;
        case 'm':
        $val *= 1024;
        case 'k':
        $val *= 1024;
    }
    return $val;
}

function max_file_upload_in_bytes() {
    //select maximum upload size
    $max_upload = return_bytes(ini_get('upload_max_filesize'));
    //select post limit
    $max_post = return_bytes(ini_get('post_max_size'));
    //select memory limit
    $memory_limit = return_bytes(ini_get('memory_limit'));
    // return the smallest of them, this defines the real limit
    return min($max_upload, $max_post, $memory_limit);
}

function getTimeZoneFromJSOffset($offset){
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


function getUTCDateTime($date, $time, $timezone_offset){
    $date_string = $date." ".$time." ".getTimeZoneFromJSOffset($timezone_offset);
    return date('Y-m-d\TH:i:sP', strtotime($date_string));
}

function upload_material_toS3($file, $path, $title, $user_id, $type){
//upload to S3
    $s3 = \ Storage::disk('s3');
    $path = str_replace("'", '', $path);
    $s3->put($path, file_get_contents($file), 'public');

    $materials = new Materials;
    $materials->user_id = $user_id;
    $materials->title = $title;
    $materials->type = $type;
    $materials->url = getenv('S3_ENDPOINT').$path;
    $saved = $materials->save();

}

function getNumberOfUnreadConversation($user_id){
    return Message::getNumberOfUnreadConversation($user_id);
}