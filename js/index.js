$(".messages").animate({ scrollTop: $(document).height() }, 
"fast");

$("#profile-img").click(function () {
	$(".contact-item").toggleClass("active");
});

$(".expand-button").click(function () {
	$("#profile").toggleClass("expanded");
});

function newMessage() {
	message = $("#message-input .message-input-inner input").val();
	if ($.trim(message) == '') {
		return false;
	}
	roomid = $("#roomid").val(); console.log(message); console.log(roomid);
	if (roomid > -1) {
		$.post("API.php",
			{
				action: "addChat",
				roomid: roomid,
				chat: message
			},
			function (data) {
				selectTop10Friend();
			});
		$('#message-input .message-input-inner input').val(null);
	}
};

$('.submit').click(function () {
	newMessage();
});

$(window).on('keydown', function (e) {
	if (e.which == 13) {
		newMessage();
		return false;
	}
});

function htmlEncode(value) {
	return $('<div/>').text(value).html();
}

function htmlDecode(value) {
	return $('<div/>').html(value).text();
}

$(window).on('load', onload);

function onload() {
	selectTop10Friend();
	var friendInterval = setInterval(updateFriend, 1000);
	var chatInterval = null;
}

function updateChat() {
	roomid = $("#roomid").val();
	maxchatid = $('#maxchatid').val();

	if (roomid > - 1) {
		$.post("API.php",
			{
				action: "updateChat",
				roomid: roomid,
				maxchatid: maxchatid
			},
			function (data) {
				//console.log(data);
				data.forEach(row => {
					minchatid = $("#minchatid").val();
					maxchatid = $("#maxchatid").val();
					if (minchatid != -1) {
						if (minchatid > row.CHAT_ID) {
							minchatid = row.CHAT_ID;
						}
					} else {
						minchatid = row.CHAT_ID;
					}

					if (maxchatid != -1) {
						if (maxchatid < row.CHAT_ID) {
							li_class = "";
							if (userid == row.USER_ID) {
								li_class = "sent";
							} else {
								li_class = "replies";
							}
							item = '';
							item += '<li class="' + li_class + '">'
							item += '<img src="image/uploads/' + row.USER_AVT + '" alt="avt" class="img-inner" />';
							item += '<div class="details">';
							item += '<div class="info">';
							item += '<p class="user_name">' + htmlDecode(row.USER_NAME) + '</p>';
							item += '<p class="time">' + htmlDecode(row.CHAT_TIME) + '</p>';
							item += '</div>';
							item += '<p class="text">' + htmlDecode(row.CHAT_CONTENT) + '</p>';
							item += '</div>';
							item += '</li>';
							$("#content #messages ul").append(item);
							maxchatid = row.CHAT_ID;
						}
					} else {
						maxchatid = row.CHAT_ID;
						li_class = "";
						if (userid == row.USER_ID) {
							li_class = "sent";
						} else {
							li_class = "replies";
						}
						item = '';
						item += '<li class="' + li_class + '">'
						item += '<img src="image/uploads/' + row.USER_AVT + '" alt="avt" class="img-inner" />';
						item += '<div class="details">';
						item += '<div class="info">';
						item += '<p class="user_name">' + htmlDecode(row.USER_NAME) + '</p>';
						item += '<p class="time">' + htmlDecode(row.CHAT_TIME) + '</p>';
						item += '</div>';
						item += '<p class="text">' + htmlDecode(row.CHAT_CONTENT) + '</p>';
						item += '</div>';
						item += '</li>';
						$("#content #messages ul").append(item);
						maxchatid = row.CHAT_ID;
					}

					$('#minchatid').val(minchatid);
					$('#maxchatid').val(maxchatid);

				});
			});
	}
}

