<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<?php
include "connection.php"; // Include the database connection

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get input values from the form
    $user_id = $_POST['user_id']; // Changed staff_id to user_id
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if both password fields match
    if ($new_password === $confirm_password) {
        // Prepare SQL query to check if the User ID exists
        $sql = "SELECT * FROM users WHERE userID = ?"; // Changed staff to user
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("s", $user_id); // Bind the user ID
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User ID exists, update the password
            $update_sql = "UPDATE users SET userPassword = ? WHERE userID = ?"; // Changed staffPassword to userPassword
            $update_stmt = $connect->prepare($update_sql);
            $update_stmt->bind_param("ss", $new_password, $user_id); // Bind the new password and user ID
            $update_stmt->execute();

            // Check if the update was successful
            if ($update_stmt->affected_rows === 1) {
                $success_message = "Password successfully updated.";
            } else {
                $error_message = "Error updating password. Please try again.";
            }

            $update_stmt->close(); // Close the update statement
        } else {
            $error_message = "User ID not found."; // Changed Staff ID to User ID
        }

        $stmt->close(); // Close the check statement
    } else {
        $error_message = "Passwords do not match."; // Show error if passwords mismatch
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/forms.css">
</head>

<body>
    <h2>Forgot Password</h2>

    <div class="form-container">

        <!-- Show success or error message -->
        <?php
        if (isset($error_message))
            echo "<p class='error-message'>$error_message</p>";
        if (isset($success_message))
            echo "<p class='success-message'>$success_message</p>";
        ?>

        <!-- Forgot Password Form -->
        <form action="forgot_pass.php" method="POST">
            <div class="input-group">
                <input type="text" id="user_id" name="user_id" placeholder="User ID" required> <!-- Changed staff_id to user_id -->
                <span class="icon"><i class='bx bxs-user'></i></span>
            </div>
            <div class="input-group">
                <input type="password" id="new_password" name="new_password" placeholder="New Password" required>
                <span class="icon"><i class='bx bxs-lock'></i></span>
            </div>
            <div class="input-group">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password"
                    required>
                <span class="icon"><i class='bx bxs-lock'></i></span>
            </div>
            <button type="submit">Submit</button>
            <hr>
        </form>

        <p class="prompt">
            <a href="login.php">Back to login</a>
        </p>
    </div>

</body>

</html>
