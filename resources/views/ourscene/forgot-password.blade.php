@extends('ourscene/layouts.main')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Calendar</div>

				<div class="panel-body">
					@if(Session::has('success'))
				<!-- Flash message -->
				<div class="alert alert-success" role="alert">{{ Session::get('success') }}</div>
				
				<div class="form-group text-center">
					<a href="{{ url('auth/login') }}">Log in</a>
				</div>
			@else	

				@if(Session::has('error'))
					<div class="alert alert-danger" role="alert">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						{{ Session::get('error') }}
					</div>
				@endif

				{!! Form::open(array(
					'id'			=> 'forgot-password-form',
					'url'			=> action('UserController@postForgotPassword'),
					'method'		=> 'POST',
				)) !!}

					<div class="form-group">
						<span class="note">A temporary password will be sent to this email address.</span>
					</div>
					<div class="form-group">
						<input type="email" class="form-control" name="email" placeholder="Email address" autofocus required/>
					</div>
					
					<div class="form-group text-center">
						<input type="submit" id="submit-btn" class="btn pedal-btn-primary" value="Submit" required/>
					</div>

				{!! Form::close() !!}

					<a href="{{ url('cms/login') }}" class="pull-left">< Login</a>
			@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