function updateFriend() {
	maxtime = $("#maxtime").val();
	if (maxtime != " ") {
		$.post("API.php",
			{
				action: "updateFriend",
				maxtime: maxtime
			},
			function (data) {
				//console.log(data);
				data.forEach(row => {
					mintime = $("#mintime").val();
					maxtime = $("#maxtime").val();
					if (mintime != " ") {
						a = new Date(mintime).getTime();
						b = new Date(row.USER_ROOM_TIME).getTime();
						if (a > b) {
							mintime = row.USER_ROOM_TIME;
						}
					} else {
						mintime = row.USER_ROOM_TIME;
					}

					if (maxtime != " ") {
						a = new Date(maxtime).getTime();
						b = new Date(row.USER_ROOM_TIME).getTime();
						if (a < b) {
							maxtime = row.USER_ROOM_TIME;
							selectTop10Friend()
						}
					} else {
						maxtime = row.USER_ROOM_TIME;
						selectTop10Friend()
					}

					$('#mintime').val(mintime);
					$('#maxtime').val(maxtime);

				});
			});
	}


}

function selectTop10Friend() {
	$("#contacts .contact-inner ul").html("");
	$.post("API.php",
		{
			action: "selectTop10Friend"
		},
		function (data) {
			// console.log(data);
			data.forEach(row => {
				mintime = $("#mintime").val();
				maxtime = $("#maxtime").val();
				if (mintime != " ") {
					a = new Date(mintime).getTime();
					b = new Date(row.USER_ROOM_TIME).getTime();
					if (a > b) {
						mintime = row.USER_ROOM_TIME;
					}
				} else {
					mintime = row.USER_ROOM_TIME;
				}
				

				if (maxtime != " ") {
					a = new Date(maxtime).getTime();
					b = new Date(row.USER_ROOM_TIME).getTime();
					if (a < b) {
						maxtime = row.USER_ROOM_TIME;
					}
				} else {
					maxtime = row.USER_ROOM_TIME;
				}

				room_time = row.USER_ROOM_TIME.split(' ');
				date = room_time[0].split('-');
				time = room_time[1].split(':');

				var status = 'offline';
				var system_date = new Date();
				
				function get_value(n) {
					return String("00" + n).slice(-2);
				}

				var system_time = `${system_date.getFullYear()}\-${get_value(system_date.getMonth() + 1)}\-${get_value(system_date.getDate())}`;
				if (system_time === room_time[0]) {
					if ((parseInt(system_date.getHours()) * 60 + parseInt(system_date.getMinutes())) - (parseInt(time[0]) * 60 + parseInt(time[1])) <= 30) {
						status = 'online';
					}
				}

				$('#mintime').val(mintime);
				$('#maxtime').val(maxtime);


				item = '';
				item += '<li class="contact-item" onclick=contact_onclick(this)>';
				item += '<input type="hidden" class="roomid" value="' + row.ROOM_ID + '" />';
				item += '<input type="hidden" class="user_id" value="' + row.USER_ID + '" />';
				item += '<input type="hidden" class="user_avt_' + row.USER_ID + '" value="' + row.USER_AVT + '" />';
				item += '<div class="' + status + '">';
				item += '<img src="image/uploads/' + row.USER_AVT + '" alt="user" />';
				item += '</div>';
				item += '<div class="contact-info">';
				item += '<p class="name">' + row.USER_NAME + '</p>';
				item += '<p class="preview">Last action: ' + row.USER_ROOM_TIME + '</p>';
				item += '</div>';
				item += '</li>';
				$("#contacts .contact-inner ul").append(item);
			});
		});
}

$("#search .search-inner input").on("input", searchFriend);

function searchFriend() {
	$("#contacts .contact-inner ul").html("");
	s = $("#search .search-inner input").val();
	$.post("API.php",
		{
			action: "searchFriend",
			s: s
		},
		function (data) {
			// console.log(data);
			data.forEach(row => {
				mintime = $("#mintime").val();
				maxtime = $("#maxtime").val();
				if (mintime != " ") {
					a = new Date(mintime).getTime();
					b = new Date(row.USER_ROOM_TIME).getTime();
					if (a > b) {
						mintime = row.USER_ROOM_TIME;
					}
				} else {
					mintime = row.USER_ROOM_TIME;
				}

				if (maxtime != " ") {
					a = new Date(maxtime).getTime();
					b = new Date(row.USER_ROOM_TIME).getTime();
					if (a < b) {
						maxtime = row.USER_ROOM_TIME;
					}
				} else {
					maxtime = row.USER_ROOM_TIME;
				}

				$('#mintime').val(mintime);
				$('#maxtime').val(maxtime);

				item = '';
				item += '<li class="contact-item" onclick=contact_onclick(this)>';
				item += '<input type="hidden" class="user_id" value="' + row.USER_ID + '" />';
				item += '<input type="hidden" class="user_avt_' + row.USER_ID + '" value="' + row.USER_AVT + '" />';
				item += '<div class="online">';
				item += '<img src="image/uploads/' + row.USER_AVT + '" alt="user" />';
				item += '</div>';
				item += '<div class="contact-info">';
				item += '<p class="name">' + row.USER_NAME + '</p>';
				item += '<p class="preview">Last action: ' + row.USER_ROOM_TIME + '</p>';
				item += '</div>';
				item += '</li>';
				$("#contacts .contact-inner ul").append(item);
			});
		});
}

