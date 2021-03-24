<?php
require_once('config.php');

header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['userid'])) {
        $action = $_POST['action'];
        if ($action == 'selectTop10Chat') {
            $_SESSION['roomid'] = $_POST['roomid'];
            $conn = new mysqli($server, $user, $pass, $db);
            $conn->set_charset('utf8');
            $stmt = $conn->prepare("call selectTop10Chat(?) ");
            $roomid = (isset($_SESSION['roomid'])) ? $_SESSION['roomid'] : 0;
            $stmt->bind_param("i", $roomid);
            $stmt->execute();
            $result = $stmt->get_result();
            $outp = $result->fetch_all(MYSQLI_ASSOC);

            echo json_encode($outp);

        }

        if ($action == 'searchFriend') {
            $conn = new mysqli($server, $user, $pass, $db);
            $conn->set_charset('utf8');
            $stmt = $conn->prepare("call searchFriend(?, ?) ");
            $stmt->bind_param("is", $_SESSION['userid'], $_POST['s']);
            $stmt->execute();
            $result = $stmt->get_result();
            $outp = $result->fetch_all(MYSQLI_ASSOC);

            echo json_encode($outp);

        }

        if ($action == 'addFriend') {
            $conn = new mysqli($server, $user, $pass, $db);
            $conn->set_charset('utf8');
            $stmt = $conn->prepare("call checkFriend(?, ?) ");
            $stmt->bind_param("ii", $_SESSION['userid'], $_POST['friendid']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 0) {
                $conn = new mysqli($server, $user, $pass, $db);
                $conn->set_charset('utf8');
                $stmt = $conn->prepare("call addRoom(?, ?) ");
                $stmt->bind_param("ii", $_SESSION['userid'], $_POST['friendid']);
                $stmt->execute();
                $result = $stmt->get_result();
                $outp = $result->fetch_all(MYSQLI_ASSOC);

                echo json_encode($outp);
            }

        }

        if ($action == 'addChat') {
            $conn = new mysqli($server, $user, $pass, $db);
                $conn->set_charset('utf8');
                $stmt = $conn->prepare("call addChat(?, ?, ?) ");
                $stmt->bind_param("iis", $_POST['roomid'], $_SESSION['userid'], $_POST['chat']);
                $stmt->execute();
                $result = $stmt->get_result();
                $outp = $result->fetch_all(MYSQLI_ASSOC);

                echo json_encode($outp);
        }

        if ($action == 'selectTop10Friend') {
            $conn = new mysqli($server, $user, $pass, $db);
            $conn->set_charset('utf8');
            $stmt = $conn->prepare("call selectTop10Friend(?) ");
            $userid = (isset($_SESSION['userid'])) ? $_SESSION['userid'] : 0;
            $stmt->bind_param("i", $userid);
            $stmt->execute();
            $result = $stmt->get_result();
            $outp = $result->fetch_all(MYSQLI_ASSOC);

            echo json_encode($outp);

        }

        if ($action == 'updateChat') {
            $_SESSION['roomid'] = $_POST['roomid'];
            $_SESSION['maxchatid'] = $_POST['maxchatid'];
            $conn = new mysqli($server, $user, $pass, $db);
            $conn->set_charset('utf8');
            $stmt = $conn->prepare("call selectNewestChat(?, ?) ");
            $roomid = (isset($_SESSION['roomid'])) ? $_SESSION['roomid'] : 0;
            $maxchatid = (isset($_SESSION['maxchatid'])) ? $_SESSION['maxchatid'] : 0;
            $stmt->bind_param("ii", $roomid, $maxchatid);
            $stmt->execute();
            $result = $stmt->get_result();
            $outp = $result->fetch_all(MYSQLI_ASSOC);

            echo json_encode($outp);

        }

        if ($action == 'updateFriend') {
            $_SESSION['maxtime'] = $_POST['maxtime'];
            $conn = new mysqli($server, $user, $pass, $db);
            $conn->set_charset('utf8');
            $stmt = $conn->prepare("call selectNewestFriend(?, ?) ");
            $userid = (isset($_SESSION['userid'])) ? $_SESSION['userid'] : 0;
            $maxtime = (isset($_SESSION['maxtime'])) ? $_SESSION['maxtime'] : "";
            $stmt->bind_param("is", $userid, $maxtime);
            $stmt->execute();
            $result = $stmt->get_result();
            $outp = $result->fetch_all(MYSQLI_ASSOC);

            echo json_encode($outp);

        }
    }
}
?>