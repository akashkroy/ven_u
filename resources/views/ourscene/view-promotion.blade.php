<?php
	use OurScene\Helpers\DatetimeUtils;
?>

@extends('ourscene.layouts.main')

@section('head')

@endsection

@section('content')

<div id="view-event">

	<div id="page-nav-buttons">
		<a class="btn page-nav-btn" href="{{ URL::previous() }}">
			<img src="{{ asset('images/icons/back-purple.svg') }}"/>&nbsp;&nbsp;Back
		</a>
	</div>

	<div id="view-event-container" class="card">

		<!-- Venue profile -->

		<div id="venue-profile">
			<div class="row">
				<div class="col s12 m8 l8">
					<div class="circular-img-container profile-pic" style="background-image: url('{{ getProfilePicture($promotion_creator['_id']) }}')"></div>
					<span class="name">{{ $promotion_creator['name'] }}</span>
				</div>
				<div class="col s12 m4 l4 right-align">
					<div class="left-align" style="display: inline-block;">

					@if(Session::get('id') == $promotion['user_id'])
						<a href="{{ action('PromotionController@getEditPromotion', array('id' => $promotion['_id'])) }}" class="btn ourscene-btn-4">Edit Promotion details</a><br/>
					@endif

					</div>
				</div>
			</div>
		</div>

		<!-- Cover photo -->

		<div id="cover-photo"></div>

		<!-- Event title -->

		<div id="event-title" class="card-action center-align">{{ $promotion['title'] }}</div>



		<div id="event-main-section" class="card-action">

			<div class="row">

				<!-- Promotion navigation -->

				<div id="event-navigation" class="col s4 offset-s4 m4 offset-m4 l2 offset-l5">
					<ul class="tabs">
						<li class="tab col s4"><a class="active" href="#event-details">Promotion details</a></li>
					</ul>
				</div>

				<!-- Promotion details -->

				<div id="event-details" class="col s12">
					<div id="contacts" class="card-action">
						<div class="row">
							<div class="col s3 m3 l3 center-align">
								<img src="{{ asset('images/icons/contact.svg') }}"/><br/>
								<div class="label">Contact No.</div>
								<div class="divider"></div>
								{{ $promotion_creator['phone_number'] }}
							</div>
							<div class="col s3 m3 l3 center-align">
								<img src="{{ asset('images/icons/email.svg') }}"/><br/>
								<div class="label">Email</div>
								<div class="divider"></div>
								{{ $promotion_creator['email'] }}
							</div>
							<div class="col s3 m3 l3 center-align">
								<img src="{{ asset('images/icons/website.svg') }}"/><br/>
								<div class="label">Facebook</div>
								<div class="divider"></div>
								{{ $promotion_creator['social_media']['fb'] }}
							</div>
							<div class="col s3 m3 l3 center-align">
								<img src="{{ asset('images/icons/website.svg') }}"/><br/>
								<div class="label">Twitter</div>
								<div class="divider"></div>
								{{ $promotion_creator['social_media']['twitter'] }}
							</div>

						</div>
					</div>

					<div id="details" class="card-action">

						<div class="detail">
							<div class="label">When</div>
							<br/>
							{{ date('F d, Y h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($promotion['start_datetime'])->sec) }}
							-
							{{ date('F d, Y h:i A', DatetimeUtils::convertMongoUTCDatetimeToMongoClientDatetime($promotion['end_datetime'])->sec) }}
						</div>
						<div class="detail">
							<div class="label">Type</div>
							<br/>
							{{ $promotion['promotion_type'] }}
						</div>
						<div class="detail">
							<div class="label">About</div>
							<br/>
							{{ $promotion['description'] }}
						</div>
						<div class="detail">
							<div class="label">Age requirements</div>
							<br/>
							{{ $promotion['age_requirements'] }}
						</div>
						<div class="detail">
							<div class="label">Address</div>
							<br/>
						@if(isset($promotion_creator['address']['unit_street']))
							<span class="bold-weight">Unit/Street</span><br/>{{ $promotion_creator['address']['unit_street'] }}<br/><br/>
						@endif
						@if(isset($promotion_creator['address']['city']))
							<span class="bold-weight">City</span><br/>{{ $promotion_creator['address']['city'] }}<br/><br/>
						@endif
						@if(isset($promotion_creator['address']['zipcode']))
							<span class="bold-weight">Zipcode</span><br/>{{ $promotion_creator['address']['zipcode'] }}<br/><br/>
						@endif
						@if(isset($promotion_creator['address']['state']))
							<span class="bold-weight">State</span><br/>{{ $promotion_creator['address']['state'] }}<br/><br/>
						@endif
						@if(isset($promotion_creator['address']['country']))
							<span class="bold-weight">Country</span><br/>{{ $promotion_creator['address']['country'] }}<br/><br/>
						@endif
						</div>
					</div>
				</div>

			</div>

		</div>

	</div>

</div>

@endsection

@section('scripts')


@endsection
