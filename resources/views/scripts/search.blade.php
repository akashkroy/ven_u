<script>

$(document).ready(function(){

	// Keydown event search bar

	var keyCtr = null;
	var hasSearchResults = false;

	$(document).on('keyup','{{ $textbox_selector }}', function(e){

		// Hides dropdown if search is empty)
		if ($(this).val() == '') {
			$('{{ $dropdown_selector }}').hide()
			$('{{ $dropdown_selector }}').css({"opacity":0})
			return
		}

		if ($.inArray(e.keyCode, [46, 9, 27, 110, 190,91,16,93]) !== -1 ||
         // Allow: Ctrl+A, Command+A
        (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ) {
             // let it happen, don't do anything
             return;
        }
        else{
			if (e.keyCode != 38 && e.keyCode != 40 && e.keyCode != 39 && e.keyCode != 37 && e.keyCode != 13) {
				$( document ).ajaxStop();
				$('{{ $dropdown_selector }}').show()
				$('{{ $dropdown_selector }}').css({"opacity":1})
				$('{{ $dropdown_selector }}').empty()
				$.ajax({
					url: "{{action('SearchController@getSearchResults')}}?param=" + $(this).val(),
					type: "GET", // default is GET but you can use other verbs based on your needs.
					dataType: "json", // specify the dataType for future reference
					success: function(data){

						$('{{ $dropdown_selector }}').empty()
						
						if(data.venues.length + data.artists.length == 0){

							var no_results="";

							no_results+= "<li>"
							no_results+= '<div class="">'
							no_results+= '<span style="font-size: 1rem;"><br/>No results found.</span>'
							no_results+= '</div>'
							no_results+= "</li>"

							$('{{ $dropdown_selector }}').append(no_results);

							hasSearchResults=false;
						}
						else{
							var venue="", artists="", all_results=""

							if (!data.venues.length < 1){
								venue += '<span class="search-divider">Venue</span> <li class="divider"></li>'
								for (var i = 0; i < data.venues.length; i++) {
									venue+= "<li>"
									venue+= '<a href="{!! url('/profile') !!}/'+ data.venues[i]._id +'">'+ data.venues[i].name +'</a>'
									venue+= '<div class="">'
									venue+= '<span>Venue</span><i>•</i><span>'+ data.venues[i].address.city + ' ' + data.venues[i].address.state + ', ' + data.venues[i].address.country   +'</span>'
									venue+= '</div></li>'
									venue+= '<li class="divider"></li>'
								}
							}
							if (!data.artists.length < 1){
								artists += '<span class="search-divider">Artist</span> <li class="divider"></li>'
								for (var i = 0; i < data.artists.length; i++) {
									artists+= "<li>"
									artists+= '<a href="{!! url('/profile') !!}/'+ data.artists[i]._id +'">'+ data.artists[i].name +'</a>'
									artists+= '<div class="">'
									artists+= '<span>Artist</span><i>•</i><span>'+ data.artists[i].address.city + '</span>'
									artists+= '</div></li>'
									artists+= '<li class="divider"></li>'
								}
							}

							var all_results_link = "{{ action('SearchController@getSearch') }}?params=" + $('{{ $textbox_selector }}').val()
							
							all_results += '<span class="search-divider"><a href="' + all_results_link + '">VIEW ALL RESULTS</a></span>'

							$('{{ $dropdown_selector }}').append(venue);
							$('{{ $dropdown_selector }}').append(artists);
							$('{{ $dropdown_selector }}').append(all_results);

							hasSearchResults=true;
						}

						keyCtr = null;


            		}
	           	}).done(function(data){
					console.log("success")
				}).fail(function(data){
	         		console.log("error")
				}).always(function(){
	         		console.log("completed")
	       		});
			}else {
				// up
				if (e.keyCode == 38 && hasSearchResults) {

					if (keyCtr > 0) {

						keyCtr--
					}
					$("{{ $dropdown_selector }} li").removeClass('active')
					$("{{ $dropdown_selector }} li:not(.divider)").eq(keyCtr).addClass('active')
					// alert(keyCtr)
				} //down
				else if (e.keyCode == 40 && hasSearchResults) {
					$("{{ $dropdown_selector }} li").removeClass('active')
					$("{{ $dropdown_selector }} li:not(.divider)").eq(keyCtr).addClass('active')
					if (keyCtr < $("{{ $dropdown_selector }} li:not(.divider)").length -1) {
						keyCtr++

					}
				}
				/*else if (e.keyCode == 13 && $("{{ $dropdown_selector }} li:not(.divider)").length > 0 && keyCtr != null) {
					window.location = $("{{ $dropdown_selector }} li:not(.divider)").eq(keyCtr).find('a').attr('href')
				}*/
				else if (e.keyCode == 13 && $("{{ $dropdown_selector }} li.active").length > 0) {
					window.location = $("{{ $dropdown_selector }} li.active").find('a').attr('href')
				}
				else if(e.keyCode == 13 && keyCtr == null){
					location.href = "{{action('SearchController@getSearch')}}?params=" + $('{{ $textbox_selector }}').val()
				}

			}
		}
	});
});
</script>
