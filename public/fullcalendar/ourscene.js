$(document).ready(function() {

	const EVENT_COLOR = '#3E82F7';
	const PROMOTION_COLOR = '#ECB500';
	const PENDING_COLOR = '#9A9A9A';

	// Initialize private calender

	$('#public-calendar').fullCalendar({
		// put your options and callbacks here

		defaultView: 'agendaWeek',
		header: {
			left: 'prev,next today',
			center: 'title'
		},
		eventLimitText: 'event',
		select: function(start, end) {
				
			$("#event-form").append('<input type="hidden" name="start_datetime" value="'+start+'"/>');
			$("#event-form").append('<input type="hidden" name="end_datetime" value="'+end+'"/>');
			$("#event-form").submit();
			$('#calendar').fullCalendar('unselect');
		},
		eventLimit: true,
		events: function (start, end, timezone, callback){
			// if artist
			var user_id = document.getElementById('public-calendar').getAttribute('user-id')
			$.ajax({
				url: EVENT_ROOT+'/ajax-fetch-public-events-by-user-id',
				type: "POST",
				data: "_token="+CSRF_TOKEN+"&user_id="+user_id,
				success: function(data){
					
					var calendar_events = []; //container of events and promotions
					
					var json_data = JSON.parse(data);

					//get events
					var events = json_data['events'];
					
					//get promotions
					var promotions = json_data['promotions'];

					//check if calendar events exist
					if (events.length + promotions.length > 0){
						$('#alert-public-calendar-events').css("display", "none");
					}

					//process events

					for (var index in events){
						
						var event = events[index];
						
						//format event details

						var title = event['title'];
						var cover_charge = event['cover_charge'];
						var cover_charge_access = event['cover_charge_access'];
						var venue_id = event['venue_id'];
						var venue_name = event['venue_name'];
						var equipments = event['equipments'];
						var age_requirements = event['age_requirements'];
						var type = event['event_type'];

						var start_datetime = event['start_datetime']['sec'];
						var start_time = moment().startOf('day').seconds(start_datetime).format('HH:mm:ss');
						var start_date = moment(start_datetime*1000).format('YYYY-MM-DD');
						start_datetime = start_date + 'T' + start_time;

						var end_datetime = event['end_datetime']['sec'];
						var end_time = moment().startOf('day').seconds(end_datetime).format('HH:mm:ss');
						var end_date = moment(end_datetime*1000).format('YYYY-MM-DD');
						end_datetime = end_date + 'T' + end_time;

						var opening_datetime = event['opening_time'];
						var opening_time = moment().startOf('day').seconds(opening_datetime).format('HH:mm:ss');
						var description = event['description'];

						//add event to calendar events

						calendar_events.push({
								title: title,
								age_requirements: age_requirements,
								type: type,
								start: start_datetime,
								end: end_datetime,
								id: event['id'],
								s_time: start_time,
								s_date: start_date,
								e_time: end_time,
								e_date: end_date,
								opening_time: opening_time,
								description: description,
								cover_charge: cover_charge,
								cover_charge_access: cover_charge_access,
								venue_id: venue_id,
								venue_name: venue_name,
								status: status,
								equipments: equipments,
								color: EVENT_COLOR
						});
					}

					//process promotions

					for (var index in promotions){

						var promotion = promotions[index];

						//format promotion details

						var title = promotion['title'];
						var age_requirements = promotion['age_requirements'];
						var type = promotion['event_type'];

						var start_datetime = promotion['start_datetime']['sec'];
						var start_time = moment().startOf('day').seconds(start_datetime).format('HH:mm:ss');
						var start_date = moment.utc(start_datetime*1000).format('YYYY-MM-DD');
						start_datetime = start_date + 'T' + start_time;

						var end_datetime = promotion['end_datetime']['sec'];
						var end_time = moment().startOf('day').seconds(end_datetime).format('HH:mm:ss');
						var end_date = moment.utc(end_datetime*1000).format('YYYY-MM-DD');
						end_datetime = end_date + 'T' + end_time;

						var opening_datetime = promotion['opening_time'];
						var opening_time = moment().startOf('day').seconds(opening_datetime).format('HH:mm:ss');
						var description = promotion['description'];

						//add promotion to calendar events

						calendar_events.push({
								classification: 'promotion',
								title: title,
								age_requirements: age_requirements,
								type: type,
								start: start_datetime,
								end: end_datetime,
								id: promotion['id'],
								s_timestamp: start_datetime,
								s_time: start_time,
								s_date: start_date,
								e_time: end_time,
								e_date: end_date,
								status: status,
								opening_time: opening_time,
								description: description,
								cover_charge_access: cover_charge_access,
								cover_charge: cover_charge,
								venue_id: venue_id,
								venue_name: venue_name,
								equipments: equipments,
								user_id: user_id,
								color: PROMOTION_COLOR
						});

					}

					callback(calendar_events);
				}
			});
		},
		eventClick: function(calEvent, jsEvent, view){

			// Check calendar event classification

			// Promotion: Redirect to promotion details page

			if(calEvent.classification == 'promotion'){
				window.location.href = PROMOTION_ROOT+"/"+calEvent.id;
				return;
			}
			
			// Event: View event modal
			
			document.getElementById('myModalLabel').innerHTML = calEvent.title;
			document.getElementById('view_age_restrictions').innerHTML = calEvent.age_requirements;
			document.getElementById('view_event_type').innerHTML = calEvent.type;
			document.getElementById('view_event_start_time').innerHTML = calEvent.s_time;
			document.getElementById('view_event_start_date').innerHTML = moment(calEvent.s_date).format('MMMM D, YYYY');
			document.getElementById('view_event_end_time').innerHTML = calEvent.e_time;
			document.getElementById('view_event_end_date').innerHTML = moment(calEvent.e_date).format('MMMM D, YYYY');
			document.getElementById('view_event_venue').innerHTML = calEvent.venue_name;
			document.getElementById('view_event_cover_charge').innerHTML = calEvent.cover_charge;
			document.getElementById('view_event_opening_time').innerHTML = calEvent.opening_time;
			document.getElementById('view_event_description').innerHTML = calEvent.description;
			document.getElementById('cover-charge-event-id').setAttribute('value', calEvent.id);

			$('#equipment-table-body').empty();
			$('#equipment-table-body').append("<tr><th>Name</th><th>Owner</th><th>Quantity</th><th>Inclusions</th><th>Options</th>");

			for (equipment_index in calEvent.equipments){
				var equipment_holder = calEvent.equipments[equipment_index];
				var equipment = equipment_holder['equipment'];
				var equipment_name = "<td>"+equipment['name']+"</td>";
				var equipment_owner = "<td>"+equipment['owner']+"</td>";
				var equipment_qty = "<td>"+equipment['qty']+"</td>";
				var inclusion = "<td>";
				for (inclusion_index in equipment['inclusion']){
					var inclusions = equipment['inclusion'][inclusion_index];
					inclusion = inclusion + "<p>"+inclusions+"</p></br>";
				}
				inclusion = inclusion + "</td>";
				var equipment_element = '<tr class="equipment-row">' + equipment_name + equipment_owner + equipment_qty + inclusion + "</tr>";
				$('#equipment-table-body').append(equipment_element);
			}
			
			if (calEvent.cover_charge_access){
				$('#update-cover-charge-button').css('display', 'block');
				document.getElementById('view_event_cover_charge').removeAttribute("disabled");
			}

			$.ajax({
				url: SERVICE_ROOT+'/ajax-fetch-public-service-by-events-id',
				method: 'POST',
				data: "_token="+CSRF_TOKEN+"&id="+calEvent.id,
				success: function(data){

					$('#view-event-service-table-body').empty();
					$('#view-event-service-table-body').append('<tr><th>Artist Name</th><th>Time</th><th>Price</th><th></th></tr>');
					$('#view-event-service-table-body').css('display', 'none');

					for (var service_index in data){
						$('#view-event-service-table-body').css('display', 'block');
						var service = data[service_index];

						var start_datetime = service['start_datetime']['sec'];
						var start_time = moment().startOf('day').seconds(start_datetime).format('HH:mm:ss');
						var end_datetime = service['end_datetime']['sec'];
						var end_time = moment().startOf('day').seconds(end_datetime).format('HH:mm:ss');

						$('#view-event-service-table-body').append('<tr id="'+service['_id']+'"class="service-row"><td class="artist_name" value="'+service['artist_name']+'">' + service['artist_name'] + '</td><td> From <p class="start_time" value="'+start_datetime+'">' + start_time + '</p> to <p class="end_time" value="'+end_datetime+'">' + end_time + '</p></td></tr>');

					}
				}
			});

			$('#modal-viewevent').modal();
			document.getElementById('request_performance_btn').onclick = function(){requestPerformance(calEvent.id);};

		}
	});

	$('#modal-alert').on('hidden.bs.modal', function () {
    	
	})

	$('#update-cover-charge-form').submit(function(e){
		e.preventDefault();
		$.ajax({
			url: EVENT_ROOT+'/ajax-update-cover-charge',
			method: 'POST',
			data: $('#update-cover-charge-form').serialize(),
			success: function(data){
				if (data['invalid'] == 'true'){
					document.getElementById('alert-message').innerHTML='Sorry, unable to change cover charge!';
				} else {
					document.getElementById('alert-message').innerHTML='Success';

					$('#modal-alert').on('hidden.bs.modal', function () {
    					location.reload();
					})
				}
				$('#modal-alert').modal();

			}
		});
		
	});

	$('#create-service-form').submit(function(e){

		e.preventDefault();
		$.ajax({
			url: SERVICE_ROOT+'/ajax-create-service',
			type: "POST",
			data: $('#create-service-form').serialize(),
			success: function (data) {
				if(data['error']){

					//show error
					document.getElementById('alert-message').innerHTML= data['error'];
					$('#modal-alert').modal();
				}
				else{
					//show success
					$('#modal-newservice').modal('hide');
					var artist_name = data['artist_name'];
					var start_datetime = (data['start_datetime']['sec']);
					var start_time = moment().startOf('day').seconds(start_datetime).format('HH:mm:ss');
					var end_datetime = (data['end_datetime']['sec']);
					var end_time = moment().startOf('day').seconds(end_datetime).format('HH:mm:ss');
					var price = data['price'];
					var service_id = data['_id'];

					var remove_option = '<a value="'+service_id+'" onclick="removeService(this)">Remove</a>';
					var change_option = '<a data-toggle="modal" data-target="#modal-updateservice" value="'+service_id+'" onclick="updateService(this)">Change</a>';
					var option_closing_tags = '<input type="hidden" name="services[]" value="'+service_id+'">';
					var options = change_option + remove_option + option_closing_tags;

					$('#service-table-body').append('<tr id="'+service_id+'"class="service-row"><td class="artist_name" value="'+artist_name+'">' + artist_name + '</td><td> From <p class="start_time" value="'+start_datetime+'">' + start_time + '</p> to <p class="end_time" value="'+end_datetime+'">' + end_time + '</p></td><td class="price" value="'+price+'">' + price + '</td><td>'+ options +'</td></tr>');					
					if ($('#service-table-body').css('display') == 'none'){
						$('#service-table-body').css('display', 'block');
					}
					document.getElementById('alert-message').innerHTML='Success!';
					$('#modal-alert').modal();
				}
			},
			error: function (data){
				//show error
			}
		});
	});

	$('#request-performance-form').submit(function(e){

		e.preventDefault();
		$.ajax({
			url: SERVICE_ROOT+'/ajax-request-performance',
			type: "POST",
			data: $('#request-performance-form').serialize(),
			success: function (data) {
				if(data['error']){

					//show error
				}
				else{
					//show success
					document.getElementById('alert-message').innerHTML='Success!';
					$('#modal-alert').modal();
					$('#modal-requestperformance').modal('hide');

				}
			},
			error: function (data){
				//show error
			}
		});
	});

	$('#update-service-form').submit(function(e){
		e.preventDefault();
		$.ajax({
			url: SERVICE_ROOT+'/ajax-update-service',
			type: "POST",
			data: $('#update-service-form').serialize(),
			success: function (data) {
				if(data['error']){

					//show error
				}
				else{
					//show success
					$('#modal-updateservice').modal('hide');
					var artist_name = data['artist_name'];
					var start_datetime = (data['start_datetime']['sec']);
					var start_time = moment().startOf('day').seconds(start_datetime).format('HH:mm');
					var end_datetime = (data['end_datetime']['sec']);
					var end_time = moment().startOf('day').seconds(end_datetime).format('HH:mm');
					var price = data['price'];
					var service_id = data['_id'];

					var node_for_deletion = document.getElementById(service_id);
					node_for_deletion.parentNode.removeChild(node_for_deletion);

					var remove_option = '<a value="'+service_id+'" onclick="removeService(this)">Remove</a>';
					var change_option = '<a data-toggle="modal" data-target="#modal-updateservice" value="'+service_id+'" onclick="updateService(this)">Change</a>';
					var option_closing_tags = '<input type="hidden" name="services[]" value="'+service_id+'">';
					var options = change_option + remove_option + option_closing_tags;

					$('#service-table-body').append('<tr id="'+service_id+'"class="service-row"><td class="artist_name" value="'+artist_name+'">' + artist_name + '</td><td> From <p class="start_time" value="'+start_datetime+'">' + start_time + '</p> to <p class="end_time" value="'+end_datetime+'">' + end_time + '</p></td><td class="price" value="'+price+'">' + price + '</td><td>'+ options +'</td></tr>');
				}
			},
			error: function (data){
				//show error
			}
		});
	});

	$('#add-equipments-form').submit(function(e){
		e.preventDefault();
		$.ajax({
			url: EVENT_ROOT+'/ajax-add-equipment',
			method: 'POST',
			data: $('#add-equipments-form').serialize(),
			success: function(data){
				document.getElementById('alert-message').innerHTML='Success';
				$('#modal-alert').modal();
				$('#modal-alert').on('hidden.bs.modal', function () {
    					location.reload();
				});
				$('#modal-service-equipment').modal('hide');
			},
			error: function(data){
				document.getElementById('alert-message').innerHTML='Sorry, unable to save equipment!';
				$('#modal-alert').modal();
				$('#modal-service-equipment').modal('hide');
				$('#modal-viewevent').modal('hide');

			}
		});
	});

	$('#create-event-form').submit(function(e){
		e.preventDefault();
		$.ajax({
			url: EVENT_ROOT+'/ajax-create-event',
			type: "POST",
			data: $('#create-event-form').serialize(),
			success: function(data){

				if (data['success']){
					document.getElementById('alert-message').innerHTML = data['success'];

					window.open(data['redirect_url'], "GC",  "height=600,width=400");
					$('#modal-alert').modal();	
					$('#modal-alert').on('hidden.bs.modal', function () {
    					window.location=ROOT;
					})
					
				} else {
					document.getElementById('alert-message').innerHTML = data['error'];
					$('#modal-alert').modal();	
				}

				// Insert Google Calendar create event here

			},
			error: function(data){
				alert("error");
			}
		});
	});

	$('#promotion-start').datetimepicker({
		format: 'YYYY-MM-DD hh:mm:ss A',
		minDate: moment(new Date()).startOf('day')
	});

	$('#promotion-end').datetimepicker({
		format: 'YYYY-MM-DD hh:mm:ss A',
		minDate: moment(new Date()).startOf('day')
	});

	$('#door-opening-time').datetimepicker({
		format: 'YYYY-MM-DD hh:mm:ss A',
		minDate: moment(new Date()).startOf('day')
	});

	$('#add-service-equipment-button').on('click', function(){
		var e = document.getElementById("service-equipment-selector");
		var equipment_id = e.value;
		$.ajax({
			url: EQUIPMENT_ROOT+'/ajax-fetch-equipment-by-id',
			type: "POST",
			data: '_token='+CSRF_TOKEN+"&id="+equipment_id,
			success: function(data){

				var equipment_owner = data['owner'];
				var equipment_user_id = data['user_id'];
				var equipment_name = data['name'];
				var equipment_qty = data['qty'];
				var equipment_inclusions = data['inclusion'];

				var row_eq_name = '<td>'+equipment_name+'</td>';
				var row_eq_owner = '<td>'+equipment_owner+'</td>';
				var row_eq_qty = '<td>'+equipment_qty+'</td>'
				var row_inclusions = '<td>';
				for (var index in equipment_inclusions){
					var inclusion = equipment_inclusions[index];
					row_inclusions = row_inclusions + '<p>'+inclusion+'</p><br>';
				}
				row_inclusions = row_inclusions + '</td>';

				var row_remove_btn_with_identifier = '<td><button type="button" onclick="removeNewServiceEquipment(this)">Remove</button><input type="hidden" name="equipments[]" value="'+equipment_id+'"></td>';
				var row_equipment = '<tr class="service-equipment-row">'+row_eq_name+row_eq_owner+row_eq_qty+row_inclusions+row_remove_btn_with_identifier+'</tr>';

				$('#service-equipment-table-body').append(row_equipment);
			}
		});	
	});

    $("#venue-selector").on('focusout',function() {
        var val = $('#venue-selector').val()
        var id = $('#venues option').filter(function() {
            return this.value == val;
        }).data('id');
        if (id)
        	document.getElementById('event_venue_id').setAttribute('value', id);
        else
        	document.getElementById('event_venue_id').setAttribute('value', 'null');
    });

    $("#event-start-time").on('focusout',function() {

        var new_start_time = $('#event-start-time').val()
        document.getElementById('event-opening-time').setAttribute('max', new_start_time);
        
    });

});


