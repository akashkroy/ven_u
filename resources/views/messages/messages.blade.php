<?php
	use OurScene\Models\User;
?>

@extends('ourscene.layouts.main')

@section('content')
<script type="text/javascript" src="{{ asset('js/list-all-messages.js') }}"></script>

<div class="dropdown-users">

</div>

<div class="container-fluid">
	<div class="row">
		<div id="message-form" class="card col s12 m12 l8 offset-l2 ">
	    <div class="card-action title">
	      <div class="row" style="margin-bottom:0;">
	      	<div class="col s9">
		      	<img src="{{asset('images/icons/message-purple.svg')}}" alt="" style="width: 25px;display:inline-block; top: 7px; position:relative; margin-right: 4px;"/>
				<h5 style="display: inline-block;">Messages</h5>
		    </div>
			<div class="col s3 hide-on-med-and-up">
				<a href="{{action('MessageController@getNewMessage')}}" type="button" style="float:right;" class="btn btn-primary">New</a>
			</div>
			<div class="col s3 hide-on-small-only">
				<a href="{{action('MessageController@getNewMessage')}}" type="button" style="float:right;" class="btn btn-primary">New Message</a>
			</div>
	      </div>
	  	</div>
	    <div class="message-content">
	      <div class="row">
	        <div id="messages-conversation" class="col s12">
	          	<ul class="collection" id="messages_container_ul">
				
				</ul>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
</div>

<script type="text/javascript">
	 var user_id = "{{Session::get('id')}}";
	function getMessageList(){	
		console.log("get message");
		$.ajax({
				 url: "{{ action('MessageController@getAjaxHeaderMessages')}}/"+user_id,
				 type: "GET", 
				 dataType: "json",
				 success: function(data){
					
					if (data == "false") {
						
					}else {
						var messages = data;
						var $messages_container = $("#messages_container_ul");
						$messages_container.html('');

						for (var index in messages){
							var conversation = messages[index];
							var conversation_str = '<li class="collection-item avatar row" style="padding:0 !important">'
							conversation_str += '<a href='
							conversation_str += "{{action('MessageController@getMessage','')}}" + "/" + conversation['_id'] + " >";
							
							conversation_str += '<div class="col s2 m1 l1">'
							conversation_str += '<img src=' 
							if(conversation['image'] !== null && conversation['image'] != "")
								conversation_str += conversation['image'] 
							else{
								conversation_str += "{{asset('images/icons/profile-pic.png')}}"
							}
							conversation_str += ' class="circle">'
							conversation_str += '</div>'

							conversation_str += '<div class="col s8 m10 l10">'
							conversation_str += '<div class="row">'
							conversation_str += '<span class="conversation-title col s12 m12 l12"'

							if(conversation['is_read']){
								conversation_str += 'style="color:#111;"'
							}
							conversation_str += '>' + conversation['title'] + '</span>'
							conversation_str += '</div>'
							
							conversation_str += '<div class="row">'
							conversation_str += '<p class="message col s12 m12 l12">'
							conversation_str += '<span '
							if(conversation['is_read']){
								conversation_str += 'style="color:#111;"'
							}
							conversation_str += '>' + conversation['last_msg']['body'] + '</span></p></div></div>'
							conversation_str += '<span class="secondary-content message-time col s1 m1 l1">'

							var epoch = parseInt(conversation['last_msg']['time']);
							var messageDate = new Date(epoch * 1000);
							var messageDay = new Date(epoch * 1000).setHours(0, 0, 0, 0);
							var today = new Date().setHours(0, 0, 0, 0);
							var time = " ";
							if(messageDay.valueOf() === today.valueOf()){
								time = messageDate.toLocaleTimeString().substr(0,messageDate.toLocaleTimeString().lastIndexOf(":"));
								time += " " + messageDate.toLocaleTimeString().substr(messageDate.toLocaleTimeString().lastIndexOf(":") + 3)
							}else{
								time = (messageDate.getMonth() + 1) + "/" + messageDate.getDate();
							}

							conversation_str += time;
							conversation_str += "</span></a></li>";
							$messages_container.append(conversation_str);
						}
					}
				 }
		 }).done(function(data){
		 }).fail(function(data){
		 }).always(function(){
		 })
	}
	getMessageList();
	var autoReloadMessageList = setInterval("getMessageList()", 3000);

	</script>
@endsection
