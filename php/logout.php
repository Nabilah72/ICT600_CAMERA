<?php
session_start(); // Start session

// Check if logout request was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../index.html");
    exit();
}
?>