function removeNewServiceEquipment(node){
	$(node).closest('.service-equipment-row').remove();	
}

function removeService(node){

	var service_id = node.getAttribute('value');

	$.ajax({
		url: SERVICE_ROOT+'/ajax-delete-service',
		type: "POST",
		data: 'service_id='+service_id+"&_token="+CSRF_TOKEN,
		success: function(data){
			$(node).closest('.service-row').remove();

			if (!$('#service-table-body').find('.service-row').length){
				$('#service-table-body').css('display', 'none');
			}

		}
	});

}

function updateService(node){

	var service_id = node.getAttribute('value');

	var data = "_id=" + service_id + "&_token=" + CSRF_TOKEN;
	$.ajax({
		url: SERVICE_ROOT+'/ajax-service-lookup',
		type: 'POST',
		data: data,
		success: function (data) {
				if(data['error']){

					//show error
				}
				else{
					//show success
					var start_datetime = data['start_datetime']['sec'];
					var start_time = moment().startOf('day').seconds(start_datetime).format('HH:mm:ss');
					var start_date = moment(start_datetime*1000).format('YYYY-MM-DD')

					var end_datetime = data['end_datetime']['sec'];
					var end_time = moment().startOf('day').seconds(end_datetime).format('HH:mm:ss');
					var end_date = moment(end_datetime*1000).format('YYYY-MM-DD')

					$('#update-service-name').val(data['artist_name']);

					document.getElementById('update-service-startdate').setAttribute('value', start_date);
					document.getElementById('update-service-starttime').setAttribute('value', start_time);
					document.getElementById('update-service-enddate').setAttribute('value', end_date);
					document.getElementById('update-service-endtime').setAttribute('value', end_time);
					document.getElementById('update-service-price').setAttribute('value', data['price']);
					document.getElementById('update-service-id').setAttribute('value', service_id);

				}
				return data;
			},
			error: function (data){
				//show error

				return data;
			}
	});
}

