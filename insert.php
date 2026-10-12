<?php
require_once "db.php";
$name = "Neha";
$email = "neha@gmail.com";
$course = "Computer";
$sql = "INSERT INTO students (name, 
email, course)
 VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
 $name,
 $email,
 $course
]);
echo "Student Added Successfully";
?>
