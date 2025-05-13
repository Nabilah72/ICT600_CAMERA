<?php
session_start();
include "connection.php"; // Include database connection

function titleCase($string)
{
    return ucwords(strtolower(trim($string)));
}

// Handle "Update User" request
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    // Retrieve input values from the form
    $id = $_POST['userID']; // Change staffID to userID
    $name = titleCase(string: $_POST['userName']); // Title case (Change staffName to userName)
    if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        header("Location: user.php?error=invalidName"); // Change staff.php to user.php
        exit;
    }
    $phone = preg_replace("/\D/", "", $_POST['userPhone']); // Remove non-digits (Change staffPhone to userPhone)
    $email = strtolower(string: trim($_POST['userEmail'])); // Lowercase (Change staffEmail to userEmail)
    $role = trim($_POST['userRole']); // Change staffRole to userRole
    $status = trim($_POST['userStatus']); // Change staffStatus to userStatus
    $password = trim($_POST['userPassword']); // Change staffPassword to userPassword

    // Check if password is provided
    if (!empty($password)) {
        // Hash the new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement to update all user fields including password
        $stmt = $connect->prepare("UPDATE users SET userName=?, userPhone=?, userEmail=?, userRole=?, userStatus=?, userPassword=? WHERE userID=?"); // Change staff to user
        $stmt->bind_param("sssssss", $name, $phone, $email, $role, $status, $hashedPassword, $id); // Change staff to user
    } else {
        // Prepare SQL statement to update user fields without changing the password
        $stmt = $connect->prepare("UPDATE users SET userName=?, userPhone=?, userEmail=?, userRole=?, userStatus=? WHERE userID=?"); // Change staff to user
        $stmt->bind_param("ssssss", $name, $phone, $email, $role, $status, $id); // Change staff to user
    }

    // Execute the statement and set a session message based on the result
    if ($stmt->execute()) {
        $_SESSION['message'] = "User updated successfully!"; // Change staff to user
    } else {
        $_SESSION['message'] = "Failed to update user: " . $stmt->error; // Change staff to user
    }

    // Close the statement and redirect back to user page
    $stmt->close();
    header("Location: user.php"); // Change staff.php to user.php
    exit();
}
?>