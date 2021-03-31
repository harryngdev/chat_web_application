<?php
    require_once('config.php');

    if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) {
        header('location: login.php');
    }
    header('Content-Type: text/html; charset="UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chat Room</title>
    <link
        rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
    />
    <link 
		href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" 
		rel="stylesheet"
	/>
    <link rel="stylesheet" href="css/reset.css" />
    <link rel="stylesheet" href="css/index/style.css" />
</head>
<body>
<div id="wrapper">
    <div id="author-intro">
        <div class="logo"><a href="https://nvanha.github.io/myweb/index.htm" target="_blank"><img src="image/logo.png" alt="logo" /></a></div>
    </div>
    <div id="container">
        <div id="side-panel">
            <div id="profile">
                <div class="profile-inner">
                    <input type="hidden" id="userid" value="<?php echo $_SESSION['userid'] ?>" />
                    <img src="image/uploads/<?php echo $_SESSION['useravt'] ?>" id="profile-img" alt="user" class="online" />
                    <p><?php echo $_SESSION['username']; ?></p>
                    <i class="fa fa-chevron-down expand-button" aria-hidden="true"></i>
                    <label for="id">User id: <?php echo $_SESSION['userid'] ?></label>
                </div>
            </div>
            <div id="search">
                <div class="search-inner">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <input type="text" placeholder="Search contacts..." />
                </div>
            </div>
            <div id="title"><p>direct message</p></div>
            <div id="contacts">
                <input type="hidden" id="mintime" value="" />
                <input type="hidden" id="maxtime" value="" />
                <div class="contact-inner">
                    <ul class="contact-list">
                        <!-- <li class="contact-item">
                            <div class="online">
                                <img src="image/user3.jpg" alt="user" />
                            </div>    
                            <div class="contact-info">
                                <p class="name">Thao</p>
                                <p class="preview">Late action: 2021-03-25 13:12:23</p>
                            </div>
                        </li>
                        <li class="contact-item">
                            <div class="offline">
                                <img src="image/user5.jpg" alt="user"  />
                            </div>
                            <div class="contact-info">
                                <p class="name">Quy</p>
                                <p class="preview">Late action: 2021-03-25 13:12:23</p>
                            </div>
                        </li>
                        <li class="contact-item">
                            <div class="offline">
                                <img src="image/user4.jpg" alt="user"  />
                            </div>
                            <div class="contact-info">
                                <p class="name">Khanh</p>
                                <p class="preview">Late action: 2021-03-25 13:12:23</p>
                            </div>
                        </li>
                        <li class="contact-item">
                            <div class="online">
                                <img src="image/user1.jpg" alt="user" />
                            </div>
                            <div class="contact-info">
                                <p class="name">Nghia</p>
                                <p class="preview">Late action: 2021-03-25 13:12:23</p>
                            </div>
                        </li> -->
                    </ul>
                </div>
            </div>
            <div id="bottom-bar">
                <div class="bottom-bar-inner">
                    <input type="text" id="friendid" placeholder="ID Contact" />
                    <button id="addcontact"><i class="fa fa-plus" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
        <div id="content">
            <div id="contact-profile">
                <div class="contact-profile-inner">
                    <input type="hidden" value="-1" id="roomid" />
                    <p class="name none-el"></p>
                    <a href="logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i><p>SIGN OUT</p></a>
                </div>
            </div>
            <div id="messages">
                <input type="hidden" value="-1" id="minchatid" />
                <input type="hidden" value="-1" id="maxchatid" />
                <div class="message-bg">
                    <section></section>
                    <p>No one's around to play with Wumpus</p>
                </div>
                <ul>
                    <li class="information none-el">
                        <img src="image/user3.jpg" alt="avt" class="img-inner" />
                        <div class="description">
                            <h2></h2>
                            <p>This is the beginning of your direct message history with <span></span></p>
                        </div>
                    </li>
                    <!-- <li class="sent">
                        <img src="image/user2.jpg" alt="avt" class="img-inner" />
                        <div class="details">
                            <div class="info">
                                <p class="user_name">Ha</p>
                                <p class="time">30/03/2021</p>
                            </div>
                            <p class="text">Test!</p>
                        </div>
                    </li>
                    <li class="sent">
                        <img src="image/user2.jpg" alt="avt" class="img-inner" />
                        <div class="details">
                            <div class="info">
                                <p class="user_name">Ha</p>
                                <p class="time">30/03/2021</p>
                            </div>
                            <p class="text">Test!</p>
                        </div>
                    </li>
                    <li class="replies">
                        <img src="image/user3.jpg" alt="avt" class="img-inner" />
                        <div class="details">
                            <div class="info">
                                <p class="user_name">Thao</p>
                                <p class="time">30/03/2021</p>
                            </div>
                            <p class="text">Test!</p>
                        </div>
                    </li> -->
                </ul>
            </div>
            <div id="message-input" class="none-el">
                <div class="message-input-inner">
                    <input type="text" placeholder="Write your message" />
                    <button class="submit"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="js/index.js"></script>
</body>
</html>