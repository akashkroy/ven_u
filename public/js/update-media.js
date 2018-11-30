
$(".media_images").fancybox();
$('.fancybox-media').fancybox({
	openEffect  : 'none',
	closeEffect : 'none',
	helpers : {
		media : {}
	}
});

$(document).ready(function() {
	var maxNoOfSongs = 5;
	var maxNoOfImages = 5;
	var maxNoOfVideos = 5;
	materials_has_loaded_animators = false;
	// when form is submitted
	$('#update-media-form').submit(function(e){

		//validate
		var file_size_exceeded = false;		
		var totalsize = 0;

		//Check file size
	   $('#update-media-form input:file').each(function(){
	     if($(this).val().length > 0){
	        totalsize=totalsize+$(this)[0].files[0].size;
	      }
	 	});
	  	if (totalsize > 335544320) {
	  	  console.log('did exceed');
	  	  file_size_exceeded = true;
	      $('#file-size-exceeded-error').show();
	  	}

	  	if(file_size_exceeded){
	  		e.preventDefault();
	  		console.log('prevent default file exceeded');
	  		//scroll to top
			$('html, body').animate({scrollTop : 0},800);
	  	}

	  	if(!file_size_exceeded && !materials_has_loaded_animators){
			e.preventDefault()

			$('#file-size-exceeded-error').hide();
				
			console.log('prevent default add loading');

			$('#register-materials-images').find(".file-field.input-field.row ").each(function(){
				$('#register-materials-images').append('<div class="row"> <div class="col s7"> <div class="progress"> <div class="indeterminate"></div> </div> </div> </div>')
				return false;
			})

			$('#register-materials-videos').find(".file-field").each(function(){
				$('#register-materials-videos').append('<div class="row"> <div class="col s7"> <div class="progress"> <div class="indeterminate"></div> </div> </div> </div>')
				return false;
			})

			$('#register-materials-songs').find(".file-field ").each(function(){
				$('#register-materials-songs').append('<div class="row"> <div class="col s7"> <div class="progress"> <div class="indeterminate"></div> </div> </div> </div>')
				return false;
			})

			$('#media-update-form-submit-btn').prop("disabled",true)
			$('#media-update-form-submit-btn').css({"opacity":".6"})
			$('#media-update-form-submit-btn').text('UPLOADING ')
			$('#media-update-form-submit-btn').append('<img src="'+media_uploader+'" style="position:relative;top: 3px; " alt="" width="13px" />')
			materials_has_loaded_animators = true;
			$(this).submit()
		}
	})


	//add and remove song
	$('#add-more-songs').click(function(){
		console.log("add more song");
		if(song < maxNoOfSongs){
			song++;
			$('#register-materials-songs').append('<div class="file-field col s12"> <div class="file-btn btn col s2 m2 l2"> <span>File</span> <input type="file" id="materials-songs-'+song+'" name="materials-songs-'+song+'" accept="audio/mp3,audio/m4a,audio/wav,.mp3,.m4a,.wav"> </div> <div class="file-path-wrapper col s4 m4 l4"> <input class="file-path validate" type="text"> </div> <div class="col s2 m2 l2" id="register-materials-songs"> <a href="javascript:void(0);" id="remove-material-song" class="col s1 m1 l1 s-offset-1 m-offset-2 l-offset-2 material-icons"> <img src="'+delete_icon+'" class="remove-media-icon"> </a> </div> </div>');
			$("#materials-songs-"+song).trigger('click');
		}
		if(song == maxNoOfSongs){
			$(this).addClass('disabled');
		}
	});

	$('#register-materials-songs').on('click', '#remove-material-song', function(e){
		e.preventDefault();
		$(this).parent('div').parent('div').remove();
		$('#add-more-songs').removeClass('disabled');
		song--;
	});


	//add and remove images
	$('#add-more-images').click(function(){
		if(image < maxNoOfImages){
			image++;
			$('#register-materials-images').append('<div class="file-field input-field row "> <div class="file-btn btn col s2 m2 l2"> <span>File</span> <input type="file" id="materials-images-'+ image+'" name="materials-images-'+image+'" accept="image/gif,image/jpg,image/png,image/jpeg,.gif,.jpg,.png,.jpeg" style=""> </div> <div class="file-path-wrapper col s4 m4 l4"> <input class="file-path validate" type="text"> </div> <div class="col s2 m2 l2"> <a href="javascript:void(0);" id="remove-material-image" class="col s1 m1 l1 s-offset-1 m-offset-2 l-offset-2" style=> <img src="'+delete_icon+'" class="remove-media-icon"> </a> </div> </div>');
			$("#materials-images-"+image).trigger('click');
			if(image == maxNoOfImages){
				$(this).addClass('disabled');
			}
		}
	});

	$('#register-materials-images').on('click', '#remove-material-image', function(e){
		e.preventDefault();
		$(this).parent('div').parent('div').remove();
		$('#add-more-images').removeClass('disabled');
		image--;
	});

	//add and remove videos
	$('#add-more-videos').click(function(){
		if(video < maxNoOfVideos){
			video++;
			$('#register-materials-videos').append('<div class="file-field col s12"> <div class="file-btn btn col s2 m2 l2"> <span>File</span> <input type="file" id="materials-videos-'+video+'" name="materials-videos-'+video+'" accept="video/mp4,video/x-m4v,video/quicktime"> </div> <div class="file-path-wrapper col s4 m4 l4"> <input class="file-path validate" type="text"> </div> <div class="col s2 m2 l2" id="register-materials-videos"> <a href="javascript:void(0);" id="remove-material-video" class="col s1 m1 l1 s-offset-1 m-offset-2 l-offset-2 material-icons"> <img src="'+delete_icon+'" class="remove-media-icon"> </a> </div> </div>');
			$("#materials-videos-"+video).trigger('click');
		}
		if(video == maxNoOfVideos){
			$(this).addClass('disabled');
		}
	});

	$('#register-materials-videos').on('click', '#remove-material-video', function(e){
		e.preventDefault();
		$(this).parent().parent('div').remove();
		$('#add-more-videos').removeClass('disabled');
		video--;
	});


	// Delete images
	$("#material-images").on('click','.remove-material-images',function(e){
		e.preventDefault()
		$(this).find('img').show()
		var selecteMaterialImageId = ($(this).parent().find('input').val())
		console.log(selecteMaterialImageId)
		$.ajax({
			url: delete_material_action + "?material_id=" + selecteMaterialImageId,
			type: "POST", // default is GET but you can use other verbs based on your needs.
				dataType: "json", // specify the dataType for future reference
				success: function(data){
					image--;
			 		$(e.target).closest('div').remove();
			 		$('#add-more-images').removeClass('disabled');
				}
		 }).done(function(data){
		 }).fail(function(data){
		 }).always(function(){
		 })
	})

	// Delete videos
	$(document).on('click','.remove-material-videos',function(e){
		e.preventDefault()
		$(this).find('img').show()
		var selecteMaterialVideoId = ($(this).parent().find('input').val())
		$(this).prop("disabled",true)
		$.ajax({
			url: delete_material_action + "?material_id=" + selecteMaterialVideoId,
			type: "POST",
				dataType: "json",
				success: function(data){
					video--;
			 		$(e.target).closest('div').remove();
			 		$('#add-more-videos').removeClass('disabled');
				}
		 }).done(function(data){
		 }).fail(function(data){
		 }).always(function(){
		 })
	})

	// Delete songs
	$(document).on('click','.remove-material-songs',function(e){
		e.preventDefault()
		$(this).find('img').show()

		var selecteMaterialSongId = ($(this).parent().find('input').val())
		$(this).prop("disabled",true)
		$.ajax({
			url: delete_material_action + "?material_id=" + selecteMaterialSongId,
			type: "POST",
				dataType: "json",
				success: function(data){
					$(this).prop("disabled",false)
					song--;
					$(e.target).closest('div').remove();
					$('#add-more-songs').removeClass('disabled');
				}
		 }).done(function(data){
		 }).fail(function(data){
			 $(this).prop("disabled",false)
		 }).always(function(){
		 })
		 return false;
	})
});
