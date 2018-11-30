 	var monthNames = ["January", "February", "March", "April", "May", "June",
		  "July", "August", "September", "October", "November", "December"
	];

	var last_message_day = "";
	var last_sender = "";
	// Get Messages from User
	function getMessages(){
		$('#conversation-list').empty()
		$.ajax({
				 url: get_conversation_url,
				 type: "GET",
				 dataType: "json",
				 success: function(data){
					var str = "";
					data.messages.forEach(function(item){
						
						console.log(item)
						var epoch = item.time.sec;
						var messageDate = new Date(epoch * 1000);
						var messageDay = new Date(epoch * 1000).setHours(0, 0, 0, 0);
						var today = new Date().setHours(0, 0, 0, 0);
						var day_str = monthNames[messageDate.getMonth()] + " " + messageDate.getDate() + ", " + messageDate.getFullYear();
						var time_str = messageDate.toLocaleTimeString().substr(0,messageDate.toLocaleTimeString().lastIndexOf(":"));
						var period_str = messageDate.toLocaleTimeString().substr(messageDate.toLocaleTimeString().lastIndexOf(":") + 3);

						var message_timestamp = message_timestamp = time_str + " " + period_str;


						var is_new_day = last_message_day.valueOf() !== messageDay;
						if(is_new_day){
							str += "<p class='center-align'>" + day_str + "</p>"
						}

						str+= '<li class="collection-item avatar">'

						var is_new_sender = last_sender != item.sender_id;
						if(is_new_sender || is_new_day){
							if (item.image){
								str+=   '<img src="'+item.image+'" alt="" class="circle">'
							}else{
								str+= 	'<img src="'+ default_profile_pic + '" alt="" class="circle">'
							}
							str+= 	'<span class="title name">'+ item.sender_name +'</span>'
						}

						last_sender = item.sender_id;
						last_message_day = messageDay;

						str+= 	'<p class="message">' + item.body + '</p>'
						str+= 	'<span  class="secondary-content">' + message_timestamp + '</span>'
						str+= 	'</li>'
					})
					$('#conversation-list').append(str)
				 }
		}).done(function(data){
			$('#conversation-list').scrollTop(99999999)
			conn.send(JSON.stringify({
			 "type" : "read",
			 "user_id": user_id,
			 "message_id" : conversation_id,
			 "status": "read"
			}))
		}).fail(function(data){
			console.log("error")
		}).always(function(){
			console.log("completed")
		})
	}
	
	conn.onopen = function(e) {
	    console.log("Connection established!");
		getMessages();
	};

	conn.onmessage = function(e) {
			var result = JSON.parse(e.data)

			if(result.message_id != conversation_id){
				return
			}

			if (result.status == "success") {

				var str = '';

				var epoch = result.time.sec;
				var messageDate = new Date(epoch * 1000);
				var messageDay = new Date(epoch * 1000).setHours(0, 0, 0, 0);
				var today = new Date().setHours(0, 0, 0, 0);
				var day_str = monthNames[messageDate.getMonth()] + " " + messageDate.getDate() + ", " + messageDate.getFullYear();
				var time_str = messageDate.toLocaleTimeString().substr(0,messageDate.toLocaleTimeString().lastIndexOf(":"));
				var period_str = messageDate.toLocaleTimeString().substr(messageDate.toLocaleTimeString().lastIndexOf(":") + 3);

				var message_timestamp = message_timestamp = time_str + " " + period_str;

				var is_new_day = last_message_day.valueOf() !== messageDay;
				if(is_new_day){
					str += "<p class='center-align'>" + day_str + "</p>"
				}

				str += '<li class="collection-item avatar">'

				var is_new_sender = last_sender != result.sender_id;
				if(is_new_sender || is_new_day){
					if (result.image){
						str+=   '<img src="'+result.image+'" alt="" class="circle">'
					}else{
						str+= 	'<img src="'+ default_profile_pic +'" alt="" class="circle">'
					}
					str+= 	'<span class="title name">'+ result.sender_name +'</span>'
				}

				last_sender = result.sender_id;
				last_message_day = messageDay;

				str+= 	'<p class="message">' + result.body + '</p>'
				str+= 	'<span  class="secondary-content">' + message_timestamp + '</span>'
				str+= 	'</li>'

				$('#conversation-list > li:last-child').after(str)
				$('#conversation-list').scrollTop(99999999)

				conn.send(JSON.stringify({
				 "type" : "read",
				 "user_id": user_id,
				 "message_id" : conversation_id,
				 "status": "read"
				}))
			}else{
				var str = "";
				str+= '<li class="collection-item avatar">'
				str+= '<img src="'+ user_profile_pic +'" alt="" class="circle">'
				str+= '<span class="title name" style="color:#8c0000;">(Failed sending message. Try Again)</span>'
				str+= '<p class="message" style="color:#aaa">' + result.body
				str+= '</p>'
				str+= '<span  class="secondary-content"></span>'
				str+= '</li>'

				$('#conversation-list > li:last-child').after(str)
				$('#conversation-list').scrollTop(99999999)
			}
	};

	// When user reply
	$(document).on('click','#reply',function(){
		if($('#text-message').val().length > 0){
			conn.send(JSON.stringify({
		 		"type" 		: "message",
		 		"user_id"	: user_id,
				"message_id": conversation_id,
				"message" 	: $('#text-message').val()
			}))
			$('#text-message').val('')
		}
	})