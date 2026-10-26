<?php
require_once "db.php";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=students_export.csv');

$output = fopen('php://output', 'w');
fputcsv($output, array('Student ID', 'Name', 'Email', 'Course', 'Status', 'Created At'));

$query = "SELECT student_id, name, email, course, status, created_at FROM students";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}
fclose($output);
exit();
?>