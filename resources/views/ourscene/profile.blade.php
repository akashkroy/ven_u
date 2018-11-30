@extends('ourscene/layouts.main')

@section('head')
	<!-- FullCalendar -->

	<script src="{{ asset('fullcalendar/fullcalendar.js') }}"></script>
	<link rel='stylesheet' href="{{ asset('fullcalendar/fullcalendar.css') }}"></link>

	<style>
		#timeline{
			width: 400px;
			height: 20px;
			background: #4200f7;
			margin-top: 20px;
			float: left;
			border-radius: 15px;
		}

		#playhead{
			width: 18px;
			height: 18px;
			border-radius: 50%;
			margin-top: 1px;
			background: rgba(0, 255, 196, 0.82);
		}
</style>

@endsection

@section('content')

<div id="view-profile" class="card">

	<div class="profile-header card-title">
	@if(Session::get('id') == $user->_id)
		<div class="nav right-align">
			<a href="{{ action('UserController@getProfileSettings') }}" class="btn ourscene-btn-1 nav-btn">Edit Profile</a>
		</div>
	@endif
		<div class="info valign-wrapper">
			<div style="display:block; background-image: url('{{getProfilePicture($user->id)}}'); width:90px; height: 90px; background-size: cover; background-position: 50%;" class="circular-img-container valign">

			</div>
			<span class="valign">{{ $user->name }}</span>
		</div>
	</div>

	<div id="profile-container">
		<div class="row">
			<div class="col s12 m12 l6 offset-l3 ourscene-tab-navigation" id="profile-navigation">
			  <!-- Navigation tabs -->
			  <ul class="tabs" role="tablist">
			  @if($user->user_type === 'artist')
			    <li role="presentation" class="tab col s3 active-reg-step-tab">
			    	<a href="#profile-details"><span>Profile</span></a>
			    </li>
			    <li role="presentation" class="tab col s3 active-reg-step-tab">
			    	<a href="#media"><span>Media</span></a>
			    </li>
			    <li role="presentation" class="tab col s3 inactive-reg-step-tab">
			    	<a href="#events"><span>Event Calendar</span></a>
			    </li>
			  @else
			  	<li role="presentation" class="tab col s3 active-reg-step-tab">
			  		<a href="#profile-details"><span>Venue Details</span></a>
			  	</li>
			    <li role="presentation" class="tab col s3 active-reg-step-tab">
			    	<a href="#events"><span>Events</span></a>
			    </li>
			    <li role="presentation" class="tab col s3 inactive-reg-step-tab">
			    	<a href="#profile-equipment"><span>Equipment</span></a>
			    </li>
			  @endif
			  </ul>
		  	</div>

			<!-- Tab for profile details -->
			<div id="profile-details" class="col s12 m12 l12 profile-tab">

				<div id="contacts" class="card-action">
					@if($user->user_type === 'venue')
					<div class="row">
						<div class="col s3 m3 l3 center-align contact-panel">
							<img src="{{ asset('images/icons/contact.svg') }}"/><br/>
							<div class="label">Contact No.</div>
							<div class="divider"></div>
							{{ $user['phone_number'] }}
						</div>
						<div class="col s3 m3 l3 center-align contact-panel">
							<img src="{{ asset('images/icons/email.svg') }}"/><br/>
							<div class="label">Email</div>
							<div class="divider"></div>
							{{ $user['email'] }}
						</div>
						<div class="col s3 m3 l3 center-align contact-panel">
							<img src="{{ asset('images/icons/website.svg') }}"/><br/>
							<div class="label">Facebook</div>
							<div class="divider"></div>
							<a href="{{ getHyperLink($user['social_media']['fb']) }}" class="ourscene-link-1" target="_blank" >{{ $user['social_media']['fb'] }}</a>
						</div>
						<div class="col s3 m3 l3 center-align contact-panel">
							<img src="{{ asset('images/icons/website.svg') }}"/><br/>
							<div class="label">Twitter</div>
							<div class="divider"></div>
							<a href="{{ getHyperLink($user['social_media']['twitter']) }}" class="ourscene-link-1" target="_blank">{{ $user['social_media']['twitter'] }}</a>
						</div>
					</div>
					@else
					<div class="row">
						<div class="col s6 m4 l4 offset-m2 offset-l2 center-align contact-panel">
							<img src="{{ asset('images/icons/contact.svg') }}"/><br/>
							<div class="label">Contact No.</div>
							<div class="divider"></div>
							{{ $user['phone_number'] }}
						</div>
						<div class="col s6 m4 l4 center-align center-align contact-panel">
							<img src="{{ asset('images/icons/email.svg') }}"/><br/>
							<div class="label">Email</div>
							<div class="divider"></div>
							{{ $user['email'] }}
						</div>
					</div>
					<div class="row">
						<div class="col s3 m3 l3 center-align contact-panel">
							<img src="{{ asset('images/icons/website.svg') }}"/><br/>
							<div class="label">Facebook</div>
							<div class="divider"></div>
							<a href="{{ getHyperLink($user['social_media']['fb']) }}" class="ourscene-link-1" target="_blank" >{{ $user['social_media']['fb'] }}</a>
						</div>
						<div class="col s3 m3 l3 center-align contact-panel">
							<img src="{{ asset('images/icons/website.svg') }}"/><br/>
							<div class="label">Twitter</div>
							<div class="divider"></div>
							<a href="{{ getHyperLink($user['social_media']['twitter']) }}" class="ourscene-link-1" target="_blank">{{ $user['social_media']['twitter'] }}</a>
						</div>
						<div class="col s3 m3 l3 center-align contact-panel">
							<img src="{{ asset('images/icons/website.svg') }}"/><br/>
							<div class="label">Soundcloud</div>
							<div class="divider"></div>
							<a href="{{ getHyperLink($user['social_media']['soundcloud']) }}" class="ourscene-link-1" target="_blank" >{{ $user['social_media']['soundcloud'] }}</a>
						</div>
						<div class="col s3 m3 l3 center-align contact-panel">
							<img src="{{ asset('images/icons/website.svg') }}"/><br/>
							<div class="label">Bandcamp</div>
							<div class="divider"></div>
							<a href="{{ getHyperLink($user['social_media']['bandcamp']) }}" class="ourscene-link-1" target="_blank">{{ $user['social_media']['bandcamp'] }}</a>
						</div>
					</div>

					@endif
				</div>

				<div id="details" class="card-action">
					<div class="row">
						<div class="label col s12 m12 l12"> About </div>
						<p class="col s12 m10 l8">{{$user->description}}</p>
					</div>

					@if($user->user_type === 'venue')
					<div class="row">
						<div class="label col s12 m12 l12">Venue Type</div>
						<div class="col s12 m12 l12">
							<ul>
							@foreach ($user->venue_type as $type)
								@if(array_key_exists($type,$venue_types))
								<li> {{$venue_types[$type]}} </li>
								@else
								<li> {{$type}}
								@endif
							@endforeach
							</ul>
						</div>
					</div>

					<div class="row">
						<div class="label col s12 m12 l12">Venue Serves</div>
						<div class="col s12 m12 l12">
							<ul>
							@if ($user->serves_alcohol == true)
								<li>&#9679; &nbsp; Serves Alcohol </li>
							@endif
							@if ($user->serves_food == true)
								<li>&#9679; &nbsp; Serves Food </li>
							@endif
							</ul>
						</div>
					</div>

					<div class="row">
						<div class="label col s12 m12 l12">Address</div>
						<p class="col s12 m12 l12">
							{{$user->address['unit_street']}}, {{$user->address['city']}}, {{$user->address['state']}}, {{$user->address['country']}}, {{$user->address['zipcode']}}
						</p>
					</div>

					<div class="row">
						<div class="label col s12 m12 l12">Operating Hours</div>
						<p class="col s12 m12 l12"> {{$user->operating_hrs_open}} - {{$user->operating_hrs_close}} </p>
					</div>

					<div class="row">
						<div class="label col s12 m12 l12">Seating Capacity</div>
						<p class="col s12 m12 l12">{{ $user->seating_capacity }}</p>
					</div>

					@else
					<div class="row">
						<div class="label col s12 m12 l12">City</div>
						<p class="col s12 m12 l12">{{ $user->address['city'] }}</p>
					</div>
					<div class="row">
						<div class="label col s12 m12 l12">Genre</div>
						<div class="col s12 m12 l12">
							<ul>
							@foreach ($user->artist_genre as $key => $genre)
								<li> {{$genre}} </li>
							@endforeach
							</ul>
						</div>
					</div>
					<div class="row">
						<div class="label col s12 m12 l12">Equipment</div>
						<div class="col s12 m12 l12">
							<ul>
							@foreach ($equipments as $equip)
							 	<li> -  {{$equip->name}} </li>
							@endforeach
							</ul>
						</div>
					</div>
					@endif
				</div>
			</div>

			<!-- Tab for events calendar -->

			<div id="events" class="col s12 m12 l12 profile-tab">
				
				<div id="calendar-container">
		
					<!-- Calendar title -->

					<div id="calendar-title-container">
						<img src="{{ asset('images/icons/calendar-month-purple.svg') }}"/>
						<span id="calendar-title"></span>
					</div>

					<!-- Calendar -->

					<div id="calendar" class="ourscene-calendar"></div>
							
				</div>

			</div>

			@if($user->user_type === 'venue')
			<!-- Tab for Equipment (Venue Profile Only) -->
			<div id="profile-equipment" class="col s12 m12 l12 profile-tab" role="tabpanel">
				<div class="row card-action">
					<div class="label col s12 m12 l12" id="equipment-title">
						<img src="{{ asset('images/icons/house-equipment.svg') }}"/>
						House Equipment
					</div>
					
					<br/><br/><br/>

				@if(count($equipments))
					<ul style="font-family: 'OpenSans-SemiBold';">
						@foreach ($equipments as $equip)
						 	<li>
								<span style="font-size:20px; line-height: 1px; height: 10px; position:relative; top: 4px; margin-right: 5px; color:#534d93;">•</span>{{$equip->name}}
								@if(count($equip->inclusion) > 0)
									<ul style="margin-left: 3%;">
									@foreach ($equip->inclusion as $incl)
										<li><span style="font-size:15px; line-height: 1px; height: 10px; position:relative; top: 2px; margin-right: 5px; color:#534d93;">•</span>{{$incl}}</li>
									@endforeach
									</ul>
								@endif
							</li>
						@endforeach
					</ul>
				@else
					No equipment.
				@endif

				</div>
			</div>
			@endif

			@if($user->user_type === 'artist')
			<!-- Tab for Media (Artist Profile Only) -->
			<div id="media" class="col s12 m12 l12 profile-tab" role="tabpanel">
				<div class="row card-action" id="songs-container">

					<div class="s12 m8 l6 offset-m2 offset-l3 label" style="margin-bottom:10px;">Songs</div>

				@if(count($songs))
					<?php $i =0; ?>
					@foreach ($songs as $song)

						<div class="col s12 m10 l8 offset-m1 offset-l2 song-file-container">
							<div class="row">
								<div class="song-icon-container col s1 m1 l1">
									<img src="{{asset('images/icons/audio.svg')}}" class="song-icon">
								</div>
								<div class="song-title-container col s6 m6 l6">
									<span class="song-title truncate"><b>{{$song->title}}</b></span>
								</div>
								<div class="time-display-container col s2 m2 l2">
									<span class="right time-display" id="time{{$i}}">00:00</span>
								</div>
								<div class="control-btn-container col s2 m2 l2">
									<button class="playBtn controlBtn right" id="play{{$i}}"></button>
								</div>
								<audio class="music" id="music{{$i}}" preload='none'>
								  <source src="{{$song->url}}" type="audio/mp3">
								</audio>
							</div>
						</div>
						<?php $i++; ?>
					@endforeach
				@else
					<div class="col s6 m2 l2">
						No songs.
					</div>
				@endif
					
				</div>

				<div class="row card-action">
					<div class="col s12 m12 l12">
						<div id="images-container row">
							<div class="label col s12 m12 l12" style="margin-bottom:10px;"> Photos </div>
						@if(count($images))
							@foreach ($images as $image)
							<div class="col s6 m2 l2">
						    	<a class="media_images" href="{{$image->url}}" style="width:auto">
						    		<div alt="" style="background-image: url('{{$image->url}}'); background-size:cover; background-position: 50%; width: 100%;height: 100%; margin:0 auto; align: baseline; top:0; left:0"/></div>
								</a>
							</div>
						  	@endforeach
						@else
							<div class="col s6 m2 l2">
								No photos.
							</div>
						@endif
						</div>
					</div>

					<br/><br/>
					<div class="col s12 m12 l12">
						<div id="videos-container row">
							<div class="label col s12 m12 l12" style="margin-bottom:10px;"> Videos </div>
							@if(count($videos))
								@foreach ($videos as $video)
									<div class="col s12 m4 l4">
										<video class="responsive-video" src="{{$video->url}}" controls>
										   <!-- <source src="{{$video->url}}" type="video/mp4"> -->
										</video>
									</div>
								@endforeach
							@else
								<div class="col s6 m2 l2">
								No videos.
								</div>
							@endif
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			@endif

		</div>
	</div>
</div>
@endsection

@section('scripts')

<script>
	function makeDefaultProfilePic(image){
		image.onerror = "";
	    image.src = "{{asset('images/icons/artist.svg')}}";
	    return true;
	}
</script>

<script type="text/javascript" src="{{ asset('js/media.js') }}"></script>
<script>
	var USER_ID = '{{ $user_id }}';
</script>
<script type="text/javascript" src="{{ asset('js/profile-events-calendar.js') }}"></script>
<script type="text/javascript">
	$('.media_images').fancybox()

	$(document).one('click','a[href=#events]',function(){
		//force click of calendar month button because full calendar does not render automatically on hidden tabs
		$('#calendar > div.fc-toolbar > div.fc-left > button.fc-month-button.fc-button.fc-state-default.fc-corner-left.fc-corner-right').trigger('click')
	})
</script>

@endsection