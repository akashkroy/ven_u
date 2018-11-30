$(document).ready(function() {

	$( '#update-payment-btn' ).on( 'click', function( event ) {

		var $post = $('#update-payment-form').serialize();
		console.log("clicked");

		$.ajax({
			url: ROOT+'/update-payment-info',
			type: "POST",
			data: $post,
			success: function (data) {
				if(data['error']){
					console.log(data['error']);
					//show error
					// document.getElementById('alert-message').innerHTML= data['error'];
					// $('#modal-alert').modal();
				}else{
					//show success
					console.log("Success changing payment info")
					// document.getElementById('alert-message').innerHTML='Success!';
				}
			},
			error: function (data){
				//show error
				console.log("ERROR");
			}
		});
	});
});

function loadProfilePicture(event) {
    // var output = document.getElementById('profile-picture-preview');
    // output.src = URL.createObjectURL(event.target.files[0]);
		var file = event.target.files[0]
		var reader = new FileReader();
		reader.onloadend = function () {
			$('#profile-picture-preview').css({"background-image":"url('" + reader.result + "')"})
    }
		if (file) {
      reader.readAsDataURL(file);
    }

};
