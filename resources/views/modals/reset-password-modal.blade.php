<div id="reset-password-modal" class="modal">
	<div class="modal-content">

		<div class="ourscene-modal-title-1">Reset password</div>
		
		<div class="error-field" style="display: none;"></div>
		<div class="success-field" style="display: none;"></div>

		<div class="progress" style="visibility: hidden;">
			<div class="indeterminate"></div>
		</div>

		<br/>

		{!! Form::open(array(
			'id'	=> 'reset-password-form',
			'url'	=> action('UserController@postForgotPassword'),
			'method'	=> 'POST'
		)) !!}

		<input type="hidden" id="token" value="{{ csrf_token() }}">

		<div class="input-field">
			<label for="" class="required">Email address</label>
			<input type="email" name="email" placeholder="" required/>
		</div>

	</div>

	<div class="modal-footer">
		<a class="modal-action modal-close btn ourscene-btn-plain-1">Cancel</a>
		<a id="reset-btn" class="btn ourscene-btn-plain-1" onClick="$('<input type=\'submit\'>').hide().appendTo('#reset-password-form').click().remove();">Reset</a>
	</div>

		{!! Form::close() !!}
</div>
<script>
$(document).ready(function(){

	$('#reset-password-form').submit(function(e){

		e.preventDefault();

		var $modal = $('#reset-password-modal');
		var $progress_bar = $modal.find('.progress');

		//hide errors and successes
		$('.error-field').hide();
		$('.success-field').hide();

		//show progress bar
		$progress_bar.css('visibility', 'visible');

		$.ajax({
			url: ROOT+'/reset-password',
			type: "POST",
			data: $('#reset-password-form').serialize(),
			success: function(data){
				
				//hide progress bar
				$progress_bar.css('visibility', 'hidden');

				if(data['error']){

					$error_field = $modal.find('.error-field');
					
					$error_field.html(data['error']);
					
					$error_field.show();
				}
				else if(data['success']){

					$success_field = $modal.find('.success-field');
					
					$success_field.html(data['success']);
					
					$success_field.show()

					setTimeout(function() {$modal.closeModal();}, 2000)
					
				}
			},
			error: function (jqXHR, status, err){

				//hide progress bar
				$progress_bar.css('visibility', 'hidden');

				$modal.find('.error-field').html('There was an error in processing your request. Please try again later.');
			}
		});

		
	});
});
</script>