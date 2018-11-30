<script>

$(document).ready(function(){

	// Keydown event search bar

	$(document).on('keyup','{{ $textbox_selector }}',function(e){

		// Hides dropdown if search is empty
		if ($(this).val() == '') {
			$('{{ $dropdown_selector }}').hide()
			$('{{ $dropdown_selector }}').css({"opacity":0})
			return
		}

		if(e.keyCode == 13){ // Enter
			location.href = "{{action('SearchController@getSearch')}}?params=" + $('{{ $textbox_selector }}').val()
		}
		else if ($.inArray(e.keyCode, [46, 9, 27, 13, 110, 190,91,16,93]) !== -1 ||
		 // Allow: Ctrl+A, Command+A
		(e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
		 // Allow: home, end, left, right, down, up
		(e.keyCode >= 35 && e.keyCode <= 40)) {
		     // let it happen, don't do anything
		     return;
		}else{
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
							no_results+= '<div class="" style="padding-top: 5px;">'
							no_results+= '<span style="font-size: 1.2rem;">No results found.</span>'
							no_results+= '</div>'
							no_results+= "</li>"

							$('{{ $dropdown_selector }}').append(no_results);
						}
						else{
							var venue="", artists="";
							
							if (!data.venues.length < 1){
								venue += '<li><span class="search-divider">Venue</span> <li class="divider"></li>'
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
								artists += '<li><span class="search-divider">Artist</span> <li class="divider"></li>'
								for (var i = 0; i < data.artists.length; i++) {
									artists+= "<li>"
									artists+= '<a href="{!! url('/profile') !!}/'+ data.artists[i]._id +'">'+ data.artists[i].name +'</a>'
									artists+= '<div class="">'
									artists+= '<span>Artist</span><i>•</i><span>'+ data.artists[i].city + '</span>'
									artists+= '</div></li>'
									artists+= '<li class="divider"></li>'
								}
							}
							$('{{ $dropdown_selector }}').append(venue)
							$('{{ $dropdown_selector }}').append(artists)
						}

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
});
</script>
