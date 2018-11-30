<!-- Modal Structure -->
@if ($user->status === 'active')
<div id="deactivate-modal" class="modal">
	<div class="modal-content">
		<div class="ourscene-modal-title-1">Deactivate account</div>
		<p>Are you sure you want to deactivate your account?</p>
	</div>
	<div class="modal-footer">
		<a href="#!" class=" modal-action modal-close btn ourscene-btn-plain-1">Cancel</a>
		<a href="{{ action('UserController@getDeactivateAccount') }}" id="deactivate-button" class="modal-action btn ourscene-btn-plain-1">Deactivate</a>
	</div>
</div>
@else
<div id="reactivate-modal" class="modal">
	<div class="modal-content">
		<div class="ourscene-modal-title-1">Reactivate account</div>
		<p>Are you sure you want to reactivate your account?</p>
	</div>
	<div class="modal-footer">
		<a href="#!" class="modal-action modal-close btn ourscene-btn-plain-1">Cancel</a>
		<a href="{{ action('UserController@getReactivateAccount') }}" id="reactivate-button" class="modal-action btn ourscene-btn-plain-1">Reactivate</a>
	</div>
</div>
@endif