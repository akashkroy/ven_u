<?php
use OurScene\Models\User;
?>

<div class="settings-tab-container">
	<!-- Start of Form -->
	{!! Form::model(
		$user,
		array(
			'url'		=> action('UserController@postUpdateProfile'),
			'method'	=> 'POST',
			'files'		=> 'true',
		)
	)!!}

	<div class="row">
		<div class="col s12 m12 l12 input-field">
			<img id="profile-picture-preview" class="profile-picture-preview circle" src="{!! url($user->image) !!}" onerror="makeDefaultProfilePic(this)" alt="{!! $user->image !!}" />
			<input id="input-profile-picture" type="file"  class="form-control" name="input-profile-picture" onchange="loadProfilePicture(event)"/>
		</div>
	</div>

	<br/><br/>

	<div class="row input-field">
		<div class="col s12 m4 l4">
			{!! Form::label('name', 'Venue Name', ["class"=>"required-label"]) !!}
			{!! Form::text('name', null, ['required',"class"=>"required"]) !!}
		</div>
	</div>

	<div class="row input-field">
		<div class="col s12 m4 l4">
			{!! Form::label('email', 'Email address', ["class"=>"required-label"]) !!}
			{!! Form::email('email', null, ['required',"class"=>"required", "placeholder"=>"your@email.com"]) !!}
		</div>
	</div>

	<div class="row input-field">
		<div class="col s12 m8 l6 ">
			{!! Form::label('description', 'About', ["class"=>"required-label"]) !!}
			{!! Form::text('description', null, array('required', "class"=>"required", "cols"=>"50", "rows"=>"5", "style"=>"resize: none;", "placeholder"=>"About the venue")) !!}
		</div>
	</div>

	<div class="row input-field">
		<div class="col s12 m12 l12 input-field">
			<label for="" class="active">Venue Type</label>
			<br/>
		</div>

		@foreach($venue_types as $key => $value)
			<div class="col s6 m4 l4">
				 <input type="checkbox" id="{!! $key !!}" name="{!! $key !!}" class="filled-in" @if(User::where('_id',Session::get('id'))->first()['venue_type'] && in_array($key,User::where('_id',Session::get('id'))->first()['venue_type'])) checked/ @endif>
				 <label for="{!! $key !!}">{!!$value!!}</label>			
			</div>
		@endforeach
	</div>

	<?php
		//get other venue type
		//assumption: other venue type is the last value in the user venue type array
		$other_venue_type = "";

		$user_venue_type = User::where('_id', Session::get('id'))->first()['venue_type'];

		if($user_venue_type && count($user_venue_type)){
			
			$last_type = end($user_venue_type);
			if(!in_array($last_type, array_keys($venue_types)))
				$other_venue_type = $last_type;
		}

	?>

	<div class="row">
		<div class="col s12 m12 l12 input-field">
			<label for="" class="active">Other</label>
			<input type="text" class="registration-txtbx-1 col s4 m4 l4" name="other_venue_type" placeholder="Venue type" value="{{ $other_venue_type }}"/>
		</div>
	</div>

	<div class="row">
		
		<div class="col s12 m12 l12 input-field">
			<label for="" class="active">Address</label>
			<br/><br/>
		</div>

		<div class="col s12 m6 l4 input-field">
			<label for="unit_street">Unit/Building/Street <font style="color: #f00;">*</font></label>
			<input type="text" name="unit_street" @if(isset($user["address"]["unit_street"])) value="{{ $user["address"]["unit_street"] }}" @else value="{!! old('unit_street') !!}" @endif required/>
		</div>

		<div class="col s12 m6 l4 input-field">
			<label for="city">City <font style="color: #f00;">*</font></label>
			<input type="text" name="city" @if(isset($user["address"]["city"])) value="{{ $user["address"]["city"] }}" @else value="{!! old('city') !!}" @endif required/>
		</div>

		<div class="col s12 m6 l4 input-field">
			<label for="zipcode">Zip Code <font style="color: #f00;">*</font></label>
			<input type="text" name="zipcode" @if(isset($user["address"]["zipcode"])) value="{{ $user["address"]["zipcode"] }}" @else value="{!! old('zipcode') !!}" @endif required/>
		</div>

		<div class="col s12 m6 l4 input-field">
			<label for="state">State/Province <font style="color: #f00;">*</font></label>
			<input type="text" name="state" @if(isset($user["address"]["state"])) value="{{ $user["address"]["state"] }}" @else  value="{!! old('state') !!}" @endif required/>
		</div>

		<div class="col s12 m6 l4 input-field">
			<label for="country">Country <font style="color: #f00;">*</font></label>
			<input type="text" name="country" @if(isset($user["address"]["country"])) value="{{ $user["address"]["country"] }}" @else  value="{!! old('country') !!}" @endif required/>
		</div>
	</div>

	<div class="row input-field">
		<div class="col s12 m6 l6 ">
			{!! Form::label('phone_number', 'Contact Number', ["class"=>"required-label"]) !!}
			{!! Form::text('phone_number', null, array('required', "class"=>"required", "placeholder"=>"Phone number","pattern"=>"[0-9-+\s\(\)]*")) !!}
		</div>
	</div>

	<div class="row">
		<div class="col s12 m12 l12 input-field" style="margin-top: -15px;">
			<label>Operating hours <font style="color: #f00;">*</font></label>
		</div>

	</div>
	<div class="row">

		<div class="col s6 m3 l3 input-field">
			<input type="text" class="time-picki-picker" name="operating_hrs_open" id="operating_hrs_open" placeholder="" required value="{{$user['operating_hrs_open']}}">
			<label for="operating_hrs_open" style="top:-.8rem">Open </label>
		</div>

		<div class="col s6 m3 l3 input-field">
			<input type="text" class="time-picki-picker" name="operating_hrs_close" id="operating_hrs_close" placeholder="" required value="{{$user['operating_hrs_close']}}">
			<label for="operating_hrs_close" style="top:-.8rem">Close </label>
		</div>

		<div class="col s12 m3 l3 input-field">
			<label for="seating_capacity">Seating Capacity <font style="color: #f00;">*</font></label>
			<input type="number" name="seating_capacity" min="1" step="1" value="{{ $user->seating_capacity }}" />
		</div>
	</div>

	<div class="row input-field">
		<span class="col s12 m12 l12">Venue Serves</span>
		<div class="col s6 m3 l4">
			<input type="checkbox" id="serves_alcohol" name="serves_alcohol" class="filled-in" @if($user["serves_alcohol"]){!! 'checked' !!}@endif />
			<label for="serves_alcohol">Serves Alcohol</label>
		</div>
		<div class="col s6 m3 l3 ">
			<input type="checkbox" id="serves_food" name="serves_food" class="filled-in" @if($user["serves_food"]){!! 'checked' !!}@endif/>
			<label for="serves_food">Serves Food</label>
		</div>
	</div>

	<div class="form-section">Social Media Accounts</div>

	<div class="row">
		<div class="col s12 m6 l3 input-field">
			<label for="facebook_account">Facebook</label>
			<input type="text" id="facebook_account" name="facebook_account" placeholder="" value="{{ $user->social_media['fb'] }}"/>
		</div>
		<div class="col s12 m6 l3 input-field">
			<label for="twitter_account">Twitter</label>
			<input type="text" id="twitter_account" name="twitter_account" placeholder="" value="{{ $user->social_media['twitter'] }}"/>
		</div>
	</div>

	<div class="col s6 m4 l4">
		<input type="submit" class="btn btn-large ourscene-btn-1" value="SAVE" required/>
	</div>
	{!! Form::close() !!}
</div>
