<?php namespace OurScene\Models;

use Log;
use Session;

use Jenssegers\Mongodb\Model as Eloquent;
use OurScene\Models\Service;

class Event extends Eloquent {

	protected $connection = 'mongodb';

	protected $collection = 'events';

	public $timestamps = false;

	public static function getPrivateEventsByUserId($id, $start_datetime, $end_datetime){

		if(Session::get('user_type') == 'venue'){

			//the venue of the event is the current user (venue)
			//and the status of the event is not rejected
			$condition = array(
				'$and'	=> array(
					array(
						'start_datetime' => array(
							'$gt'	=> $start_datetime,
							'$lt'	=>	$end_datetime
						)
					),	
					array(	
						'$or'	=> array(
							array(
								'user_id'	=> $id,
								'status' => array(
									'$nin'	=>	['cancelled']
								)
							),
							array(
								'$and'	=> array(
									array(
										'venue.id'	=> $id
									),
									array(
										'status' => array(
											'$nin'	=>	['rejected', 'cancelled']
										)
									)
								)
							)
						)
					)
				)
			);
		}
		else if(Session::get('user_type') == 'artist'){

			//the current user (artist) is in the services lineup

			$condition = array(
				'$and'	=> array(
					array(
						'start_datetime' => array(
							'$gt'	=> $start_datetime,
							'$lt'	=>	$end_datetime
						)
					),
					array(
						'status' => array(
							'$nin'	=>	['cancelled']
						)
					),
					array(
						'$or'	=> array(
							array(
								'user_id'	=> $id
							),
							array(
								'services_lineup.artist_id'	=> $id
							)
						)
					)
				)
			);
		}

		return Event::where($condition);
	}

	public static function getPublicEventsByUserId($user_id, $user_type, $start_datetime, $end_datetime){

		if($user_type == 'venue'){

			//the venue of the event is the current user (venue)
			//and the status of the event is not rejected
			$condition = array(
				'$and'	=> array(
					array(
						'start_datetime' => array(
							'$gt'	=> $start_datetime,
							'$lt'	=>	$end_datetime
						)
					),
					array(
						'$or'	=> array(
							array(
								'user_id'	=> $user_id,
								'status' => array(
									'$nin'	=>	['cancelled']
								)
							),
							array(
								'$and'	=> array(
									array(
										'venue.id'	=> $user_id
									),
									array(
										'status' => array(
											'$nin'	=>	['pending', 'rejected', 'cancelled']
										)
									)
								)
							)
						)
					)
				)
			);
		}
		else if($user_type == 'artist'){

			//the current user (artist) is in the services lineup

			$condition = array(
				'$and'	=> array(
					array(
						'start_datetime' => array(
							'$gt'	=> $start_datetime,
							'$lt'	=>	$end_datetime
						)
					),
					array(
						'status' => array(
							'$nin'	=>	['pending', 'rejected', 'cancelled']
						)
					),
					array(
						'$or'	=> array(
							array(
								'user_id'	=> $user_id
							),
							array(
								'services_lineup.artist_id'	=> $user_id
							)
						)
					)
				)
			);
		}

		return Event::where($condition);
	}

	public static function getEventsByUser($id){

		#if id is in service line up or is venue
		$service_condition = array(
			'services_lineup.service.artist_id'	=> array(
				'$in' => array($id)
			),
			'status' => array('$in' => array('confirmed'))
		);

		$venue_condition = array(
				'venue.id' => array('$in' => array($id))
		);

		return Event::where('user_id', $id)->orWhere($service_condition)->orWhere($venue_condition);
	}

	public static function getRequestedPerformances($id){

		$conditions = array(
			'service_lineup.service.status' => array(
				'$in' => array('pending')
			)
		);

		return Event::where('user_id', $id)->orWhere('venue.venue_id', $id)->where($conditions);
	}

	public static function artistExists($event_id, $artist_id){

		$conditions = array(
			'service_lineup.service.artist_id'	=> array(
				'$in' => array($artist_id)
			)
		);
		$events = Event::where('_id', $event_id)->where($conditions)->get();

		return count($events) != 0;
	}

	public function scopeCreateEvent($query, $event){
		$new_event = new Event;

		$new_event->title = $event['title'];
		$new_event->start_datetime = $event['start_datetime'];
		$new_event->end_datetime  = $event['end_datetime'];
		$new_event->opening_time = $event['opening_time'];
		$new_event->event_type = $event['event_type'];
		$new_event->age_requirements = $event['age_requirements'];
		$new_event->description = $event['description'];
		$new_event->cover_charge = $event['cover_charge'];
		$new_event->user_id = $event['user_id'];
		$new_event->status = 'draft';
		$new_event->confirm_date = '';
		$new_event->venue = $event['venue_container'];

		$new_event->save();

		return $new_event;
	}
}
