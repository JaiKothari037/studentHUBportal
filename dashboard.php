<?php
require_once "timeout.php";

// Redirect to specific role dashboards
if ($_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
} elseif ($_SESSION['role'] === 'student') {
    header("Location: student_dashboard.php");
    exit();
}
?>