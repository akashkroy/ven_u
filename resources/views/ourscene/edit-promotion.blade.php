@extends('ourscene/layouts.main')

@section('head')
	
<!-- Autocomplete -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

@endsection

@section('content')

<div id="event-form" class="card">

	<div class="card-action title">
		<img src="{{ asset('images/icons/create.svg') }}"/>
		Edit Promotion <span class="edit-event-title">{{ $promotion['title'] }}</span>
	
		<a class="btn ourscene-btn-4 right delete-event-btn modal-trigger" href="#confirm-delete-promotion-modal">Delete</a>
	</div>

	<div id="event-form-container">
		<div id="promotion-form-container">
			@include('ourscene.promotion-form')
		</div>
	</div>
</div>

<!-- Modals -->

<!-- Confirm delete promotion modal -->

@include('modals.confirm-with-link-modal', [
	'modal_id' => 'confirm-delete-promotion-modal',
	'modal_content' => 'Are you sure you want to delete this promotion?',
	'modal_confirm_link' => action('PromotionController@getDeletePromotion', array('id' => $promotion['_id'])),
])

@endsection

@section('scripts')

<script>
$(document).ready(function(){

	//uncheck all promotion types
	$("input[name=type]").prop('checked', false);

	//check the edit promotion type
	$("input[name=type][value='{{ $promotion['promotion_type'] }}']").prop('checked', true);

	//check if there is not a checked promotion type
	if(! $("input[name='type']:checked").length){
		//initialize and enable the other promotion type
		$("input[name=type][value='other']").prop('checked', true);
		
		$promotion_other_type = $("#promotion-other-type");

		$promotion_other_type.val("{{ $promotion['promotion_type'] }}");
		$promotion_other_type.prop('disabled', false);
	}
	
	//check the edit promotion age requirements
	$("input[name=age_requirements][value='{{ $promotion['age_requirements'] }}']").prop('checked', true);
});
</script>

<!-- Event form JS -->

<script>
	var AJAX_AUTOCOMPLETE_ARTISTS = "{{ action('UserController@getAutocompleteArtists') }}";
	var AJAX_AUTOCOMPLETE_VENUES = "{{ action('UserController@getAutocompleteVenues') }}";
	var remove_icon_src = "{{ asset('images/icons/delete.svg') }}";
</script>
<script src="{{ asset('js/event-form.js') }}"></script>

@endsection