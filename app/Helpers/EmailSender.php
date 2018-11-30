<?php

namespace OurScene\Helpers;

use OurScene\Models\User;
use OurScene\Models\Event;
use Session;
use Mail;

class EmailSender{

	/* Event */

	public static function updateEvent($event, $service, $venue, $artist){

		return Mail::send('emails.update-event', ['event' => $event, 'service' => $service, 'venue' => $venue, 'artist' => $artist], function ($m) use ($artist, $event) {
			$m->to($artist->email, $artist->name)->subject('Updated event: '.$event->title);
		});
	}

	public static function cancelEvent($event, $service, $venue, $artist){

		return Mail::send('emails.cancel-event', ['event' => $event, 'service' => $service, 'venue' => $venue, 'artist' => $artist], function ($m) use ($artist, $event) {
			$m->to($artist->email, $artist->name)->subject('Cancelled event: '.$event->title);
		});
	}

	/* Performance */

	public static function cancelPerformance($event, $service, $venue, $artist){

		return Mail::send('emails.cancel-performance', ['event' => $event, 'service' => $service, 'venue' => $venue, 'artist' => $artist], function ($m) use ($artist, $event) {
			$m->to($artist->email, $artist->name)->subject('Cancelled performance: '.$event->title);
		});
	}

	public static function updatePerformanceTime($event, $service, $venue, $artist){

		return Mail::send('emails.update-performance-time', ['event' => $event, 'service' => $service, 'venue' => $venue, 'artist' => $artist], function ($m) use ($artist, $event) {
			$m->to($artist->email, $artist->name)->subject('Adjustments on your performance time: '.$event->title);
		});
	}

	/* Request for service */

	public static function requestForService($event, $service, $venue, $artist){

		return Mail::send('emails.request-for-service', ['event' => $event, 'service' => $service, 'venue' => $venue, 'artist' => $artist], function ($m) use ($artist, $event) {
			$m->to($artist->email, $artist->name)->subject('Request for service: '.$event->title);
		});

	}

	public static function confirmRequestForService($event, $service, $venue, $artist){

		return Mail::send('emails.confirm-request-for-service', ['event' => $event, 'service' => $service, 'venue' => $venue, 'artist' => $artist], function ($m) use ($venue, $event) {
			$m->to($venue->email, $venue->name)->subject('Accepted request for service: '.$event->title);
		});

	}

	public static function rejectRequestForService($event, $service, $venue, $artist){

		return Mail::send('emails.reject-request-for-service', ['event' => $event, 'service' => $service, 'venue' => $venue, 'artist' => $artist], function ($m) use ($venue, $event) {
			$m->to($venue->email, $venue->name)->subject('Declined request for service: '.$event->title);
		});

	}

	public static function cancelRequestForService($event, $service, $venue, $artist){

		return Mail::send('emails.cancel-request-for-service', ['event' => $event, 'service' => $service, 'venue' => $venue, 'artist' => $artist], function ($m) use ($artist, $event) {
			$m->to($artist->email, $artist->name)->subject('Cancelled request for service: '.$event->title);
		});
	}

	/* Request for performance */

	public static function requestForPerformance($event, $service, $venue, $artist){

		return Mail::send('emails.request-for-performance', ['event' => $event, 'service' => $service, 'venue' => $venue, 'artist' => $artist], function ($m) use ($venue, $event) {
			$m->to($venue->email, $venue->name)->subject('Request for performance: '.$event->title);
		});

	}

	public static function confirmRequestForPerformance($event, $service, $venue, $artist){

		return Mail::send('emails.confirm-request-for-performance', ['event' => $event, 'service' => $service, 'venue' => $venue, 'artist' => $artist], function ($m) use ($artist, $event) {
			$m->to($artist->email, $artist->name)->subject('Accepted request for performance: '.$event->title);
		});

	}

	public static function rejectRequestForPerformance($event, $service, $venue, $artist){

		return Mail::send('emails.reject-request-for-performance', ['event' => $event, 'service' => $service, 'venue' => $venue, 'artist' => $artist], function ($m) use ($artist, $event) {
			$m->to($artist->email, $artist->name)->subject('Declined request for performance: '.$event->title);
		});

	}

	public static function cancelRequestForPerformance($event, $service, $venue, $artist){

		return Mail::send('emails.cancel-request-for-performance', ['event' => $event, 'service' => $service, 'venue' => $venue, 'artist' => $artist], function ($m) use ($venue, $event) {
			$m->to($venue->email, $venue->name)->subject('Cancelled request for performance: '.$event->title);
		});

	}

	/* User */

	public static function emailTemporaryPassword($data){

		$user = $data['user'];
		$password = $data['password'];

		$response = Mail::send('emails.password', ['user' => $user, 'password' => $password], function ($m) use ($user) {
			$m->to($user->email, $user->name)->subject('Password Recovery');
		});
	}

}

?>
