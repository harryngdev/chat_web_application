<?php
    require_once('config.php');

    header("Content-Type: application/json; charset=UTF-8");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_SESSION['userid'])) {
            $action = $_POST['action'];
            if ($action == 'addFriend') {
                $conn = mysqli_connect($servername, $username, $password, $databasename);
                // mysqli_set_charset($conn, 'utf8mb4');
                $stmt = mysqli_prepare($conn, "CALL checkFriend(?, ?) ");
                mysqli_stmt_bind_param($stmt, "ii", $_SESSION['userid'], $_POST['friendid']);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) == 0) {
                    $conn = mysqli_connect($servername, $username, $password, $databasename);
                    mysqli_set_charset($conn, 'utf8mb4');
                    $stmt = mysqli_prepare($conn, "CALL addRoom(?, ?) ");
                    mysqli_stmt_bind_param($stmt, "ii", $_SESSION['userid'], $_POST['friendid']);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $outp = mysqli_fetch_all($result, MYSQLI_ASSOC);

                    echo json_encode($outp);
                }

            }

            if ($action == 'selectTop10Friend') {
                $conn = mysqli_connect($servername, $username, $password, $databasename);
                mysqli_set_charset($conn, 'utf8mb4');
                $stmt = mysqli_prepare($conn, "CALL selectTop10Friend(?) ");
                $userid = (isset($_SESSION['userid'])) ? $_SESSION['userid'] : 0;
                mysqli_stmt_bind_param($stmt, "i", $userid);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $outp = mysqli_fetch_all($result, MYSQLI_ASSOC);

                echo json_encode($outp);

            }

            if ($action == 'updateChat') {
                $_SESSION['roomid'] = $_POST['roomid'];
                $_SESSION['maxchatid'] = $_POST['maxchatid'];
                $conn = mysqli_connect($servername, $username, $password, $databasename);
                $stmt = mysqli_prepare($conn, "CALL selectNewestChat(?, ?) ");
                $roomid = (isset($_SESSION['roomid'])) ? $_SESSION['roomid'] : 0;
                $maxchatid = (isset($_SESSION['maxchatid'])) ? $_SESSION['maxchatid'] : 0;
                mysqli_stmt_bind_param($stmt, "ii", $roomid, $maxchatid);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $outp = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
                echo json_encode($outp);
    
            }

            if ($action == 'updateFriend') {
                $_SESSION['maxtime'] = $_POST['maxtime'];
                $conn = mysqli_connect($servername, $username, $password, $databasename);
                $stmt = mysqli_prepare($conn, "CALL selectNewestFriend(?, ?) ");
                $userid = (isset($_SESSION['userid'])) ? $_SESSION['userid'] : 0;
                $maxtime = (isset($_SESSION['maxtime'])) ? $_SESSION['maxtime'] : "";
                mysqli_stmt_bind_param($stmt, "is", $userid, $maxtime);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $outp = mysqli_fetch_all($result, MYSQLI_ASSOC);

                echo json_encode($outp);
            }

            if ($action == 'searchFriend') {
                $conn = mysqli_connect($servername, $username, $password, $databasename);
                $stmt = mysqli_prepare($conn, "CALL searchFriend(?, ?) ");
                mysqli_stmt_bind_param($stmt, "is", $_SESSION['userid'], $_POST['s']);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $outp = mysqli_fetch_all($result, MYSQLI_ASSOC);
                
                echo json_encode($outp);
            }

            if ($action == 'selectTop10Chat') {
                $_SESSION['roomid'] = $_POST['roomid'];
                $conn = mysqli_connect($servername, $username, $password, $databasename);
                $stmt = mysqli_prepare($conn, "CALL selectTop10Chat(?) ");
                $roomid = (isset($_SESSION['roomid'])) ? $_SESSION['roomid'] : 0;
                mysqli_stmt_bind_param($stmt, "i", $roomid);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $outp = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
                echo json_encode($outp);
    
            }

            if ($action == 'addChat') {
                $conn = mysqli_connect($servername, $username, $password, $databasename);
                $stmt = mysqli_prepare($conn, "CALL addChat(?, ?, ?) ");
                mysqli_stmt_bind_param($stmt, "iis", $_POST['roomid'], $_SESSION['userid'], $_POST['chat']);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $outp = mysqli_fetch_all($result, MYSQLI_ASSOC);

                echo json_encode($outp);
            }
        }
    }
?>