<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeout_duration = 30; // Session timeout set to 30 seconds for quick testing

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=unauthorized");
    exit();
}

// 2. Check for session inactivity timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout_duration)) {
    session_unset();
    session_destroy();
    header("Location: login.php?error=timeout");
    exit();
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();
?>