<div class="settings-tab-container">
    
    <div class="section">
	    <div class="large-form-section">CHANGE PASSWORD</div>

	    <!-- Change Password Form -->

		{!! Form::open(array(
			'url'		=> action('UserController@postChangePassword'),
			'method'	=> 'POST',
			'id'		=> 'change-own-password-form'
		)) !!}

		<div id="error-new-password" class="error-field" style="display: none;">
			Check if retype password is correct.
		</div>

		<div id="check-current-password-error" class="error-field" style="display: none;">
			Please enter your correct old password.
		</div>

		<br/><br/>

		<div class="row">
			<div class="col s12 m6 l6 input-field">
				<label class="control-label">Old password <font style="color: #f00;">*</font></label>	
				<input type="password" name="current_password" id="current-password" class="form-control" placeholder="Type old password" required/>
			</div>
		</div>

		<div class="row">
			<div class="col s12 m6 l6 input-field">
				<label for="password">New password <font style="color: #f00;">*</font></label>
				<input type="password" id="password" class="registration-txtbx-1" name="password" value="{!! old('password') !!}" placeholder="8 min. characters" minlength="8" pattern=".{8,}" required/>
			</div>
		</div>

		<div class="row">
			<div class="col s12 m6 l6 input-field">
				<label for="register-password">Retype password <font style="color: #f00;">*</font></label>
				<input type="password" id="retype-password" class="" name="retype_password" value="{!! old('retype_password') !!}" placeholder="Retype Password" minlength="8" required/>
			</div>
		</div>

		<div class="row">
			<div class="col s12 m6 l6 input-field">
				<input type="submit" id="change-own-password-btn" class="btn ourscene-btn-1 pull-right" value="Change password"/>
			</div>
		</div>
		{!! Form::close() !!}
	</div>

	<div class="long-divider"></div>

	<div class="section">
		
		<div class="large-form-section">GOOGLE CALENDAR</div>

		<div class="row">
			<div class="col s12 m10 l8">
				<p>Once integrated, events that you created will be added to your Google calendar.</p>
			</div>
		</div>

		<div class="row">
			<div class="col s12 m6 l6">

			@if($user->gcalendar['allow'])
				<a href="{{ action('EventController@getDisableIntegrateGoogleCalendar') }}" class="btn ourscene-btn-1">Do not integrate with Google Calendar</a>
			@else
				<a href="{{ action('EventController@authenticateGoogleCalendar') }}" class="btn ourscene-btn-1">Integrate with Google Calendar</a>
			@endif
			
			</div>
		</div>
	</div>

	<div class="long-divider"></div>

	<div class="section">

		@if ($user->status === 'active') 
			<div class="large-form-section">DEACTIVATE ACCOUNT</div>
		@else
		  <div class="large-form-section">REACTIVATE ACCOUNT</div>
		@endif
		
		<div class="row">
			<div class="col s12 m10 l8">
				<p>Before you deactivate your account, you should know:</p>
				<p>Once deactivated, you will not be able to access any of the components or features of the OurScene application. Any existing events, promotions, messages, and other OurScene components will be inaccessible.</p>
 				<p>As of right now, deactivation is not permanent. We will be implementing a permanent deletion solution in the near future. Please stay tuned for upcoming releases.</p>
 				<p>We do not control content indexed by search engines.</p>
 				<p>You can reactivate your account at any time. To reactivate, simply log in to your account.</p>
 				<p>We’re going to miss you! You’re a part of a community that makes a real difference in the lives of artists and the parties that appreciate, promote, and enable them to express their art. Thank you for all you’ve done and we hope to see you back soon!</p>
			</div>
		</div>

		<div class="row">
			<div class="col s12 m6 l6">
				<!-- Modal Trigger -->
				@if ($user->status === 'active')
					<button data-target="deactivate-modal" class="btn ourscene-btn-1 modal-trigger">Deactivate</button>
				@else
					<button data-target="reactivate-modal" class="btn ourscene-btn-1 modal-trigger">Reactivate</button>
				@endif
			</div>
		</div>
	</div>

 </div>
