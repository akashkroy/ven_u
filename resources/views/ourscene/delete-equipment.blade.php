@extends('ourscene/layouts.main')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Delete Equipment</div>
				<div class="panel-body">
					Are you sure that you want to delete this equipment?

					{!! Form::open(array(
						'url'		=> action('EquipmentController@postDeleteEquipment'),
						'method'	=> 'POST',
					)) !!}

					<div class="form-group">
						<div class="col-md-6 col-md-offset-4">
							<button type="submit" name="delete_equipment" class="btn btn-primary" value="yes">Yes</button>
							<a class="btn btn-primary" href="{!! URL::previous() !!}">No</a>
						</div>
					</div>

					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection