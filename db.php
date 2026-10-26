<?php
$host = "127.0.0.1";
$port = 3307; // Change to 3306 if using the default MySQL port
$user = "root";
$pass = "";
$dbname = "studenthub";

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>