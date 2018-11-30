@extends('ourscene/layouts.main')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Promotions</div>
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

					@if(count($promotions) > 0)
					<div class="panel-group" id="accordion">
						@foreach($promotions as $promotion)
						<div class="panel panel-default">
							<div class="panel-heading">
								<h6 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion" href="#{!! $promotion->id !!}">{!! $promotion->title !!}</a>
								</h6>
							</div>
							<div id="{!! $promotion->id !!}" class="panel-collapse collapse">
								<div class="panel-body">
									@if(!empty($promotion->description)){!! $promotion->description !!}<br/>@endif
									<a href="{{ url('/promotion/'.$promotion->id) }}">Go to this promotion</a>
								</div>
							</div>
						</div>
						@endforeach
					</div>
					@else
					There are no promotions available.
					@endif

					<div class="form-group">
						<div class="col-md-12">
							<a class="btn btn-primary" href="{{ url('/add-promotion') }}">Add Promotion</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
