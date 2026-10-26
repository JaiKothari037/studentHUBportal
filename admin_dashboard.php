<?php
require_once "timeout.php";
require_once "db.php";

// 1. RBAC CHECK: Restrict non-admin access (Returns HTTP 403)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "<!DOCTYPE html><html><head><title>403 Forbidden</title>";
    echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css' rel='stylesheet'></head>";
    echo "<body class='bg-light text-center py-5'>";
    echo "<div class='container'><h1 class='display-1 text-danger fw-bold'>403</h1>";
    echo "<h2 class='mb-3'>Forbidden: Access Restricted</h2>";
    echo "<p class='lead text-muted'>You do not have administrative privileges to access this page.</p>";
    echo "<a href='student_dashboard.php' class='btn btn-primary me-2'>Go to Student Dashboard</a>";
    echo "<a href='logout.php' class='btn btn-outline-danger'>Logout</a></div></body></html>";
    exit();
}

// 2. HELPER FUNCTION: Log Actions to audit_logs
function record_audit_log($conn, $user_id, $username, $action) {
    $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, username, action) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("iss", $user_id, $username, $action);
        $stmt->execute();
        $stmt->close();
    }
}

// Log dashboard access once per session view
if (!isset($_SESSION['audit_logged'])) {
    record_audit_log($conn, $_SESSION['user_id'], $_SESSION['username'], 'Accessed Admin Dashboard');
    $_SESSION['audit_logged'] = true;
}

// 3. AGGREGATION QUERIES FOR DYNAMIC STATS
$user_count = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'] ?? 0;
$student_count = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'] ?? 0;
$event_count = $conn->query("SELECT COUNT(*) AS total FROM events")->fetch_assoc()['total'] ?? 0;
$active_student_count = $conn->query("SELECT COUNT(*) AS total FROM students WHERE status='active'")->fetch_assoc()['total'] ?? 0;

// FETCH LATEST REGISTRATIONS
$latest_registrations = $conn->query("
    SELECT r.registration_id, s.name AS student_name, e.title AS event_title, r.registered_at 
    FROM registrations r
    JOIN students s ON r.student_id = s.student_id
    JOIN events e ON r.event_id = e.event_id
    ORDER BY r.registration_id DESC LIMIT 5
");

// FETCH RECENT AUDIT LOGS
$audit_logs = $conn->query("SELECT * FROM audit_logs ORDER BY log_id DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 14 - Admin Dashboard & Audit System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .stat-card { border-left: 5px solid #0d6efd; border-radius: 6px; }
        .stat-card.green { border-left-color: #198754; }
        .stat-card.orange { border-left-color: #fd7e14; }
        .stat-card.purple { border-left-color: #6f42c1; }
    </style>
</head>
<body>

    <!-- DYNAMIC ROLE-BASED NAVIGATION MENU -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">StudentHub Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="admin_dashboard.php">Admin Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="students.php">Student Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="events.php">Event Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="student_crud.html">AJAX API CRUD</a></li>
                </ul>
                <div class="d-flex align-items-center text-white">
                    <span class="me-3">Logged in: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                    <span class="badge bg-danger me-3"><?php echo strtoupper($_SESSION['role']); ?> ROLE</span>
                    <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <h2 class="mb-1">Practical 14: Administrative Dashboard</h2>
        <p class="text-muted mb-4">System analytics, role-based access moderation, and real-time activity traceability.</p>

        <!-- DYNAMIC STATS WIDGETS -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card stat-card shadow-sm p-3 bg-white">
                    <div class="text-muted small text-uppercase font-weight-bold">Total Portal Users</div>
                    <div class="display-6 fw-bold text-primary"><?php echo $user_count; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card green shadow-sm p-3 bg-white">
                    <div class="text-muted small text-uppercase font-weight-bold">Total Students</div>
                    <div class="display-6 fw-bold text-success"><?php echo $student_count; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card orange shadow-sm p-3 bg-white">
                    <div class="text-muted small text-uppercase font-weight-bold">Active Students</div>
                    <div class="display-6 fw-bold text-warning"><?php echo $active_student_count; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card purple shadow-sm p-3 bg-white">
                    <div class="text-muted small text-uppercase font-weight-bold">Total Events</div>
                    <div class="display-6 fw-bold text-purple" style="color:#6f42c1;"><?php echo $event_count; ?></div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- LATEST REGISTRATIONS WIDGET -->
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Latest Event Registrations</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Reg ID</th>
                                    <th>Student</th>
                                    <th>Event</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($latest_registrations && $latest_registrations->num_rows > 0): ?>
                                    <?php while ($reg = $latest_registrations->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $reg['registration_id']; ?></td>
                                            <td><strong><?php echo htmlspecialchars($reg['student_name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($reg['event_title']); ?></td>
                                            <td><small class="text-muted"><?php echo $reg['registered_at']; ?></small></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted py-3">No event registrations found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- AUDIT LOGS WIDGET -->
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">System Audit Logs (Traceability)</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Log ID</th>
                                    <th>Username</th>
                                    <th>Action</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($audit_logs && $audit_logs->num_rows > 0): ?>
                                    <?php while ($log = $audit_logs->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $log['log_id']; ?></td>
                                            <td><strong><?php echo htmlspecialchars($log['username']); ?></strong></td>
                                            <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($log['action']); ?></span></td>
                                            <td><small class="text-muted"><?php echo $log['created_at']; ?></small></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted py-3">No audit logs recorded yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>