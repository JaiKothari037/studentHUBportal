<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeout_duration = 30; // 30 seconds for easy testing

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=unauthorized");
    exit();
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout_duration)) {
    session_unset();
    session_destroy();
    header("Location: login.php?error=timeout");
    exit();
}

$_SESSION['last_activity'] = time();
?>