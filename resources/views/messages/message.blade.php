<?php
	use OurScene\Models\User;
	use OurScene\Helpers\ChatSessionHelper;
?>

@extends('ourscene.layouts.main')

@section('content')
<script>
	$(document).ready(function(){
    $('.tooltipped').tooltip({delay: 50});
  });
</script>
<div class="container-fluid">
	<div class="row">
		<div id="message-form" class="card col s12 m12 l8 offset-l2 messsage-all">

		    <div class="card-action title">
		    	<div class="row" style="margin-bottom:0;">
					<div class="col s8" style="display: inline-block;">
						<img src="{{asset('images/icons/message-purple.svg')}}" alt="" style="width: 25px;display:inline-block; top: 7px; position:relative; margin-right: 4px; float:left;"/>
						<h5 data-position="bottom" data-delay="50" data-tooltip="{{$conversation['title']}}" style="display: block; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; " class="con title tooltipped">
							{{$conversation['title']}}
						</h5>
				    </div>
					<div class="col s4">
						<a style="width:100%;" href="{{action('MessageController@index')}}" type="button" class="btn btn-primary">Back</a>
					</div>
		    	</div>
		  	</div>

		    <div class="message-content">
		    	<div class="row">
			        <div id="messages-conversation" class="col s12">
			          <ul id="conversation-list" class="collection">

			          </ul>
			          <div class="col s12 message-footer">
			            <form class="" action="" method="post">
				            <div class="input-field col s8">
				                <textarea id="text-message" class="materialize-textarea" placeholder="Type message..."></textarea>
				            </div>
				            <div class="input-field col s4">
				                <button style="width:100%;" id="reply" type="button" class="btn btn-primary" name="button">Send</button>
				            </div>
			            </form>
			          </div>
			        </div>
		     	</div>
		    </div>
	  </div>
	</div>
</div>
@endsection

@section('scripts')
<script>
	var user_profile_pic 		= "@if(getProfilePicture(Session::get('id')) != "") {{getProfilePicture(Session::get('id'))}} @else {{asset("images/icons/profile-pic.png")}} @endif";
	var conn 					= new WebSocket("ws://{{env('RATCHET_SERVER')}}:8080");
	var conversation_id 		= "{{$conversation['_id']}}";
	var user_id 				= "{{Session::get('id')}}";
	var default_profile_pic 	= "{{asset("images/icons/profile-pic.png")}}";
	var get_conversation_url	= "{{action('MessageController@getConversation')}}?thread={{$conversation['_id']}}";
</script>
<script type="text/javascript" src="{{ asset('js/conversation.js') }}"></script>
@stop
