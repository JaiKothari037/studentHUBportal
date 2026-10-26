<?php
require_once "timeout.php";
require_once "db.php";

// RBAC Protection: Allow Admin users only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<h2 style='color:red;'>Access Denied: Admin privileges required to manage events.</h2>";
    echo '<p><a href="login.php">Go to Login</a></p>';
    exit();
}

$message = "";
$error = "";

$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
$max_size = 2 * 1024 * 1024; // 2MB

// 1. ADD / EDIT EVENT
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $category = $_POST['category'] ?? 'General';
    $status = $_POST['status'] ?? 'open';
    $poster_name = NULL;

    // File Upload & Validation
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['poster']['tmp_name'];
        $file_size = $_FILES['poster']['size'];
        $file_type = mime_content_type($file_tmp);
        $file_ext = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));

        if (!in_array($file_type, $allowed_types)) {
            $error = "Invalid file type! Only JPG, JPEG, PNG, and WEBP images are allowed.";
        } elseif ($file_size > $max_size) {
            $error = "File size exceeds the 2MB limit!";
        } else {
            $poster_name = "poster_" . time() . "_" . uniqid() . "." . $file_ext;
            if (!move_uploaded_file($file_tmp, $upload_dir . $poster_name)) {
                $error = "Failed to save uploaded file.";
            }
        }
    }

    if (empty($error)) {
        if ($action === 'add') {
            if (!empty($title) && !empty($event_date)) {
                $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, category, status, poster) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("ssssss", $title, $description, $event_date, $category, $status, $poster_name);
                    if ($stmt->execute()) {
                        $message = "Event created successfully!";
                    } else {
                        $error = "Database error: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $error = "SQL prepare error: " . $conn->error;
                }
            } else {
                $error = "Title and Event Date are required.";
            }
        } elseif ($action === 'edit') {
            $event_id = intval($_POST['event_id']);
            if ($poster_name) {
                $stmt = $conn->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, category = ?, status = ?, poster = ? WHERE event_id = ?");
                if ($stmt) {
                    $stmt->bind_param("ssssssi", $title, $description, $event_date, $category, $status, $poster_name, $event_id);
                }
            } else {
                $stmt = $conn->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, category = ?, status = ? WHERE event_id = ?");
                if ($stmt) {
                    $stmt->bind_param("sssssi", $title, $description, $event_date, $category, $status, $event_id);
                }
            }

            if (isset($stmt) && $stmt) {
                if ($stmt->execute()) {
                    $message = "Event updated successfully!";
                } else {
                    $error = "Update failed: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error = "SQL prepare error: " . $conn->error;
            }
        }
    }
}

// 2. DELETE EVENT
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    $res = $conn->query("SELECT poster FROM events WHERE event_id = $delete_id");
    if ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['poster']) && file_exists($upload_dir . $row['poster'])) {
            unlink($upload_dir . $row['poster']);
        }
    }

    $stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $message = "Event deleted successfully!";
        } else {
            $error = "Failed to delete event.";
        }
        $stmt->close();
    }
}

// FETCH EVENT FOR EDITING
$edit_event = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $res = $conn->query("SELECT * FROM events WHERE event_id = $edit_id");
    if ($res) {
        $edit_event = $res->fetch_assoc();
    }
}

// FETCH ALL EVENTS
$events = $conn->query("SELECT * FROM events ORDER BY event_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Practical 12 - Event Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f8f9fa; }
        .card { background: white; padding: 20px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; vertical-align: middle; }
        th { background-color: #007bff; color: white; }
        .msg { padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 15px; }
        .err { padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; margin-bottom: 15px; }
        .poster-img { width: 80px; height: 100px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc; }
        .btn { padding: 6px 12px; text-decoration: none; color: white; background: #007bff; border-radius: 3px; border: none; cursor: pointer; }
        .btn-danger { background: #dc3545; }
        .btn-success { background: #28a745; }
    </style>
</head>
<body>

    <h2>Practical 12: Event Management System (Admin CRUD & Upload)</h2>
    <p>Logged in as Admin: <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></strong> | <a href="logout.php">Logout</a></p>

    <?php if ($message): ?><div class="msg"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="err"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <!-- ADD / EDIT EVENT FORM -->
    <div class="card">
        <h3><?php echo $edit_event ? "Edit Event Details" : "Create New Event"; ?></h3>
        <form action="events.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?php echo $edit_event ? 'edit' : 'add'; ?>">
            <?php if ($edit_event): ?>
                <input type="hidden" name="event_id" value="<?php echo $edit_event['event_id']; ?>">
            <?php endif; ?>

            <p>
                <label>Event Title:</label><br>
                <input type="text" name="title" value="<?php echo htmlspecialchars($edit_event['title'] ?? ''); ?>" required style="width: 300px;">
            </p>
            <p>
                <label>Description:</label><br>
                <textarea name="description" rows="3" style="width: 300px;"><?php echo htmlspecialchars($edit_event['description'] ?? ''); ?></textarea>
            </p>
            <p>
                <label>Event Date:</label><br>
                <input type="date" name="event_date" value="<?php echo htmlspecialchars($edit_event['event_date'] ?? ''); ?>" required>
            </p>
            <p>
                <label>Category:</label><br>
                <select name="category">
                    <option value="Technical" <?php echo ($edit_event['category'] ?? '') === 'Technical' ? 'selected' : ''; ?>>Technical</option>
                    <option value="Cultural" <?php echo ($edit_event['category'] ?? '') === 'Cultural' ? 'selected' : ''; ?>>Cultural</option>
                    <option value="Sports" <?php echo ($edit_event['category'] ?? '') === 'Sports' ? 'selected' : ''; ?>>Sports</option>
                    <option value="Workshop" <?php echo ($edit_event['category'] ?? '') === 'Workshop' ? 'selected' : ''; ?>>Workshop</option>
                </select>
            </p>
            <p>
                <label>Event Poster Image (JPG/PNG/WEBP, Max 2MB):</label><br>
                <input type="file" name="poster" accept="image/*">
            </p>
            <button type="submit" class="btn btn-success"><?php echo $edit_event ? "Update Event" : "Create Event"; ?></button>
            <?php if ($edit_event): ?><a href="events.php" class="btn">Cancel</a><?php endif; ?>
        </form>
    </div>

    <!-- EVENTS LIST TABLE -->
    <h3>Managed Events</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Poster</th>
                <th>Title</th>
                <th>Category</th>
                <th>Event Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($events && $events->num_rows > 0): ?>
                <?php while ($row = $events->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['event_id']; ?></td>
                        <td>
                            <?php if ($row['poster'] && file_exists($upload_dir . $row['poster'])): ?>
                                <img src="<?php echo $upload_dir . $row['poster']; ?>" class="poster-img" alt="Poster">
                            <?php else: ?>
                                <span>No Poster</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($row['title']); ?></strong><br><small><?php echo htmlspecialchars($row['description']); ?></small></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                        <td>
                            <a href="events.php?edit_id=<?php echo $row['event_id']; ?>" class="btn">Edit</a>
                            <a href="events.php?delete_id=<?php echo $row['event_id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this event?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">No events found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>