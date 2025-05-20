<?php
session_start();
include "connection.php";

function titleCase($string)
{
    return ucwords(strtolower(trim($string)));
}

if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = $_POST['userID'];
    $name = titleCase($_POST['userName']);

    if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        header("Location: user.php?error=invalidName");
        exit;
    }

    $phone = preg_replace("/\D/", "", $_POST['userPhone']);
    $email = strtolower(trim($_POST['userEmail']));
    $role = trim($_POST['userRole']);
    $status = trim($_POST['userStatus']);
    $password = trim($_POST['userPassword']);

    if (!empty($password)) {
        // Hash the new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $connect->prepare("
            UPDATE users 
            SET userName = ?, userPhone = ?, userEmail = ?, userRole = ?, userStatus = ?, userPassword = ?
            WHERE userID = ?
        ");
        $stmt->bind_param("sssssss", $name, $phone, $email, $role, $status, $hashedPassword, $id);
    } else {
        $stmt = $connect->prepare("
            UPDATE users 
            SET userName = ?, userPhone = ?, userEmail = ?, userRole = ?, userStatus = ?
            WHERE userID = ?
        ");
        $stmt->bind_param("ssssss", $name, $phone, $email, $role, $status, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = "User updated successfully!";
    } else {
        $_SESSION['message'] = "Failed to update user: " . $stmt->error;
    }

    $stmt->close();
    header("Location: user.php");
    exit;
}
?>