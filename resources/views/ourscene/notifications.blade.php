@extends('ourscene/layouts.main')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Notifications
					@if(count($not_read) > 0)<span class="badge">{!! count($not_read) !!}</span>@endif

					@if(count($notifications) > 0)
					<div style="float: right; margin-top: -7px;">
						{!! Form::open(array(
							'url'		=> action('NotificationController@markAllAsRead'),
							'method'	=> 'POST',
						)) !!}

						<input type="submit" id="mark-all-as-read" class="btn btn-primary" value="Mark All As Read"/>

						{!! Form::close() !!}
					</div>
					@endif
				</div>
				<div class="panel-body">
					<!-- Flash message -->

					@if(Session::has('success'))
						<div class="alert alert-success" role="alert">
    						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    						{{ Session::get('success') }}
    					</div>
					@endif

					@if(Session::has('error'))
						<div class="alert alert-danger" role="alert">
    						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							{{ Session::get('error') }}
						</div>
					@endif

					@if(count($notifications) > 0)
						@foreach($notifications as $notification)
							<div class="panel panel-default">
								<div class="panel-body">
									@if($notification->type === 'promotion')
										<a href="{{ url('/promotion/'.$notification->event_id) }}">{!! $notification->body !!}</a>
									@else
										<a href="{{ url('/') }}">{!! $notification->body !!}</a>
									@endif<br/>
								</div>
								<div class="panel-footer" style="font-size: 11px;">
									@if(($notification->status === 'pending')||($notification->status === 'confirmed')||($notification->status === 'canceled'))
										{!! 'Posted on '.date('F j, Y', $notification->sent_at->sec).' at '.date('g:i A', $notification->sent_at->sec) !!}
									@elseif($notification->status === 'approved')
										{!! 'Approved on '.date('F j, Y', $notification->sent_at->sec).' at '.date('g:i A', $notification->sent_at->sec) !!}
									@elseif($notification->status === 'declined')
										{!! 'Declined on '.date('F j, Y', $notification->sent_at->sec).' at '.date('g:i A', $notification->sent_at->sec) !!}
									@endif
									@if(!$notification->is_read)
										-&nbsp;<font style="text-align: right; color: #f00;">Unread</font>
									@endif
								</div>
							</div>
						@endforeach
					@else
						There are no notifications.
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
