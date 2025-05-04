<?php
session_start();
include "connection.php"; // Include database connection

function titleCase($string)
{
    return ucwords(strtolower(trim($string)));
}

// Handle "Update Staff" request
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    // Retrieve input values from the form
    $id = $_POST['staffID'];
    $name = titleCase(string: $_POST['staffName']); // Title case
    $phone = preg_replace("/\D/", "", subject: $_POST['staffPhone']);
    $email = strtolower(string: trim($_POST['staffEmail'])); // Lowercase
    $role = trim($_POST['staffRole']);
    $status = trim($_POST['staffStatus']);
    $password = trim($_POST['staffPassword']);

    // Check if password is provided
    if (!empty($password)) {
        // Hash the new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement to update all staff fields including password
        $stmt = $connect->prepare("UPDATE staff SET staffName=?, staffPhone=?, staffEmail=?, staffRole=?, staffStatus=?, staffPassword=? WHERE staffID=?");
        $stmt->bind_param("sssssss", $name, $phone, $email, $role, $status, $hashedPassword, $id);
    } else {
        // Prepare SQL statement to update staff fields without changing the password
        $stmt = $connect->prepare("UPDATE staff SET staffName=?, staffPhone=?, staffEmail=?, staffRole=?, staffStatus=? WHERE staffID=?");
        $stmt->bind_param("ssssss", $name, $phone, $email, $role, $status, $id);
    }

    // Execute the statement and set a session message based on the result
    if ($stmt->execute()) {
        $_SESSION['message'] = "Staff updated successfully!";
    } else {
        $_SESSION['message'] = "Failed to update staff: " . $stmt->error;
    }

    // Close the statement and redirect back to staff page
    $stmt->close();
    header("Location: staff.php");
    exit();
}
?>