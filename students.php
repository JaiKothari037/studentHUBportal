<?php
require_once "db.php";
$sql = "SELECT * FROM students";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Student List</h2>
<?php
foreach ($students as $student) {
 echo $student["student_id"] . " -
";
 echo $student["name"] . " - ";
 echo $student["email"] . " - ";
 echo $student["course"] . "<br>";
}
?>
