<div id="{{ $modal_id }}" class="modal confirm-with-link-modal">	
	<div class="modal-content">
		{!! $modal_content !!}
		<div class="optional-content"></div>
	</div>

	<div class="modal-footer">
		<a href="{{ $modal_confirm_link }}" class="yes-link modal-action modal-close btn ourscene-btn-plain-1">Yes</a>
		<a class="no-link modal-action modal-close btn ourscene-btn-plain-1">No</a>
	</div>

</div>