<?php
	use OurScene\Models\Service;
	use OurScene\Models\User;
	use OurScene\Helpers\DatetimeUtils;
?>


@extends('ourscene.layouts.main')

@section('head')

@endsection

@section('content')

<div id="view-event">

	<div id="page-nav-buttons">
	@if(URL::previous() != action('PaypalController@getPayOurscene'))
		<a class="btn page-nav-btn" href="{{ URL::previous() }}">
			<img src="{{ asset('images/icons/back-purple.svg') }}"/>&nbsp;&nbsp;Back
		</a>
	@endif
	</div>

	<div id="view-event-container" class=" card">

		<!-- Venue profile -->

		<div id="venue-profile">
			<div class="row">
				<div class="col s12 m8 l8">
					<div class="circular-img-container profile-pic" style="background-image: url('{{ getProfilePicture($event['venue']['id']) }}')"></div>
					<span class="name">{{ $event['venue']['name'] }}</span>
				</div>
				<div class="col s12 m4 l4 right-align">
					<div class="left-align" style="display: inline-block;">

					@if($event['status'] == 'pending')
						<span class="reject-color">PENDING</span>
						@if(Session::get('user_type') == 'artist' && Session::get('id') == $event['user_id'])
							&nbsp;&nbsp; <a href="#cancel-request-for-performance-modal" class="btn ourscene-btn-3 modal-trigger">Cancel request</a>
						@endif
					@elseif($event['status'] == 'rejected')
						<span class="reject-color">NEXT TIME</span>
					@elseif($event['status'] == 'cancelled')
						<span class="reject-color">CANCELLED</span>
					@elseif($event['status'] == 'confirmed')
						@if(Session::get('user_type') == 'venue' && Session::get('id') == $event['venue']['id'])
							@if(DatetimeUtils::datetimeGreaterThan(new MongoDate(), $event->end_datetime))
								<a class="btn ourscene-btn-4 disabled">Edit Event details</a>
							@else
								<a href="{{ action('EventController@getEditEvent', array('id' => $event['_id'])) }}" class="btn ourscene-btn-4">Edit Event details</a>
							@endif
							<br/>
						@elseif(Session::get('user_type') == 'artist')
							@if(DatetimeUtils::datetimeGreaterThan(new MongoDate(), $event->end_datetime))
								<a class="btn ourscene-btn-2 disabled">Request</a>
							@else
								<a href="#request-for-performance-modal" class="btn ourscene-btn-2 modal-trigger">Request</a>
							@endif
							<br/>
						@endif
					@endif

					@if($event['pay_to_play'])
						<div id="pay-to-play">
							<img id="pay-to-play-icon" src="{{ asset('images/icons/pay-to-play.svg') }}"/>
							Pay to play
						</div>
					@endif

					</div>
				</div>
			</div>
		</div>

		<!-- Cover photo -->

		<div id="cover-photo"></div>

		<!-- Event title -->

		<div id="event-title" class="card-action center-align">{{ $event['title'] }}</div>



		<div id="event-main-section" class="card-action">

			<div class="row">

				<!-- Event navigation -->

				<div id="event-navigation" class="col s12 m12 l6 offset-l3">
					<ul class="tabs">
						<li class="tab col s4"><a class="active" href="#event-details">Event details</a></li>
						<li class="tab col s4"><a href="#artist-line-up">Artist Line up</a></li>
					@if(Session::get('user_type') == 'venue' && Session::get('id') == $event['venue']['id'])
						<li class="tab col s4"><a href="#invited-artists">Invited artists</a></li>
						<li class="tab col s4"><a href="#artist-applied">Artist Applied</a></li>
					@endif
					</ul>
				</div>

				<!-- Event details -->

				<div id="event-details" class="col s12">
					<div id="contacts" class="card-action">
						<div class="row">
							<div class="col s3 m3 l3 center-align">
								<img src="{{ asset('images/icons/contact.svg') }}"/><br/>
								<div class="label">Contact No.</div>
								<div class="divider"></div>
								{{ $event_venue['phone_number'] }}
							</div>
							<div class="col s3 m3 l3 center-align">
								<img src="{{ asset('images/icons/email.svg') }}"/><br/>
								<div class="label">Email</div>
								<div class="divider"></div>
								{{ $event_venue['email'] }}
							</div>
							<div class="col s3 m3 l3 center-align">
								<img src="{{ asset('images/icons/website.svg') }}"/><br/>
								<div class="label">Facebook</div>
								<div class="divider"></div>
								<a href="{{ getHyperLink($event_venue['social_media']['fb']) }}" class="ourscene-link-1" target="_blank">{{ $event_venue['social_media']['fb'] }}</a>
							</div>
							<div class="col s3 m3 l3 center-align">
								<img src="{{ asset('images/icons/website.svg') }}"/><br/>
								<div class="label">Twitter</div>
								<div class="divider"></div>
								<a href="{{ getHyperLink($event_venue['social_media']['twitter']) }}" class="ourscene-link-1" target="_blank">{{ $event_venue['social_media']['twitter'] }}</a>
							</div>

						</div>
					</div>

					<div id="details" class="card-action">

						<div class="detail">
							<div class="label">When</div>
							<br/>
							{{ date('F d, Y h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($event['start_datetime'])->sec) }}
							-
							{{ date('F d, Y h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($event['end_datetime'])->sec) }}
						</div>
						@if(isset($event['opening_time']) && $event['opening_time']!="")
						<div class="detail">
							<div class="label">Doors</div>
							<br/>
							{{ date('h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($event['opening_time'])->sec) }}
						</div>
						@endif
						<div class="detail">
							<div class="label">About the event</div>
							<br/>
							{{ $event['description'] }}
						</div>
						<div class="detail">
							<div class="label">Event type</div>
							<br/>
							{{ $event['event_type'] }}
						</div>
						<div class="detail">
							<div class="label">Age requirements</div>
							<br/>
							{{ $event['age_requirements'] }}
						</div>
						<div class="detail">
							<div class="label">Cover charge</div>
							<br/>
							{{ $event['cover_charge'] }}
						</div>
						<div class="detail">
							<div class="label">Address</div>
							<br/>
						@if(isset($event_venue['address']['unit_street']))
							<span class="bold-weight">Unit/Street</span><br/>{{ $event_venue['address']['unit_street'] }}<br/><br/>
						@endif
						@if(isset($event_venue['address']['city']))
							<span class="bold-weight">City</span><br/>{{ $event_venue['address']['city'] }}<br/><br/>
						@endif
						@if(isset($event_venue['address']['zipcode']))
							<span class="bold-weight">Zipcode</span><br/>{{ $event_venue['address']['zipcode'] }}<br/><br/>
						@endif
						@if(isset($event_venue['address']['state']))
							<span class="bold-weight">State</span><br/>{{ $event_venue['address']['state'] }}<br/><br/>
						@endif
						@if(isset($event_venue['address']['country']))
							<span class="bold-weight">Country</span><br/>{{ $event_venue['address']['country'] }}<br/><br/>
						@endif
						</div>
					</div>
				</div>

				<!-- Artist line up -->

				<div id="artist-line-up" class="col s12 card-action">
					<div class="row">

						<!-- Services line up -->

						<div id="services-lineup" class="col s12 m12 l7">
							<div class="label big-label">Artist schedule</div>
							<br/>

						@if(count($event['services_lineup']))
							<ul id="services-collapsible" class="collapsible" data-collapsible="expandable">
							@foreach($event['services_lineup'] as $service)
								<li>
									<div class="collapsible-header">
										<img class="dropdown-icon" src="{{ asset('images/icons/dropdown.svg') }}">
										<span class="artist-name big-label">{{ $service['artist_name'] }}</span>
									</div>
									<div class="collapsible-body expand-service">

										<!-- Performance time -->

										<div class="label">Performance time</div>
										<br/>
										<span class="bold-weight">Start</span><br/> {{ date('F d, Y h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service['start_datetime'])->sec) }} <br/>
										<span class="bold-weight">End</span><br/> {{ date('F d, Y h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service['end_datetime'])->sec) }} <br/>

										<br/>

										<!-- Artist equipments -->

										<div class="label">
											<img class="equipment-icon" src="{{ asset('images/icons/house-equipment.svg') }}">
											Artist equipment
											
										@if($service['artist_id'] == Session::get('id'))
											<!-- Edit artist lineup equipment button -->

											<a class="btn ourscene-btn-2 right" onClick="showEditArtistLineupEquipmentModal('{{ $service['service_id'] }}', {{ json_encode($service['equipments']) }})">Edit</a>
										@endif

										</div>
									@if(count($service['equipments']))
										<ul>
										@foreach($service['equipments'] as $equipment)
											<li>
												<div class="equipment">
													<span class="bullet-symbol">&bull;</span>
													{{ $equipment['name'] }}<br/>
												</div>
											@foreach($equipment['inclusion'] as $inclusion)
												&nbsp; + {{ $inclusion }}<br/>
											@endforeach
											</li>
										@endforeach
										</ul>
									@else
										<br/>No equipment.
									@endif
									</div>
								</li>
							@endforeach
							</ul>
						@else
							<br/>No artists.
						@endif

						</div>

						<!-- House equipments -->

						<div id="house-equipments" class="col s12 m12 l5">
							<div class="label">
								<img class="equipment-icon" src="{{ asset('images/icons/house-equipment.svg') }}">
								House equipment
							</div>

						@if(count($event['equipments']))
							<ul>
							@foreach($event['equipments'] as $equipment)
								<li>
									<div class="equipment">
										<span class="bullet-symbol">&bull;</span>
										{{ $equipment['name'] }}<br/>
									</div>
								@foreach($equipment['inclusion'] as $inclusion)
									&nbsp; + {{ $inclusion }}<br/>
								@endforeach
								</li>
							@endforeach
							</ul>
						@else
							<br/>No equipment.
						@endif
						</div>
					</div>
				</div>

			@if(Session::get('user_type') == 'venue' && Session::get('id') == $event['venue']['id'])

				<!-- Invited artists -->

				<div id="invited-artists" class="col s12 card-action">
					<div class="row">

				@if(count($service_requests))
					@foreach($service_requests as $service_request)
						<div class="col s12 m4 l3">
							<div class="ourscene-card-1 invited-artist-card">
								<div class="card main-container">
									<div class="card-content">
										<div class="profile-pic-container">
											<div class="circular-img-container profile-pic" style="background-image: url('{{ getProfilePicture($service_request['artist.id']) }}')"></div>
										</div>
									</div>
									<div class="card-action performance-time">
										<span class="left">{{ date('d F Y', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service_request['start_datetime'])->sec) }}</span>
										<span class="right">{{ date('h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service_request['start_datetime'])->sec) }}</span>
										<br/>
										<span class="left">{{ date('d F Y', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service_request['end_datetime'])->sec) }}</span>
										<span class="right">{{ date('h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($service_request['end_datetime'])->sec) }}</span>
									</div>
									<div class="card-action">
										<div class="label">Artist name</div>
										{{ $service_request['artist.name'] }}

										<div class="label">Genre</div>
										<?php
											$artist = User::artists()->find($service_request['artist.id']);
										?>
										<div class="text-container-ellipsis">{{ implode(', ', $artist['artist_genre']) }}</div>
									</div>
								</div>
								<div class="center-align action-container">

									<div class="action-single">
										<a href="{{ action('UserController@getPublicProfile', array('id' => $service_request['artist.id'] )) }}" class="btn ourscene-btn-1 view-profile-btn">
											View Profile
										</a>
									</div>

									<?php
										$cancel_on_click = "showCancelRequestForServiceModal('{$service_request['_id']}')";
									?>

									<div class="row action-series">
										<div class="col s12 m12 l12">
											<a class="btn ourscene-btn-3" onClick="{{ $cancel_on_click }}">CANCEL</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					@endforeach
				@else
					No requests for service here.
				@endif
					</div>
				</div>

				<!-- Artist applied -->

				<div id="artist-applied" class="col s12 card-action">
					<div class="row">

				@if(count($performance_requests))
					@foreach($performance_requests as $performance_request)
						<div class="col s12 m4 l3">
							<div class="ourscene-card-1 artist-applied-card">
								<div class="card main-container">
									<div class="card-content">
										<div class="profile-pic-container">
											<div class="circular-img-container profile-pic" style="background-image: url('{{ getProfilePicture($performance_request['artist.id']) }}')"></div>
										</div>
									</div>
									<div class="card-action performance-time">
										<span class="left">{{ date('d F Y', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($performance_request['start_datetime'])->sec) }}</span>
										<span class="right">{{ date('h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($performance_request['start_datetime'])->sec) }}</span>
										<br/>
										<span class="left">{{ date('d F Y', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($performance_request['end_datetime'])->sec) }}</span>
										<span class="right">{{ date('h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($performance_request['end_datetime'])->sec) }}</span>
									</div>
									<div class="card-action">
										<div class="label">Artist name</div>
										{{ $performance_request['artist.name'] }}

										<div class="label">Genre</div>
										<?php
											$artist = User::artists()->find($performance_request['artist.id']);
										?>
										<div class="text-container-ellipsis">{{ implode(', ', $artist['artist_genre']) }}</div>
									</div>
								</div>
								<div class="center-align action-container">

									<div class="action-single">
										<a href="{{ action('UserController@getPublicProfile', array('id' => $performance_request['artist.id'] )) }}" class="btn ourscene-btn-1 view-profile-btn">
											View Profile
										</a>
									</div>

									<?php
										$start_date = date('m/d/Y', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($performance_request['start_datetime'])->sec);
										$start_time = DatetimeUtils::formatTimeFromBackendToFrontend(DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($performance_request['start_datetime'])->sec);
										$end_date = date('m/d/Y', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($performance_request['end_datetime'])->sec);
										$end_time = DatetimeUtils::formatTimeFromBackendToFrontend(DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($performance_request['end_datetime'])->sec);

										$accept_on_click = "showConfirmRequestForPerformanceModal('{$performance_request['_id']}', '{$start_date}', '{$start_time}', '{$end_date}', '{$end_time}')";
										$next_time_on_click = "showRejectRequestForPerformanceModal('{$performance_request['_id']}')";
									?>

									<div class="row action-series">
										<div class="col s6 m6 l6">
											<a class="btn ourscene-btn-2" onClick="{{ $accept_on_click }}">
											@if(Session::get('user_type') == 'venue')
												ACCEPT
											@elseif(Session::get('user_type') == 'artist')
												BOOK
											@endif
											</a>
										</div>
										<div class="col s6 m6 l6">
											<a class="btn ourscene-btn-3" onClick="{{ $next_time_on_click }}">NEXT TIME</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					@endforeach
				@else
					No pending requests yet.
				@endif
					</div>
				</div>
			@endif

			</div>

		</div>

	</div>

</div>

<!-- Modals -->

@if(Session::get('user_type') == 'venue' && Session::get('id') == $event['venue']['id'])

	<!-- Confirm change status of service modal -->

	@include('modals.confirm-with-link-modal', [
		'modal_id' => 'confirm-change-status-of-service-modal',
		'modal_content' => '',
		'modal_confirm_link' => '',
	])

	<!-- Confirm request for performance modal -->

	@include('modals.confirm-request-for-performance-modal')

@endif

@if(Session::get('user_type') == 'artist' && Session::get('id') == $event['user_id'] && $event['status'] == 'pending')

@endif

@if(Session::get('user_type') == 'artist' && Session::get('id') == $event['user_id'] && $event['status'] == 'pending')

	<?php
		$service = Service::servicesBySenderId(Session::get('id'))->servicesByEventId($event['_id'])->pending()->first();
	?>

	<!-- Cancel request for performance modal -->

	@include('modals.confirm-with-link-modal', [
		'modal_id' => 'cancel-request-for-performance-modal',
		'modal_content' => 'Are you sure you want to <span class="bold-weight cancel-color">cancel</span> your request for performance?',
		'modal_confirm_link' => action('ServiceController@getCancelRequestForPerformance', array('id' => $service['_id'])),
	])

@endif

@if(Session::get('user_type') == 'artist')

	<!-- Request for performance modal -->

	@include('modals.request-for-performance-modal')

	<!-- Edit artist lineup equipment modal -->

	@include('modals.edit-artist-lineup-equipment-modal')

@endif

@endsection

@section('scripts')

<script src="{{ asset('js/view-event.js') }}"></script>

@endsection
