$(".messages").animate({ scrollTop: $(document).height() }, "fast");

$("#profile-img").click(function () {
	$("#status-options").toggleClass("active");
});

$(".expand-button").click(function () {
	$("#profile").toggleClass("expanded");
	$("#contacts").toggleClass("expanded");
});

$("#status-options ul li").click(function () {
	$("#profile-img").removeClass();
	$("#status-online").removeClass("active");
	$("#status-away").removeClass("active");
	$("#status-busy").removeClass("active");
	$("#status-offline").removeClass("active");
	$(this).addClass("active");

	if ($("#status-online").hasClass("active")) {
		$("#profile-img").addClass("online");
	} else if ($("#status-away").hasClass("active")) {
		$("#profile-img").addClass("away");
	} else if ($("#status-busy").hasClass("active")) {
		$("#profile-img").addClass("busy");
	} else if ($("#status-offline").hasClass("active")) {
		$("#profile-img").addClass("offline");
	} else {
		$("#profile-img").removeClass();
	};

	$("#status-options").removeClass("active");
});

function newMessage() {
	message = $(".message-input input").val();
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
				//console.log(data);

				selectTop10Friend();
			});
		$('.message-input input').val(null);
	}

	// $('<li class="sent"><img src="./statics/image/user.jpg" alt="" /><p>' + message + '</p></li>').appendTo($('.messages ul'));
	// $('.message-input input').val(null);
	// $('.contact.active .preview').html('<span>You: </span>' + message);
	// $(".messages").animate({ scrollTop: $(document).height() }, "fast");
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
							item += '<img src="./statics/image/user.jpg" alt="" />';
							item += '<p>' + htmlDecode(row.CHAT_CONTENT) + '</p>';
							item += '</li>';
							$(".content .messages ul").append(item);
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
						item += '<img src="./statics/image/user.jpg" alt="" />';
						item += '<p>' + htmlDecode(row.CHAT_CONTENT) + '</p>';
						item += '</li>';
						$(".content .messages ul").append(item);
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
	$("#contacts ul").html("");
	$.post("API.php",
		{
			action: "selectTop10Friend"
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
					}
				} else {
					maxtime = row.USER_ROOM_TIME;
				}

				$('#mintime').val(mintime);
				$('#maxtime').val(maxtime);

				item = '';
				item += '<li class="contact" onclick=contact_onclick(this)>';
				item += '<input type="hidden" class="roomid" value="' + row.ROOM_ID + '">';
				item += '<div class="wrap">';
				item += '<img src="./statics/image/user.jpg" alt="" />';
				item += '<div class="meta">';
				item += '<p class="name">' + row.USER_NAME + '</p>';
				item += '<p class="preview">Last action: ' + row.USER_ROOM_TIME + '</p>';
				item += '</div>';
				item += '</div>';
				item += '</li>';
				$("#contacts ul").append(item);
			});
		});
}

$("#search input").on("input", searchFriend);

function searchFriend() {
	$("#contacts ul").html("");
	s = $("#search input").val();
	$.post("API.php",
		{
			action: "searchFriend",
			s: s
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
					}
				} else {
					maxtime = row.USER_ROOM_TIME;
				}

				$('#mintime').val(mintime);
				$('#maxtime').val(maxtime);

				item = '';
				item += '<li class="contact" onclick=contact_onclick(this)>';
				item += '<input type="hidden" class="roomid" value="' + row.ROOM_ID + '">';
				item += '<div class="wrap">';
				item += '<img src="./statics/image/user.jpg" alt="" />';
				item += '<div class="meta">';
				item += '<p class="name">' + row.USER_NAME + '</p>';
				item += '<p class="preview">Last action: ' + row.USER_ROOM_TIME + '</p>';
				item += '</div>';
				item += '</div>';
				item += '</li>';
				$("#contacts ul").append(item);
			});
		});
}

function selectTop10Chat() {
	$(".content .messages ul").html("");
	userid = $("#userid").val();
	roomid = $("#roomid").val();
	$.post("API.php",
		{
			action: "selectTop10Chat",
			roomid: roomid
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
				item += '<img src="./statics/image/user.jpg" alt="" />';
				item += '<p>' + htmlDecode(row.CHAT_CONTENT) + '</p>';
				item += '</li>';
				$(".content .messages ul").prepend(item);
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
	contactname = $(elmnt).find(".name").html();
	$("#roomid").val(roomid);
	$("#contacts ul").children().removeClass("active");
	$(elmnt).addClass("active");
	$(".content .contact-profile p").html(contactname);
	selectTop10Chat();
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