function serviceLookup(id){

	var data = "_id=" + id + "&_token=" + CSRF_TOKEN;
	$.ajax({
		url: SERVICE_ROOT+'/ajax-service-lookup',
		type: 'POST',
		data: data,
		success: function (data) {
				if(data['error']){

					//show error

				}
				else{
					//show success
				}
				return data;
			},
			error: function (data){
				//show error

				return data;
			}
	});

}


function confirmService(node){

	var service_id = node.getAttribute('value');

	$.ajax({
		url: SERVICE_ROOT+'/ajax-confirm-service',
		type: 'POST',
		data: '_token='+CSRF_TOKEN+'&id='+service_id,
		success: function(data){
			document.getElementById('alert-message').innerHTML='Service Confirmed!';
			$('#modal-alert').modal();
			$('#modal-viewevent').modal('hide');
		}
	});
}

function confirmEvent(event){
	var event_id = event.id;

	$.ajax({
		url: EVENT_ROOT+'/ajax-confirm-event',
		type: 'POST',
		data: '_token='+CSRF_TOKEN+'&id='+event_id,
		success: function(data){
			document.getElementById('alert-message').innerHTML='Event Confirmed!';
			$('#modal-alert').modal();
			$('#modal-viewevent').modal('hide');

			event.confirm_date = data['confirm_date'];
			$('#private-calendar').fullCalendar('updateEvent', event);
		}

	});
}

