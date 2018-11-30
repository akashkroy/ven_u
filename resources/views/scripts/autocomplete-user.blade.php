<script>

$(document).ready(function(){

	$('{{ $textbox_selector }}').on('keyup',function(e){

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
					 url: "{{ action('SearchController@getSearchResults') }}?param=" + thisElement.val(),
					 type: "GET", // default is GET but you can use other verbs based on your needs.
					 dataType: "json", // specify the dataType for future reference
					 success: function(data){
						$('.dropdown-content.autocomplete').empty()
					
					@if($user_type == "venue")
						var users = data.venues;
						var dropdown_title = "Venue";
					@elseif($user_type == "artist")
						var users = data.artists;
						var dropdown_title = "Artist";
					@endif

						//generate dropdown content HTML string

						var html="";

						if (!users.length < 1){
							html += '<span class="search-divider">'+dropdown_title+'</span> <li class="divider"></li>'
							for (var i = 0; i < users.length; i++) {
								if (i > 4) break;
								console.log(data)
								html += "<li>"
								html += '<a href="javascript:void(0);">'+ users[i].name +'</a>'
								html += '<div class="">'
								if(users[i].user_type == 'venue')
									html += '<span>'+dropdown_title+'</span><i>•</i><span>'+ users[i].address.city + ' ' + users[i].address.state + ' , ' + users[i].address.country   +'</span>';
								else
									html += '<span>'+dropdown_title+'</span><i>•</i><span>'+ users[i].address.city +'</span>';
								html += '<input type="hidden" class="id" value="'+ users[i]._id  +'">'
								html += '<input type="hidden" class="name" value="'+ users[i].name  +'">'
								html += '<input type="hidden" class="email" value="'+ users[i].email  +'">'
								html += '<input type="hidden" class="user_type" value="'+ users[i].user_type  +'">'
								html += '<input type="hidden" class="paypal_email" value="'+ users[i].paypal_info.email  +'">'
								html += '</div></li>'
								html += '<li class="divider"></li>'
							}
						}

						//append HTML string to dropdown

						$('.dropdown-content.autocomplete').append(html);
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
	
	// Click item event handler

	$('{{ $dropdown_selector }}').on('click','li:not(.search-divider)',function(){
		var id = $(this).find('div > input.id').val()
		$('.dropdown-content.autocomplete').hide()
		$('{{ $hidden_value_selector }}').val(id)
		$('{{ $textbox_selector }}').val($(this).find('a').text())
	});
});
</script>