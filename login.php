<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Practical 10</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>User Login</h2>

        <?php if (isset($_GET['error'])): ?>
            <p class="error">
                <?php 
                    if ($_GET['error'] == 'invalid') echo "Invalid email or password.";
                    elseif ($_GET['error'] == 'unauthorized') echo "Unauthorized access!";
                    elseif ($_GET['error'] == 'timeout') echo "Session timed out due to inactivity.";
                ?>
            </p>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'logged_out'): ?>
            <p class="success">Successfully logged out.</p>
        <?php endif; ?>

        <form action="authenticate.php" method="POST">
            <label>Email:</label><br>
            <input type="email" name="email" required><br><br>

            <label>Password:</label><br>
            <input type="password" name="password" required><br><br>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>