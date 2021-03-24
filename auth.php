<?php
require_once('config.php');

header('Content-Type: text/html; charset="UTF-8"');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['action'])) {
        $action = $_POST['action'];
        if ($action == "login") {
            $conn = new mysqli($server, $user, $pass, $db);
            $conn->set_charset('utf8');
            $stmt = $conn->prepare("call checkLogin(?)");
            $stmt->bind_param("s", $_POST['username']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    if (password_verify($_POST['password'], $row['USER_PASSWORD'])) {
                        $_SESSION['userid'] = $row['USER_ID'];
                        $_SESSION['username'] = $row['USER_NAME'];
                        header('location: index.php');
                    }
                }

            }
        }

        if ($action == "register") {
            $conn = new mysqli($server, $user, $pass, $db);
            $conn->set_charset('utf8');
            $stmt = $conn->prepare("select * from user where USER_NAME = ?");
            $stmt->bind_param("ss", $_POST['username']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 0) {
                $conn->set_charset('utf8');
                $stmt = $conn->prepare("select * from person where PERSON_EMAIL = ?");
                $stmt->bind_param("s", $_POST['email']);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows == 0) {
                    $conn->set_charset('utf8');
                    $stmt = $conn->prepare("call register(?, ?, ?, ?, ?)");
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt->bind_param("sssss", $_POST['username'], $password, $_POST['username'], $_POST['email'], $_POST['username']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    header('location: index.php');
                }
            }
        }
    }
}

?>
<!doctype html>
<html lang="en">
	<head>
		<title>App Chat</title>
        <meta charset="utf-8" />
	</head>
	<body>
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="panel panel-login">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-6">
                                    <a href="#" class="active" id="login-form-link">Login</a>
                                </div>
                                <div class="col-xs-6">
                                    <a href="#" id="register-form-link">Register</a>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form id="login-form" action="./auth.php" method="post" role="form" style="display: block;">
                                        <input type="hidden" name="action" id="action" tabindex="1" class="form-control" value="login">
                                        <div class="form-group">
                                            <input type="text" name="username" id="username" tabindex="1" class="form-control" placeholder="Username" value="" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" id="password" tabindex="2" class="form-control" placeholder="Password" required>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 col-sm-offset-3">
                                                    <input type="submit" name="login-submit" id="login-submit" tabindex="4" class="form-control btn btn-login" value="Log In">
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </form>
                                    <form id="register-form" action="./auth.php" method="post" role="form" style="display: none;">
                                        <input type="hidden" name="action" id="action" tabindex="1" class="form-control" value="register">
                                        <div class="form-group">
                                            <input type="text" name="username" id="username" tabindex="1" class="form-control" placeholder="Username" value="" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="email" name="email" id="email" tabindex="1" class="form-control" placeholder="Email Address" value="" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" id="password" tabindex="2" class="form-control" placeholder="Password" required>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 col-sm-offset-3">
                                                    <input type="submit" name="register-submit" id="register-submit" tabindex="4" class="form-control btn btn-register" value="Register Now">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.2/css/font-awesome.min.css'>
		<link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700,300' rel='stylesheet' type='text/css'>
		<link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css'>
		<link rel="stylesheet" type="text/css" href="./statics/css/auth.css">
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
		<script src="./statics/js/auth.js"></script>
    </body>
</html>