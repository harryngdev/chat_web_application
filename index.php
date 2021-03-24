<?php
require_once('config.php');

if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) {
	header('location: auth.php');
}
header('Content-Type: text/html; charset="UTF-8"');
?>
<!doctype html>
<html lang="vi" charset>
	<head>
		<title>Chat room</title>
		<meta charset="utf-8" />
	</head>
	<body>
		<div id="frame">
			<div id="sidepanel">
				<div id="profile">
					<div class="wrap">
						<input type="hidden" id="userid" value="<?php echo $_SESSION['userid'] ?>">
						<img src="./statics/image/user.jpg" id="profile-img src="./statics/image/user.jpg"" class="online" alt="" />
						<p><?php echo $_SESSION['username'] ?></p>
						<i class="fa fa-chevron-down expand-button" aria-hidden="true"></i>
						<div id="expanded">
							<label for="id">User id: <?php echo $_SESSION['userid'] ?></i></label>
						</div>
					</div>
				</div>
				<div id="search">
					<label for=""><i class="fa fa-search" aria-hidden="true"></i></label>
					<input type="text" placeholder="Search contacts..." />
				</div>
				<div id="contacts">
					<input type="hidden" id="mintime" value=" ">
					<input type="hidden" id="maxtime" value=" ">
					<ul>
						<!-- <li class="contact">
							<div class="wrap">
								<span class="contact-status online"></span>
								<img src="./statics/image/user.jpg" alt="" />
								<div class="meta">
									<p class="name">Louis Litt</p>
									<p class="preview">You just got LITT up, Mike.</p>
								</div>
							</div>
						</li>
						<li class="contact active">
							<div class="wrap">
								<span class="contact-status busy"></span>
								<img src="./statics/image/user.jpg" alt="" />
								<div class="meta">
									<p class="name">Harvey Specter</p>
									<p class="preview">Wrong. You take the gun, or you pull out a bigger one. Or, you call their bluff. Or, you do any one of a hundred and forty six other things.</p>
								</div>
							</div>
						</li>
						<li class="contact">
							<div class="wrap">
								<span class="contact-status away"></span>
								<img src="./statics/image/user.jpg" alt="" />
								<div class="meta">
									<p class="name">Rachel Zane</p>
									<p class="preview">I was thinking that we could have chicken tonight, sounds good?</p>
								</div>
							</div>
						</li>
						<li class="contact">
							<div class="wrap">
								<span class="contact-status online"></span>
								<img src="./statics/image/user.jpg" alt="" />
								<div class="meta">
									<p class="name">Donna Paulsen</p>
									<p class="preview">Mike, I know everything! I'm Donna..</p>
								</div>
							</div>
						</li>
						<li class="contact">
							<div class="wrap">
								<span class="contact-status busy"></span>
								<img src="./statics/image/user.jpg" alt="" />
								<div class="meta">
									<p class="name">Jessica Pearson</p>
									<p class="preview">Have you finished the draft on the Hinsenburg deal?</p>
								</div>
							</div>
						</li>
						<li class="contact">
							<div class="wrap">
								<span class="contact-status"></span>
								<img src="./statics/image/user.jpg" alt="" />
								<div class="meta">
									<p class="name">Harold Gunderson</p>
									<p class="preview">Thanks Mike! :)</p>
								</div>
							</div>
						</li>
						<li class="contact">
							<div class="wrap">
								<span class="contact-status"></span>
								<img src="./statics/image/user.jpg" alt="" />
								<div class="meta">
									<p class="name">Daniel Hardman</p>
									<p class="preview">We'll meet again, Mike. Tell Jessica I said 'Hi'.</p>
								</div>
							</div>
						</li>
						<li class="contact">
							<div class="wrap">
								<span class="contact-status busy"></span>
								<img src="./statics/image/user.jpg" alt="" />
								<div class="meta">
									<p class="name">Katrina Bennett</p>
									<p class="preview">I've sent you the files for the Garrett trial.</p>
								</div>
							</div>
						</li>
						<li class="contact">
							<div class="wrap">
								<span class="contact-status"></span>
								<img src="./statics/image/user.jpg" alt="" />
								<div class="meta">
									<p class="name">Charles Forstman</p>
									<p class="preview">Mike, this isn't over.</p>
								</div>
							</div>
						</li>
						<li class="contact">
							<div class="wrap">
								<span class="contact-status"></span>
								<img src="./statics/image/user.jpg" alt="" />
								<div class="meta">
									<p class="name">Jonathan Sidwell</p>
									<p class="preview"><span>You:</span> That's bullshit. This deal is solid.</p>
								</div>
							</div>
						</li> -->
					</ul>
				</div>
				<div id="bottom-bar">
					<input id="friendid" type="text" placeholder="ID Contact" />
					<button id="addcontact"><i class="fa fa-user-plus fa-fw" aria-hidden="true"></i> <span>Add contact</span></button>
				</div>
			</div>
			<div class="content">
				<div class="contact-profile">
					<img src="./statics/image/user.jpg" alt="" />
					<input type="hidden" id="roomid" value="-1">
					<p></p>
					<div class="social-media">
						<a href="logout.php"><i class="fa fa-times" aria-hidden="true"></i></a>
					</div>
				</div>
				<div class="messages">
					<input type="hidden" id="minchatid" value="-1">
					<input type="hidden" id="maxchatid" value="-1">
					<ul>
						<!-- <li class="sent">
							<img src="./statics/image/user.jpg" alt="" />
							<p>How the hell am I supposed to get a jury to believe you when I am not even sure that I do?!</p>
						</li>
						<li class="replies">
							<img src="./statics/image/user.jpg" alt="" />
							<p>When you're backed against the wall, break the god damn thing down.</p>
						</li>
						<li class="replies">
							<img src="./statics/image/user.jpg" alt="" />
							<p>Excuses don't win championships.</p>
						</li>
						<li class="sent">
							<img src="./statics/image/user.jpg" alt="" />
							<p>Oh yeah, did Michael Jordan tell you that?</p>
						</li>
						<li class="replies">
							<img src="./statics/image/user.jpg" alt="" />
							<p>No, I told him that.</p>
						</li>
						<li class="replies">
							<img src="./statics/image/user.jpg" alt="" />
							<p>What are your choices when someone puts a gun to your head?</p>
						</li>
						<li class="sent">
							<img src="./statics/image/user.jpg" alt="" />
							<p>What are you talking about? You do what they say or they shoot you.</p>
						</li>
						<li class="replies">
							<img src="./statics/image/user.jpg" alt="" />
							<p>Wrong. You take the gun, or you pull out a bigger one. Or, you call their bluff. Or, you do any one of a hundred and forty six other things.</p>
						</li> -->
					</ul>
				</div>
				<div class="message-input">
					<div class="wrap">
					<input type="text" placeholder="Write your message..." />
					<i class="fa fa-paperclip attachment" aria-hidden="true"></i>
					<button class="submit"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
					</div>
				</div>
			</div>
		</div>
		<link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.2/css/font-awesome.min.css'>
		<link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700,300' rel='stylesheet' type='text/css'>
		<link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css'>
		<link rel="stylesheet" type="text/css" href="./statics/css/index.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="./statics/js/index.js"></script>
    </body>
</html>