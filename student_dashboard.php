<?php
require_once "timeout.php";

if ($_SESSION['role'] !== 'student') {
    echo "<h2>Access Denied: Admin attempting to access Student Dashboard (Unauthorized Access)</h2>";
    echo '<p><a href="admin_dashboard.php">Go to Admin Dashboard</a> | <a href="logout.php">Logout</a></p>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Student Dashboard</h2>
    <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</p>
    <p>Role: <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong></p>
    <p>Active Session ID: <code><?php echo $_SESSION['session_id_display']; ?></code></p>
    
    <p><a href="admin_dashboard.php">Try Accessing Admin Dashboard</a></p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>