function selectTop10Chat(name_user, avt_user) {
	$("#content #messages ul").html("");
	item = '';
	item += '<li class="information">';
	item += '<img src="image/uploads/' + avt_user + '" class="img-inner" />';
	item += '<div class="description">';
	item += '<h2>' + name_user + '</h2>';
	item += '<p>This is the beginning of your direct message history with <span>@' + name_user + '</p>';
	item += '</div>';
	item += '</li>';
	$("#content #messages ul").prepend(item);

	userid = $("#userid").val();
	roomid = $("#roomid").val();
	$.post("API.php",
		{
			action: "selectTop10Chat",
			roomid: roomid
		},
		function (data) {
			console.log(data.reverse());
			data.forEach(row => {
				minchatid = $("#minchatid").val();
				maxchatid = $("#maxchatid").val();
				if (minchatid != -1) {
					if (minchatid > row.CHAT_ID) {
						minchatid = row.CHAT_ID;
					}
				} else {
					minchatid = row.CHAT_ID;
				}

				if (maxchatid != -1) {
					if (maxchatid < row.CHAT_ID) {
						maxchatid = row.CHAT_ID;
					}
				} else {
					maxchatid = row.CHAT_ID;
				}

				$('#minchatid').val(minchatid);
				$('#maxchatid').val(maxchatid);
				li_class = "";
				if (userid == row.USER_ID) {
					li_class = "sent";
				} else {
					li_class = "replies";
				}
				item = '';
				item += '<li class="' + li_class + '">'
				item += '<img src="image/uploads/' + row.USER_AVT + '" alt="avt" class="img-inner" />';
				item += '<div class="details">';
				item += '<div class="info">';
				item += '<p class="user_name">' + row.USER_NAME + '</p>';
				item += '<p class="time">' + htmlDecode(row.CHAT_TIME) + '</p>';
				item += '</div>';
				item += '<p class="text">' + htmlDecode(row.CHAT_CONTENT) + '</p>';
				item += '</div>';
				item += '</li>';
				$("#content #messages ul").append(item);
			});
		});
}

function getSession() {
	$.post("getSession.php",
		{
			action: "getSession"
		},
		function (data) {
			//console.log(data);
		});
}

function contact_onclick(elmnt) {
	roomid = $(elmnt).find(".roomid").val();
	user_id = $(elmnt).find(".user_id").val();
	user_avt = $(elmnt).find(".user_avt_" + user_id).val();
	contactname = $(elmnt).find(".name").html();
	contactname_item = '<span>@</span>' + contactname;
	$("#roomid").val(roomid);
	$("#contacts .contact-inner ul").children().removeClass("active");
	$(elmnt).addClass("active");
	$("#content #contact-profile .contact-profile-inner p.name").removeClass("none-el");
	$("#content #contact-profile .contact-profile-inner p.name").html(contactname_item);
	$("#content #messages .message-bg").addClass("none-el");
	$("#content #messages ul .messages-inner li.information").removeClass("none-el");
	$("#content #message-input").removeClass("none-el");
	selectTop10Chat(contactname, user_avt);
	chatInterval = setInterval(updateChat, 1000);
}

$("#addcontact").on("click", function () {
	friendid = $("#friendid").val();
	$.post("API.php",
		{
			action: "addFriend",
			friendid: friendid
		},
		function (data) {
			//console.log(data);
			selectTop10Friend();
			$("#friendid").val("");
		});
});