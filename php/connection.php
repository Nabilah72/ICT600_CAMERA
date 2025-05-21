<?php
// Database credentials
$host = "localhost";
$user = "root";
$pass = "";
$db = "camerainventorydb";
$port = 4306;

// Create a new MySQLi connection
$connect = new mysqli($host, $user, $pass, $db, $port);

// Check for connection error
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}
?>
