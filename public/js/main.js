document.addEventListener("touchstart", function(){}, true);

$(document).ready(function(){
	
	/* Materialize */

	// Modal
	$('.modal-trigger').leanModal(); //the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered

	// Dropdownn
	$('select').material_select();

	// Date picker
	$('input[type="date"]').pickadate({
		selectMonths: true, // Creates a dropdown to control month
		selectYears: 20, // Creates a dropdown of 20 years to control year
		format: 'mm/dd/yyyy',
		min: new Date(),
	});

	$('.ourscene-date').pickadate({
		selectMonths: true, // Creates a dropdown to control month
		selectYears: 20, // Creates a dropdown of 20 years to control year
		format: 'mm/dd/yyyy',
		min: new Date(),
	});


	/* Time Picki */

	$(".time-picki-picker").each(function( index ) {
		var time = $(this).val();
		if(time != ''){
			
			split = time.split(" ");

			var meridian = split[1];

			split = split[0].split(":");
			
			var h = split[0];
			var m = split[1];

			$(this).timepicki({start_time: [h, m, meridian]});
		}
		else
			$(this).timepicki({start_time: ["12", "00", "AM"]});
	});

	// Hide sidebar dropdown result when not on focus
	$(document).on('click','body',function(e){
		if (!(e.target == $('#search-sidebar').get(0) || e.target == $('#search-sidebar-dropdown > li').find('*').get(0))) {
				$('#search-sidebar-dropdown').hide()
				$('#search-sidebar-dropdown').css({"opacity":0})
		}
	});

});

// <!--  AUTO COMPLETE -->
$(document).ready(function() {
	$.fn.customAutoComplete = function(){

		$(this).on('keyup',function(e){
		  // Hides dropdown if search is empty
		  var thisElement = $(this)
		  if (thisElement.val() == '') {
		    $('.dropdown-content.autocomplete').hide()
		    $('.dropdown-content.autocomplete').css({"opacity":0})
		    return
		  }
		  if ($.inArray(e.keyCode, [46, 9, 27, 13, 110, 190,91,16,93]) !== -1 ||
		     // Allow: Ctrl+A, Command+A
		    (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
		     // Allow: home, end, left, right, down, up
		    (e.keyCode >= 35 && e.keyCode <= 40)) {
		         // let it happen, don't do anything
		         return;
		  }else{
		    $( document ).ajaxStop();
		    $('.dropdown-content.autocomplete').show()
		    $('.dropdown-content.autocomplete').css({"opacity":1})
		    $('.dropdown-content.autocomplete').empty()
		    $.ajax({
		         url: "{{action('SearchController@getSearchResults')}}?param=" + thisElement.val(),
		         type: "GET", // default is GET but you can use other verbs based on your needs.
		         dataType: "json", // specify the dataType for future reference
		         success: function(data){
		          $('.dropdown-content.autocomplete').empty()
		          var venue="",artists=""
		          if (!data.venues.length < 1){
		            venue += '<span class="search-divider">Venue</span> <li class="divider"></li>'
		            for (var i = 0; i < data.venues.length; i++) {
		              if (i > 4) break;
		              console.log(data)
		              venue+= "<li>"
		              venue+= '<a href="javascript:void(0);">'+ data.venues[i].name +'</a>'
		              venue+= '<div class="">'
		              venue+= '<span>Venue</span><i>•</i><span>'+ data.venues[i].address.city + ' ' + data.venues[i].address.state + ' , ' + data.venues[i].address.country   +'</span>'
		              venue+= '<input type="hidden" class="id" value="'+ data.venues[i]._id  +'">'
		              venue+= '<input type="hidden" class="name" value="'+ data.venues[i].name  +'">'
		              venue+= '<input type="hidden" class="email" value="'+ data.venues[i].email  +'">'
		              venue+= '<input type="hidden" class="user_type" value="'+ data.venues[i].user_type  +'">'
		              venue+= '<input type="hidden" class="paypal_email" value="'+ data.venues[i].paypal_info.email  +'">'
		              venue+= '</div></li>'
		              venue+= '<li class="divider"></li>'
		              console.log(data.venues[i])
		            }
		          }
		          if (!data.artists.length < 1){
		            artists += '<span class="search-divider">Artist</span> <li class="divider"></li>'
		            for (var i = 0; i < data.artists.length; i++) {
		              if (i > 4) break;
		              artists+= "<li>"
		              artists+= '<a href="javascript:void(0);">'+ data.artists[i].name +'</a>'
		              artists+= '<div class="">'
		              artists+= '<span>Artist</span><i>•</i><span>'+ data.artists[i].city + '</span>'
		              artists+= '<input type="hidden" class="id" value="'+ data.artists[i]._id  +'">'
		              artists+= '<input type="hidden" class="name" value="'+ data.artists[i].name  +'">'
		              artists+= '<input type="hidden" class="email" value="'+ data.artists[i].email  +'">'
		              artists+= '<input type="hidden" class="user_type" value="'+ data.artists[i].user_type  +'">'
		              artists+= '<input type="hidden" class="paypal_email" value="'+ data.artists[i].paypal_info.email  +'">'
		              artists+= '</div></li>'
		              artists+= '<li class="divider"></li>'
		              console.log(data.artists[i])
		            }
		          }
		          $('.dropdown-content.autocomplete').append(venue)
		          $('.dropdown-content.autocomplete').append(artists)
		          $('.dropdown-content.autocomplete').css({"top": thisElement.offset().top +thisElement.height(), "left" : thisElement.offset().left})
		         }
		     }).done(function(data){
		       console.log("success")
		     }).fail(function(data){
		       console.log("error")
		     }).always(function(){
		       console.log("completed")
		     })
		  }
		});
	}

});
