<?php
session_start();
include "connection.php";

if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update') {
    $userID = $_POST['userID'];
    $userName = $_POST['userName'];
    $userPhone = $_POST['userPhone'];
    $userEmail = $_POST['userEmail'];
    $removeImage = isset($_POST['removeImage']) ? true : false;

    // Get current image
    $stmt = $connect->prepare("SELECT userImage FROM users WHERE userID = ?");
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $currentImage = $user['userImage'];
    $stmt->close();

    // Handle image upload
    $newImage = $currentImage;
    if (isset($_FILES['userImage']) && $_FILES['userImage']['error'] === UPLOAD_ERR_OK) {
        $imgName = basename($_FILES["userImage"]["name"]);
        $targetDir = "../uploads/users/";
        $targetFile = $targetDir . time() . "_" . $imgName;

        if (move_uploaded_file($_FILES["userImage"]["tmp_name"], $targetFile)) {
            // Delete old image if exists and not default
            if ($currentImage && file_exists($targetDir . $currentImage)) {
                unlink($targetDir . $currentImage);
            }
            $newImage = basename($targetFile);
        }
    }

    // Handle image removal
    if ($removeImage && $currentImage && file_exists("../uploads/users/" . $currentImage)) {
        unlink("../uploads/users/" . $currentImage);
        $newImage = "";
    }

    // Update database
    $stmt = $connect->prepare("UPDATE users SET userName = ?, userPhone = ?, userEmail = ?, userImage = ? WHERE userID = ?");
    $stmt->bind_param("sssss", $userName, $userPhone, $userEmail, $newImage, $userID);

    if ($stmt->execute()) {
        $_SESSION['alertMessage'] = "Profile updated successfully";
    } else {
        $_SESSION['error_message'] = "Failed to update profile.";
    }

    $stmt->close();
}

header("Location: profile.php");
exit();
?>