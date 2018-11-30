@extends('ourscene.layouts.entrypoint')

@section('content')
<?php
	if((isset($_GET['type']) && $_GET['type']=='venue') || (Session::has('attempt_login_as') && Session::get('attempt_login_as') == 'venue')){
		$sign_in_as_btn = 'ARTIST';
		$user_type = 'venue';
		$form_url = action('UserController@postLoginVenue');
		$user_type_icon = asset('images/icons/venue.svg');
		$sign_up_url = action('UserController@getRegisterAsVenue');
	}
	else{
		$sign_in_as_btn = 'VENUE';
		$user_type = 'artist';
		$form_url = action('UserController@postLoginArtist');
		$user_type_icon = asset('images/icons/artist.svg');
		$sign_up_url = action('UserController@getRegisterAsArtist');
	}
?>

<script>
	login_type = "{{ $user_type }}";
</script>

<div class="main body-{{ $user_type }}" id="main">
	<div id="change-type-div" class="col s12 m12 l12 ">
		<div class="hide-on-small-only">
			<button type="button" class="change-type-btn l-change-type-btn" onclick="changeSignInType(this)">SIGN IN AS {{ $sign_in_as_btn }}</button>
		</div>
		<div class="hide-on-med-and-up">
			<img id="m-ourscene-logo" src="{{ asset('images/icons/logo.svg') }}"/>
			<span>OurScene</span>
			<button type="button" onclick="changeSignInType(this)" class="change-type-btn btn ourscene-btn-2">SIGN IN AS {{ $sign_in_as_btn }}</button>
		</div>
		<div class="hide-on-small-only">
			<a href="{{url('/about-us')}}" class="btn-flat static-link">ABOUT US </a>
			<span style="color:white"> | </span>
			<a href="{{url('/faq')}}" class="btn-flat static-link">FAQs </a>
		</div>
	</div>
	<div id="login-form-container">
		<div id="login-heading">
			<img id="user-type-icon" src="{{ $user_type_icon }}" />
			<br/>
			<span id="user-type-label"><b>{{ $user_type }}</b></span>
		</div>
		<div id="login-body">
			
			@if(Session::has('success'))
				<br/>
				<div class="success-field">
					{{ Session::get('success') }}
				</div>
			@endif
			@if(Session::has('error'))
				<br/>
				<div class="error-field">
					{{ Session::get('error') }}
				</div>
			@endif
			<br/>

			{!! Form::open(array(
				'id'			=> 'login-form',
				'url'			=> $form_url,
				'method'		=> 'POST'
			)) !!}
				<input type="hidden" id="timezone_offset" name="timezone_offset" value=""/>
				<div class="row form-group">
					<div class="col s12 m12 l12 login-textbox">
						<input type="email" class="form-control" name="email" @if (Session::has('attempt_username')) value="{{ Session::get('attempt_username') }}" @endif placeholder="Email address" @if(!Session::has('error')) autofocus @endif required/>
					</div>
				</div>

				<div class="row form-group">
					<div class="col s12 m12 l12 login-textbox"> 
						<input type="password" class="form-control" name="password" placeholder="Password" required/>
					</div>
				</div>

				<div class="row form-group login-btn-div">
					<div class="col s12 m12 l12">
						<input type="checkbox" class="filled-in" name="remember_me" id="remember-me"/>
						<label for="remember-me">Remember Me</label>
					</div>		
				</div>

				<div class="form-group row login-btn-div">
					<input type="submit" class="btn ourscene-btn-1 login-btn" value="SIGN IN">
				</div>
			</form>

			{!! Form::close() !!}

			<div class="col-l-12 forgot-password">
				<a class="modal-trigger" href="#reset-password-modal">Can't access your account?</a>
				<!-- <a href="{{ url('/password/email') }}">Can't access your account?</a> -->
			</div>
			<div class="col-l-12 signup-link-div">
				<span>Don't have an account?</span>
				<a id="signup-link" href="{{ $sign_up_url }}">Sign up</a>
			</div>
		</div>
	</div>	
</div>
</div>
<footer class="hide-on-med-and-up page-footer"><!-- style="padding-left:20px;padding-top:10px;margin-bottom:40px;margin-top:-30px;border-top:solid gray .5px" class="signup-link-div"> -->
	<div class="container">
		<a href="{{url('/about-us')}}" style="color:white">ABOUT US </a>
		<br/>
		<a href="{{url('/faq')}}" style="color:white">FAQs </a>
	</div>
</footer>
@include('modals.reset-password-modal')
<script>
	$(document).ready(function() {
		var timezone_offset = new Date().getTimezoneOffset();
		$("#timezone_offset").val(timezone_offset);
	});
	if(getUrlVars()["reset-password"]){
		$("#reset-password-modal").openModal();
	}
	function getUrlVars() {
		var vars = {};
		var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		vars[key] = value;
		});
		return vars;
	}
</script>
@endsection