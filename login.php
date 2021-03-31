<?php
    require_once('config.php');
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST['action'])) {
            $action = $_POST['action'];
            if ($action == "login") {
                $conn = mysqli_connect($servername, $username, $password, $databasename);
                mysqli_set_charset($conn, 'utf8mb4');
                $stmt = mysqli_prepare($conn, "CALL checkLogin(?)");
                mysqli_stmt_bind_param($stmt, 's', $_POST['username']);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        if (password_verify($_POST['password'], $row['USER_PASSWORD'])) {
                            $_SESSION['userid'] = $row['USER_ID'];
                            $_SESSION['username'] = $row['USER_NAME'];
                            $_SESSION['useravt'] = $row['USER_AVT'];
                            header('location: index.php');
                        }
                    }
                } else {
                    echo "<script>alert('User or Password is invalid');</script>";
                }
            }

            if ($action == "register") {
                $conn = mysqli_connect($servername, $username, $password, $databasename);
                mysqli_set_charset($conn, 'utf8mb4');
                $avt = $_FILES['avt']['name'];
                $avt_tmp = $_FILES['avt']['tmp_name'];
                move_uploaded_file($avt_tmp, 'image/uploads/'.$avt);
                $stmt = mysqli_prepare($conn, "SELECT * FROM user WHERE USER_NAME = ?");
                mysqli_stmt_bind_param($stmt, 's', $_POST['username']);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) == 0) {
                    mysqli_set_charset($conn, 'utf8mb4');
                    $stmt = mysqli_prepare($conn, "SELECT * FROM person WHERE PERSON_EMAIL = ?");
                    mysqli_stmt_bind_param($stmt, 's', $_POST['email']);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    if (mysqli_num_rows($result) == 0) {
                        mysqli_set_charset($conn, 'utf8mb4');
                        $stmt = mysqli_prepare($conn, "CALL register(?, ?, ?, ?, ?, ?)");
                        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        mysqli_stmt_bind_param($stmt, 'ssssss', $_POST['username'], $password, $_POST['username'], $_POST['email'], $_POST['username'], $avt);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        header('location: index.php');
                    } else {
                        echo "<script>alert('Email is already taken');</script>";
                    }
                } else {
                    echo "<script>alert('Username is already taken');</script>";
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Demo</title>
    <link
        rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
    />
    <link 
		href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" 
		rel="stylesheet"
	/>
    <link rel="stylesheet" href="css/reset.css" />
    <link rel="stylesheet" href="css/login/style.css" />
</head>
<body>
<div class="wrapper">
    <div id="bg_1"><div class="img"></div></div>
    <div id="bg_2"><div class="img"></div></div>
    <div class="panel">
        <div class="panel-heading">
            <div class="panel-title"><a href="#" class="active" id="login-form-link">Login</a></div>
            <div class="panel-title"><a href="#" id="register-form-link">Register</a></div>
        </div>
        <div class="panel-main">
            <div class="container">
                <form action="login.php" id="login-form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" id="action" value="login" />
                    <div class="form-group">
                        <input type="text" name="username" id="username" class="form-control" placeholder="Username" tabindex="1" required />
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" tabindex="2" required />
                    </div>
                    <input type="submit" name="login" id="login" tabindex="3" class="btn btn-login" value="LOG IN" />
                </form>

                <form action="login.php" id="register-form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" id="action" value="register" />
                    <div class="form-group">
                        <input type="text" name="username" id="username" class="form-control" placeholder="Username" tabindex="1" required />
                    </div>
                    <div class="form-group">
                        <input type="text" name="email" id="email" class="form-control" placeholder="Email Address" tabindex="2" required />
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" tabindex="3" required />
                    </div>
                    <div class="form-group">
                        <input type="file" name="avt" id="avt" class="form-control" tabindex="4" required />
                    </div>
                    <input type="submit" name="register" id="register" tabindex="5" class="btn btn-register" value="Register now" />
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="js/login.js"></script>
</body>
</html>