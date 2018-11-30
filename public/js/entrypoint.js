	 $(document).ready(function(){
		$('ul.tabs').tabs();
		$('select:not(.operatinghrs,.ui-datepicker-month)').material_select();
	  });

	var login_type;

	function changeSignInType($this){
		if (login_type == 'artist') {
			$('#main').addClass('body-venue').removeClass('body-artist');
			$('#login-form').attr('action', loginVenueUrl);
			$('#user-type-label').text('Venue')
			$('#user-type-icon').attr('src', assetBaseUrl + 'images/icons/venue.svg');
			$('.change-type-btn').text("SIGN IN AS ARTIST");
			$("#signup-link").attr("href", registerVenueUrl);
			login_type = 'venue';
		}else{
			$('#main').addClass('body-artist').removeClass('body-venue');
			$('#login-form').attr('action', loginArtistUrl);
			$('#user-type-label').text('Artist');
			$('#user-type-icon').attr('src', assetBaseUrl + 'images/icons/artist.svg');
			$('.change-type-btn').text("SIGN IN AS VENUE");
			$('#signup-link').attr("href", registerArtistUrl);
			login_type = 'artist';
		}

	}

	function loadProfilePicture(event) {
	    var output = document.getElementById('profile-picture-preview');
	    output.src = URL.createObjectURL(event.target.files[0]);
	};

	$(document).ready(function() {
    	$('#register-btn').prop('disabled', true);
    	$('#terms-of-service-checbox').prop('checked',false);
	    $('#terms-of-service-checbox').change(function() {
	        if($(this).is(":checked")) {
	            $('#register-btn').prop('disabled', false);
	        }else{
	        	$('#register-btn').prop('disabled', true);
	        }
	    });
	});

	$(document).ready(function() {
		var maxNoOfSongs = 5;
		var maxNoOfImages = 5;
		var maxNoOfVideos = 5;
	    var song = 0;
	    var image = 0;
	    var video = 0;

	   //add and remove song
	    $('#add-more-songs').click(function(){
	    	console.log("add more song");
	    	if(song < maxNoOfSongs){
	    		song++;
	    		$('#register-materials-songs').append('<div class="col s12 m10 l10 file-field input-field">'+
		    			'<div class="row material-upload">'+
			    			'<div class="col s2 m2 l2 upload-btn">'+
			    				'<span>Song File</span>'+
	        					'<input type="file" id="materials-songs-'+ song +'" name="materials-songs-'+ song +'" accept="audio/mp3,audio/m4a,audio/wav,.mp3,.m4a,.wav">'+
			    			'</div>'+
			    			'<div class="col s8 m8 l8">'+
						        '<input class="file-path validate" type="text">'+
						    '</div>'+
						    '<div class="col s2 m2 l2">'+
						        '<a class="remove-song-btn"><img class="remove-material-icon" src="'+ROOT+'/images/icons/cancel.svg"></a>'+
						    '</div>'+
						'</div>'+
					'</div>');
				}
				$("#materials-songs-"+song).trigger('click');
	    });

	    $('#register-materials-songs').on('click', '.remove-song-btn', function(e){
	    	e.preventDefault();
	    	$(this).parent('div').parent('div').parent('div').remove();
	    	song--;
	    });

	   //add and remove images
	    $('#add-more-images').click(function(){
	    	if(image < maxNoOfImages){
	    		image++;
	    		$('#register-materials-images').append('<div class="col s12 m10 l10 file-field input-field">'+
		    			'<div class="row material-upload">'+
			    			'<div class="col s2 m2 l2 upload-btn">'+
			    				'<span>Image File</span>'+
	        					'<input type="file" id="materials-images-'+ image +'" name="materials-images-'+ image +'" accept="image/gif,image/jpg,image/png,image/jpeg,.gif,.jpg,.png,.jpeg">'+
			    			'</div>'+
			    			'<div class="col s8 m8 l8">'+
						        '<input class="file-path validate" type="text">'+
						    '</div>'+
						    '<div class="col s2 m2 l2">'+
						        '<a class="remove-image-btn"><img class="remove-material-icon" src="'+ROOT+'/images/icons/cancel.svg"></a>'+
						    '</div>'+
						'</div>'+
					'</div>');
	    		$("#materials-images-"+image).trigger('click');
	    	}
	    });

	    $('#register-materials-images').on('click', '.remove-image-btn', function(e){
	    	e.preventDefault();
	    	$(this).parent('div').parent('div').parent('div').remove();
	    	image--;
	    });

	   //add and remove videos
	    $('#add-more-videos').click(function(){
	    	if(video < maxNoOfVideos){
	    		video++;
	    		$('#register-materials-videos').append('<div class="col s12 m10 l10 file-field input-field">'+
		    			'<div class="row material-upload">'+
			    			'<div class="col s2 m2 l2 upload-btn">'+
			    				'<span>Video File</span>'+
	        					'<input type="file" id="materials-videos-'+ video +'" name="materials-videos-'+ video +'" accept="video/mp4,video/x-m4v,video/quicktime">'+
			    			'</div>'+
			    			'<div class="col s8 m8 l8">'+
						        '<input class="file-path validate" type="text">'+
						    '</div>'+
						    '<div class="col s2 m2 l2">'+
						        '<a class="remove-video-btn"><img class="remove-material-icon" src="'+ROOT+'/images/icons/cancel.svg"></a>'+
						    '</div>'+
						'</div>'+
					'</div>');
	    		$("#materials-videos-"+video).trigger('click');
	    	}
	    });

	    $('#register-materials-videos').on('click', '.remove-video-btn', function(e){
	    	e.preventDefault();
	    	$(this).parent('div').parent('div').parent('div').remove();
	    	video--;
	    });


	});
