<?php
require_once "db.php";

$username = "admin_user";
$email    = "admin@gmail.com";
$password = "admin123";
$role     = "admin";

// Generate native PHP hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Clean old entry if it exists
$pdo->prepare("DELETE FROM users WHERE email = ?")->execute([$email]);

// Insert new admin user
$stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");

if ($stmt->execute([$username, $email, $hashed_password, $role])) {
    echo "<h2>Admin Created Successfully!</h2>";
    echo "<p><strong>Email:</strong> admin@gmail.com</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo '<a href="login.php">Click here to Go to Login</a>';
} else {
    echo "Failed to create admin user.";
}
?>