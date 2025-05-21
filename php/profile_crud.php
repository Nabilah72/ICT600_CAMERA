<?php
// Start session and include database connection
session_start();
include "connection.php";

// Redirect to login if staff is not logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the form is submitted with POST method and 'update' action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update') {
    $userID = $_POST['userID'];
    $userName = $_POST['userName'];
    $userPhone = $_POST['userPhone'];
    $userEmail = $_POST['userEmail'];
    $removeImage = isset($_POST['removeImage']) ? true : false;

    // Get the current user image from the database
    $stmt = $connect->prepare("SELECT userImage FROM users WHERE userID = ?");
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $currentImage = $user['userImage'];
    $stmt->close();

    // Default to current image
    $newImage = $currentImage;

    // Check if a new image is uploaded
    if (isset($_FILES['userImage']) && $_FILES['userImage']['error'] === UPLOAD_ERR_OK) {
        $imgName = basename($_FILES["userImage"]["name"]);
        $targetDir = "../uploads/users/";
        $targetFile = $targetDir . time() . "_" . $imgName;

        // Move uploaded file to the target directory
        if (move_uploaded_file($_FILES["userImage"]["tmp_name"], $targetFile)) {
            // If current image exists and is not default, delete it
            if ($currentImage && file_exists($targetDir . $currentImage)) {
                unlink($targetDir . $currentImage);
            }

            // Set the new image name to be saved in the database
            $newImage = basename($targetFile);
        }
    }

    // Handle image removal if requested
    if ($removeImage && $currentImage && file_exists("../uploads/users/" . $currentImage)) {
        unlink("../uploads/users/" . $currentImage); // Delete image file
        $newImage = "";  // Clear image field in database
    }

    // Execute the update statement
    $stmt = $connect->prepare("UPDATE users SET userName = ?, userPhone = ?, userEmail = ?, userImage = ? WHERE userID = ?");
    $stmt->bind_param("sssss", $userName, $userPhone, $userEmail, $newImage, $userID);

    // Check if the update was successful
    if ($stmt->execute()) {
        $_SESSION['alertMessage'] = "Profile updated successfully";
    } else {
        $_SESSION['error_message'] = "Failed to update profile.";
    }

    $stmt->close();
}
// Redirect back to profile page after processing
header("Location: profile.php");
exit();
?>