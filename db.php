<?php
$host = "127.0.0.1";
$port = 3307;
$dbname = "studenthub";
$username = "root";
$password = "";
try {
 $pdo = new PDO(
 
"mysql:host=$host;port=$port;dbname=$dbname",
 $username,
 $password
 );
 echo "Database Connected 
Successfully";
}
catch (PDOException $e) {
 echo "Connection Failed";
}
?>