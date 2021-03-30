<?php
    $servername = "";
    $username = "";
    $password = "";
    $databasename = "";

    session_start();
    $time = $_SERVER['REQUEST_TIME']; // same time()

    // For a 30 minute timeout (hết giờ, chờ), specified (chỉ định) in seconds
    $timeout_duration = 1800;

    /*
    - Here we look for the user's LAST_ACTIVITY timestamp (dấu thời gian).
    - If it's set (đặt) and indicates (cho biết) our $timeout_duration has passed, blow away (xóa) any previous $_SESSION data and start a new one.
    */
    if (isset($_SESSION['LAST_ACTIVITY']) && ($time - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
        session_unset();
        session_destroy();
        session_start();
    }

    /*
    - Finally, update LAST_ACTIVITY so that our timeout is based on it and not the user's login time.
    */
    $_SESSION['LAST_ACTIVITY'] = $time;
?>