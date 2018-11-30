var participants =[];
	$('#modal1').leanModal();
	$(document).on('keyup','#users',function(e){
		if ($(this).val() == '') {
			$('#dropdown-user').hide()
			$('#dropdown-user').css({"opacity":0})
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
			$('#dropdown-user').show()
			$('#dropdown-user').css({"opacity":1})
			$('#dropdown-user').empty()
			$.ajax({
				 url: "{{action('SearchController@getSearchResults')}}?param=" + $(this).val(),
				 type: "GET",
				 dataType: "json",
				 success: function(data){
					$('#dropdown-user').empty()
					var venue="",artists=""
					if (!data.venues.length < 1){
						venue += '<span class="search-divider">Venue</span> <li class="divider"></li>'
						for (var i = 0; i < data.venues.length; i++) {
							if (i > 4) break;
							venue+= '<li class="user-result">'
							venue+= '<input class="user-result-id" type="hidden" value="'+ data.venues[i]._id+'"/>'
							venue+= '<a href="javascript:void(0);">'+ data.venues[i].name +'</a>'
							venue+= '<div class="">'
							venue+= '<span>Venue</span><i>•</i><span>'+ data.venues[i].address.city + ' ' + data.venues[i].address.state + ' , ' + data.venues[i].address.country   +'</span>'
							venue+= '</div></li>'
							venue+= '<li class="divider"></li>'
						}
					}
					if (!data.artists.length < 1){
						artists += '<span class="search-divider">Artist</span> <li class="divider"></li>'
						for (var i = 0; i < data.artists.length; i++) {
							if (i > 4) break;
							artists+= '<li class="user-result">'
							artists+= '<input class="user-result-id" type="hidden" value="'+ data.artists[i]._id+'"/>'
							artists+= '<a href="javascript:void(0);" >'+ data.artists[i].name +'</a>'
							artists+= '<div class="">'
							artists+= '<span>Artist</span><i>•</i><span>'+ data.artists[i].city + '</span>'
							artists+= '</div></li>'
							artists+= '<li class="divider"></li>'
						}
					}
					$('#dropdown-user').append(venue)
					$('#dropdown-user').append(artists)
				 }
			 }).done(function(data){
				 console.log("success")
			 }).fail(function(data){
				 console.log("error")
			 }).always(function(){
				 console.log("completed")
			 })
		}

	})
	$(document).on('click','.user-result',function(){
		var str = '<div class="chip"> '+ $(this).find('a').text() +' <i class="material-icons">close</i> </div>'
		var id = $(this).find('input').val()
		participants.push(id)
		$('#dropdown-user').hide()
		$('#user-chips').append(str)
	})