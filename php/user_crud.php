<?php
// Start the session and include the database connection
session_start();
include "connection.php";

// Function to convert a string to title case (capitalize each word)
function titleCase($string)
{
    return ucwords(strtolower(trim($string)));
}

// Check if the request is to update a user
if (isset($_POST['action']) && $_POST['action'] === 'update') {

    $id = $_POST['userID'];  // User ID for the record to be updated
    $name = titleCase($_POST['userName']);  // Convert name to title case

    // Validate name using regular expression (only letters and spaces allowed)
    if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        // Redirect back with error if name is invalid
        header("Location: user.php?error=invalidName");
        exit;
    }

    // Remove all non-digit characters from phone number
    $phone = preg_replace("/\D/", "", $_POST['userPhone']);

    // Convert email to lowercase and trim whitespace
    $email = strtolower(trim($_POST['userEmail']));

    // Get role, status, and password inputs, trimmed of extra spaces
    $role = trim($_POST['userRole']);
    $status = trim($_POST['userStatus']);
    $password = trim($_POST['userPassword']);

    // Check if password is provided
    if (!empty($password)) {
        // Hash the new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // Prepare SQL statement with password update
        $stmt = $connect->prepare("
            UPDATE users 
            SET userName = ?, userPhone = ?, userEmail = ?, userRole = ?, userStatus = ?, userPassword = ?
            WHERE userID = ?
        ");
        // Bind parameters to the prepared statement
        $stmt->bind_param("sssssss", $name, $phone, $email, $role, $status, $hashedPassword, $id);
    } else {
        // Prepare SQL statement without password update
        $stmt = $connect->prepare("
            UPDATE users 
            SET userName = ?, userPhone = ?, userEmail = ?, userRole = ?, userStatus = ?
            WHERE userID = ?
        ");
        // Bind parameters to the prepared statement
        $stmt->bind_param("ssssss", $name, $phone, $email, $role, $status, $id);
    }

    // Execute the prepared statement and check if update was successful
    if ($stmt->execute()) {
        // Set success message in session
        $_SESSION['message'] = "User updated successfully!";
    } else {
        // Set error message with specific error detail
        $_SESSION['message'] = "Failed to update user: " . $stmt->error;
    }

    // Close the statement to free resources
    $stmt->close();

    // Redirect back to the user management page
    header("Location: user.php");
    exit;
}
?>