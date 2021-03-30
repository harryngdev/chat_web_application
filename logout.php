<?php
    require_once('config.php');

    session_unset();
    session_destroy();
    session_start();
    header('location: index.php');
?>