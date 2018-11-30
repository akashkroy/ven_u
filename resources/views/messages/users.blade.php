@extends('ourscene.layouts.main')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Users</div>
				<div class="panel-body">
					@foreach($users as $user)
						<a href="{{ url('/message/'.$user->id) }}">{!! $user->name !!}</a><br/>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
