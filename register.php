<?php
require_once "db_mysqli.php";

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize & capture inputs
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // 1. Backend Validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    // 2. Check for Duplicate Email or Username
    if (empty($errors)) {
        $checkSql = "SELECT id FROM users WHERE email = ? OR username = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ss", $email, $username);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $errors[] = "Username or Email is already registered.";
        }
        $checkStmt->close();
    }

    // 3. Hash Password and Insert into Database
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $insertSql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("sss", $username, $email, $hashed_password);

        if ($insertStmt->execute()) {
            $success = "Registration successful! User registered securely.";
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
        $insertStmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure User Registration</title>
</head>
<body>
    <h2>Secure User Registration</h2>

    <?php
    if (!empty($errors)) {
        echo '<div style="color: red;"><ul>';
        foreach ($errors as $err) {
            echo '<li>' . htmlspecialchars($err) . '</li>';
        }
        echo '</ul></div>';
    }

    if ($success) {
        echo '<p style="color: green;">' . htmlspecialchars($success) . '</p>';
    }
    ?>

    <form action="register.php" method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" minlength="6" required><br><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>