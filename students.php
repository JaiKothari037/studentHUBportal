<?php
require_once "db.php";

$message = "";
$error = "";

// 1. ADD NEW STUDENT
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = trim($_POST['course'] ?? '');

    if (!empty($name) && !empty($email) && !empty($course)) {
        $stmt = $conn->prepare("INSERT INTO students (name, email, course) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $course);
        if ($stmt->execute()) {
            $message = "Student added successfully!";
        } else {
            $error = "Error adding student: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "All fields are required for adding a student.";
    }
}

// 2. UPDATE STUDENT DETAILS
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $student_id = intval($_POST['student_id']);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    if ($student_id > 0 && !empty($name) && !empty($email) && !empty($course)) {
        $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, course = ?, status = ? WHERE student_id = ?");
        $stmt->bind_param("ssssi", $name, $email, $course, $status, $student_id);
        if ($stmt->execute()) {
            $message = "Student updated successfully!";
        } else {
            $error = "Error updating student: " . $stmt->error;
        }
        $stmt->close();
    }
}

// 3. SOFT DELETE (Change status to inactive)
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("UPDATE students SET status = 'inactive' WHERE student_id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = "Student soft deleted successfully (status set to inactive)!";
    } else {
        $error = "Failed to soft delete student.";
    }
    $stmt->close();
}

// SEARCH AND FILTER PARAMS
$search = trim($_GET['search'] ?? '');
$filter_course = trim($_GET['course'] ?? '');

// FETCH RECORD TO EDIT
$edit_student = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $edit_stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $edit_stmt->bind_param("i", $edit_id);
    $edit_stmt->execute();
    $edit_student = $edit_stmt->get_result()->fetch_assoc();
    $edit_stmt->close();
}

// BUILD SELECT QUERY WITH MYSQLI
$query = "SELECT * FROM students WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR email LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if (!empty($filter_course)) {
    $query .= " AND course = ?";
    $params[] = $filter_course;
    $types .= "s";
}

$query .= " ORDER BY student_id DESC";
$stmt = $conn->prepare($query);

if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$students = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Practical 11 - Student Management System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f8f9fa; }
        .card { background: white; padding: 20px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        .msg { padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 15px; }
        .err { padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; margin-bottom: 15px; }
        .badge-active { background: #28a745; color: white; padding: 3px 8px; border-radius: 3px; font-size: 12px; }
        .badge-inactive { background: #dc3545; color: white; padding: 3px 8px; border-radius: 3px; font-size: 12px; }
        .btn { padding: 6px 12px; text-decoration: none; color: white; background: #007bff; border-radius: 3px; border: none; cursor: pointer; }
        .btn-danger { background: #dc3545; }
        .btn-success { background: #28a745; }
    </style>
</head>
<body>

    <h2>Practical 11: Student Management System (CRUD)</h2>

    <?php if ($message): ?><div class="msg"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="err"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <!-- ADD / EDIT FORM -->
    <div class="card">
        <h3><?php echo $edit_student ? "Edit Student Details" : "Add New Student"; ?></h3>
        <form action="students.php" method="POST">
            <input type="hidden" name="action" value="<?php echo $edit_student ? "edit" : "add"; ?>">
            <?php if ($edit_student): ?>
                <input type="hidden" name="student_id" value="<?php echo $edit_student['student_id']; ?>">
            <?php endif; ?>

            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($edit_student['name'] ?? ''); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($edit_student['email'] ?? ''); ?>" required>

            <label>Course:</label>
            <select name="course" required>
                <option value="">Select Course</option>
                <?php
                $courses = ['CSE', 'IT', 'ECE', 'Civil', 'Mechanical', 'Computer'];
                foreach ($courses as $c) {
                    $selected = ($edit_student['course'] ?? '') == $c ? 'selected' : '';
                    echo "<option value='$c' $selected>$c</option>";
                }
                ?>
            </select>

            <?php if ($edit_student): ?>
                <label>Status:</label>
                <select name="status">
                    <option value="active" <?php echo $edit_student['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $edit_student['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            <?php endif; ?>

            <button type="submit" class="btn btn-success"><?php echo $edit_student ? "Update Student" : "Add Student"; ?></button>
            <?php if ($edit_student): ?><a href="students.php" class="btn">Cancel</a><?php endif; ?>
        </form>
    </div>

    <!-- SEARCH & FILTER FORM -->
    <div class="card">
        <h3>Search & Course Filter</h3>
        <form action="students.php" method="GET">
            <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="course">
                <option value="">All Courses</option>
                <?php
                foreach ($courses as $c) {
                    $selected = $filter_course == $c ? 'selected' : '';
                    echo "<option value='$c' $selected>$c</option>";
                }
                ?>
            </select>
            <button type="submit" class="btn">Search / Filter</button>
            <a href="students.php" class="btn btn-danger">Reset</a>
            <a href="export_csv.php" class="btn btn-success" style="float: right;">Export to CSV</a>
        </form>
    </div>

    <!-- STUDENT RECORDS TABLE -->
    <h3>Student Records</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($students->num_rows > 0): ?>
                <?php while ($row = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['student_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['course']); ?></td>
                        <td>
                            <span class="badge-<?php echo $row['status']; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="students.php?edit_id=<?php echo $row['student_id']; ?>" class="btn">Edit</a>
                            <a href="students.php?delete_id=<?php echo $row['student_id']; ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('Are you sure you want to soft delete this student record?');">
                               Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No student records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>