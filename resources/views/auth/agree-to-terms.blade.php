@include('modals/terms-of-service-modal')

<div class="row" style="margin: 0;">
	<div class=" col s12 m12 l12">
		<input type="checkbox" id="terms-of-service-checbox" name="terms-of-service-checbox" class="filled-in"/>
		<label for="terms-of-service-checbox" class="i-agree-label">
			<a class="ourcene-color-1">I agree to the Terms of Service</a>
		</label>
	</div>
	<p class="terms-and-condition-label col s12 m12 l12">
		By joining, you agree to the 
		<a href="{{ action('HomeController@getTermsOfService') }}" target="_blank">Terms of Service</a>
		and <a href="{{ action('HomeController@getPrivacyPolicy') }}" target="_blank">Privacy Policy</a>,
		including <a href="{{ action('HomeController@getCopyrightPolicy') }}" target="_blank">Copyright Dispute Policy</a>. 
		Others will be able to find you by email or phone number when provided.
	</p>
	<div class="col s12 m4 l6">
		<br/>

		<div class="row">
			<input type="submit" id="register-btn" class="btn ourscene-btn-1 reg-btn col s12 m12 l12" value="JOIN" required/>
		</div>
	</div>
	<p class="col s12 m10 l10">
		Already have an account? <a class="sign-in-link" href="{{action('HomeController@getIndex')}}">Sign in</a>
	</p>
</div>