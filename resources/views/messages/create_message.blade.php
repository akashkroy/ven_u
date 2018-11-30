<?php
	use OurScene\Models\User;
?>

@extends('ourscene.layouts.main')

@section('content')

<script type="text/javascript">

// User List dropdown
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
          if (!data.venues.length < 1){ // add venues on search dropdown
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
          if (!data.artists.length < 1){ // add artist on search dropdown
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
					// append the search result
          $('#dropdown-user').append(venue)
          $('#dropdown-user').append(artists)

         }
     }).done(function(data){
     }).fail(function(data){
     }).always(function(){
     })
  }

})//End of user list dropdown

var participants = ["{{Session::get('id')}}"];

// Add selected user as a chip
$(document).on('click','.user-result',function(){
	var id = $(this).find('input').val()
  var name = $(this).find('a').text()
	var str = '<div class="chip" id="'+ id +'"> '+ name +' <i class="material-icons">close</i> </div>';
	if ($.inArray(id, participants) < 0) {
		participants.push(id)
	  $('#user-chips').append(str)
	}
  $('#'+id).material_chip({
    data : [{
      tag: name,
    }]
  });
  $('#'+id).find(":input").remove();
	$('#dropdown-user').hide()
  $('#users').val('')
  $('#'+id).on('chip.delete', function(e, chip){
    var index = participants.indexOf(id);
    participants.splice(index,1);
    this.remove();
  });
})


// Create message via Ajax and Redirect to message view after sending
$(document).on('click','#reply',function(){
  if($('#text-message').val().length > 0 && participants.length > 1){
  	var url = "{{action('MessageController@createMessage')}}?thread=&user={{Session::get('id')}}&message=" + $('#text-message').val() + "&participants=" + participants;
  	$.ajax({
  			 url: url,
  			 type: "GET",
  			 dataType: "json",
  			 success: function(data){
  				 location.href = "{{action('MessageController@getMessage','')}}/" + data.thread

  			 }
  	}).done(function(data){
  	}).fail(function(data){
  	}).always(function(){
  	})
  	$('#text-message').val('')
  }
})
@if($user)
  participants.push("{{$user}}")
@endif
</script>

<div class="row">
  <div id="message-form" class="card col s12 m12 l8 offset-l2">
    
    <div class="card-action title">
      <div class="row" style="margin-bottom: 0;">
				<div class="col s7 m9 l9">
					<img src="{{asset('images/icons/message-purple.svg')}}" alt="" style="width: 25px;display:inline-block; top: 7px; position:relative; margin-right: 4px;"/>
					<h5 style="display: inline-block;">New Message</h5>
				</div>
				<div class="col s5 m2 l2">
					<a style="width:100%;" href="{{action('MessageController@index')}}" type="button" class="btn btn-primary">Back</a>
				</div>
      </div>
  	</div>

    <div class="message-content">
      <div class="row">
        <div class="input-field col s12">
          <input autocomplete="off" id="users" type="text" name="name" class="validate" data-activates='dropdown-user'>
          <ul id='dropdown-user' class='dropdown-content'>
            
          </ul>
          <label for="name">To:</label>
        </div>
        <div id="user-chips" class="col s12">
          @if($user)
            <div class="chip"> {{User::where("_id",$user)->first()["name"]}} <i class="material-icons">close</i> </div>
          @endif
        </div>
      </div>

      <div class="row">
        <div id="messages-conversation" class="col s12">
          
          <ul class="collection">
           
          </ul>

          <div class="col s12 message-footer">
            <form class="" action="" method="post">
              <div class="input-field col s8 m9 l9">
                <textarea id="text-message" class="materialize-textarea" placeholder="Type message..."></textarea>
              </div>
              <div class="input-field col s4 m3 l3">
                <button style="width:100%;" id="reply" type="button" class="btn btn-primary" name="button">Send</button>
              </div>
            </form>
          </div>

        </div> <!-- end of message conversation -->
      </div> <!-- end of row -->
    </div>
  </div>
</div>


@endsection('content')
