<?php
$host = "127.0.0.1";
$port = 3307; // Use 3306 if running on default port
$user = "root";
$pass = "";
$dbname = "studenthub";

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>