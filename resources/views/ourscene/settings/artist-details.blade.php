<div class="settings-tab-container" id="">
	<!-- <div class="row" id="venue-profile"> -->
	<!-- Start of Form -->
	{!! Form::model(
		$user,
		array(
			'url'		=> action('UserController@postUpdateProfile'),
			'method'	=> 'POST',
			'files'		=> 'true',
			'id' => 'save-profile-form'
		)
	)!!}
	<div class="row">
		<div class="col s12 m12 l12 input-field">
			<!-- <div id="profile-picture-preview" style="background-color:#333;"></div> -->
			<div id="profile-picture-preview" style="float:left;display:block; background-image: url('{{getProfilePicture($user->id)}}'); width:120px; height: 120px; background-size: cover; background-position: 50%;" class="profile-picture-preview circle"></div>
			<!-- <img id="profile-picture-preview" class="profile-picture-preview circle" src="{!! url($user->image) !!}" onerror="makeDefaultProfilePic(this)" style="float:left;" alt="{!! $user->image !!}" /> -->
			<div class="file-field input-field" style="display:inline-block;margin-top:4.1em;margin-left:.6em;">
		    <div class="btn ourscene-btn-1">
		      <span>Change</span>
		      <input id="input-profile-picture" type="file"  class="form-control" name="input-profile-picture" onchange="loadProfilePicture(event)"/>
		    </div>
		    <div class="file-path-wrapper">
		      <input style="display:none;" class="file-path validate" type="text">
		    </div>
		  </div>
		</div>
	</div>


	<br/><br/>

	<div class="row input-field">
		<div class="col s12 m4 l4">
			{!! Form::label('name', 'Artist name', ["class"=>"required-label"]) !!}
			{!! Form::text('name', null, ['required',"class"=>"required"]) !!}
		</div>
	</div>

	<div class="row input-field">
		<div class="col s12 m4 l4">
			{!! Form::label('email', 'Email address', ["class"=>"required-label"]) !!}
			{!! Form::email('email', null, ['required',"class"=>"required", "placeholder"=>"your@email.com"]) !!}
		</div>
	</div>

	<br/>

	<div class="row input-field">
		<div class="col s12 m0 l0">
			<label for="" class="active required-label">Ages</label>
		</div>
		<div class="col s6 m2 l2">
			<input type="radio" id="age-none" name="ages" class="with-gap" value="none" @if($user->ages == 'none') checked @endif/>
			<label for="age-none">None</label>
		</div>
		<div class="col s6 m2 l2">
			<input type="radio" id="age-18" name="ages" class="with-gap" value="18+" @if($user->ages == '18+') checked @endif/>
			<label for="age-18">18+</label>
		</div>
		<div class="col s6 m2 l2">
			<input type="radio" id="age-21" name="ages" class="with-gap" value="21+" @if($user->ages == '21+') checked @endif/>
			<label for="age-21">21+</label>
		</div>
	</div>

	<br/>

	<div class="row input-field">
		<div class="col s12 m4 l4 input-field">
			{!! Form::label('city', 'City', ["class"=>"required-label"]) !!}
			<input type="text" name="city" value="{{ $user->address['city'] }}" placeholder="" required/>
		</div>
		<div class="col s12 m4 l4 input-field">
			<label for="zip-code">Zip Code <font style="color: #f00;">*</font></label>
			<input type="text" name="zipcode" value="{{ $user->address['zipcode'] }}" placeholder="5110309" required/>
		</div>
	</div>

	<div class="row input-field">
		<div class="col s12 m4 l4 ">
			{!! Form::label('phone_number', 'Contact Number') !!}
			{!! Form::text('phone_number', null, ["placeholder"=>"Contact Number", "pattern"=>"[0-9-+\s\(\)]*"]) !!}
		</div>
	</div>

	<div class="row input-field">
		<div class="col s12 m4 l4 ">
			{!! Form::label('description', 'About', ["class"=>"required-label"]) !!}
			{!! Form::text('description', null, array('required', "class"=>"required", "cols"=>"50", "rows"=>"5", "style"=>"resize: none;", "placeholder"=>"About the artist")) !!}
		</div>
	</div>

	<div class="row input-field">
		<div class=" col s12 m4 l4">
			<a href="#artist-genre-modal" class="btn ourscene-btn-2 modal-trigger">ADD GENRE</a>
		</div>
	</div>

	<div class="form-section">Social Media Accounts</div>

	<div class="row">
		<div class="col s12 m4 l4 input-field">
			<label for="facebook_account">Facebook</label>
			<input type="text" id="facebook_account" name="facebook_account" class="registration-txtbx-1" placeholder="" value="{{ $user->social_media['fb'] }}"/>
		</div>
		<div class="col s12 m4 l4 input-field">
			<label for="twitter_account">Twitter</label>
			<input type="text" id="twitter_account" name="twitter_account" class="registration-txtbx-1" placeholder="" value="{!! $user->social_media['twitter'] !!}"/>
		</div>
	</div>

	<div class="row">
		<div class="col s12 m4 l4 input-field">
			<label for="soundcloud_account">Soundcloud</label>
			<input type="text" id="soundcloud_account" name="soundcloud_account" class="registration-txtbx-1" placeholder="" @if(isset($user->social_media['bandcamp'])) value="{!!$user->social_media['soundcloud']  !!}" @endif/>
		</div>
		<div class="col s12 m4 l4 input-field">
			<label for="bandcamp_account">Bandcamp</label>
			<input type="text" id="bandcamp_account" name="bandcamp_account" class="registration-txtbx-1" placeholder="" @if(isset($user->social_media['bandcamp'])) value="{!!$user->social_media['bandcamp']  !!}" @endif/>
		</div>
	</div>
	<div class="col s6 m4 l4">
		<input type="submit" class="btn btn-large ourscene-btn-1" value="SAVE" required/>
	</div>
	{!! Form::close() !!}
</div>