function updateEvent(event){
	var event_id = event.id;

	$.ajax({
		url: EVENT_ROOT+'/ajax-fetch-event-data-by-id',
		type: 'POST',
		data: '_token='+CSRF_TOKEN+'&id='+event_id,
		success: function(data){

			$('#update-event-form').append('<input type="hidden" id="event-data" name="event_data" value=\''+data+'\'>');
			$('#update-event-form').submit();
		}

	});
}

function deleteEvent(event){
	var event_id = event.id;
	$('#modal-confirmation').modal();
	document.getElementById('confirm-button').onclick = function(){
		$.ajax({
			url: EVENT_ROOT+'/ajax-delete-event',
			type: 'POST',
			data: '_token='+CSRF_TOKEN+'&id='+event_id,
			success: function(data){
				document.getElementById('alert-message').innerHTML='Event Deleted!';
				$('#modal-alert').modal();
				$('#modal-viewevent').modal('hide');
				$('#private-calendar').fullCalendar('removeEvents', event.id);
				$('#modal-confirmation').modal('hide');
			}
		});
	}
	document.getElementById('cancel-button').onclick = function(){	$('#modal-confirmation').modal('hide');}
}

function requestPerformance(id){

	var start_time = document.getElementById('view_event_start_time').innerHTML;
	var start_date = document.getElementById('view_event_start_date').innerHTML;
	var end_time = document.getElementById('view_event_end_time').innerHTML;
	var end_date = document.getElementById('view_event_end_date').innerHTML;

	document.getElementById('performance_event_id').setAttribute('value', id);

	document.getElementById('performance_start_time').setAttribute('value', start_time);
	document.getElementById('performance_start_date').setAttribute('value', start_date);
	document.getElementById('performance_start_date').setAttribute('min', start_date);
	document.getElementById('performance_start_date').setAttribute('max', end_date);

	document.getElementById('performance_end_time').setAttribute('value', end_time);
	document.getElementById('performance_end_date').setAttribute('value', end_date);
	document.getElementById('performance_end_date').setAttribute('min', start_date);
	document.getElementById('performance_end_date').setAttribute('max', end_date);

	$('#modal-requestperformance').modal();
}

function makeDefaultProfilePic(image){

	image.onerror = "";
    image.src = "/profile-pictures/blank-profile-picture-973460_960_720.png";
    
    return true;

}

function removeEquipmentFromEvent(equipment_id, event_id){
	$.ajax({
				url: EVENT_ROOT+'/ajax-remove-equipment-from-event',
				type: "POST",
				data: "_token="+CSRF_TOKEN+'&event_id='+event_id+'&equipment_id='+equipment_id,
				success: function(data){
					document.getElementById('alert-message').innerHTML='Deleted!';
					$('#modal-alert').modal();
					
				}
			});
}

function addtoGroupChat(user){
	var user_id_input = '<input type="hidden" name="participants[]" value="'+user.id+'">';
	var user_name_display = '<p>'+user.name+'</p>';
	var remove_option = '<button class="btn btn-primary" onclick="this.closest(\'.group-chat-entry\').remove()">Remove</button';
	$('#list-group-chat').append('<span class="group-chat-entry">' + user_id_input + user_name_display + remove_option +'</span>');
}








