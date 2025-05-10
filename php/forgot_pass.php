<?php
include "connection.php"; // Include the database connection

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get input values from the form
    $staff_id = $_POST['staff_id'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if both password fields match
    if ($new_password === $confirm_password) {
        // Prepare SQL query to check if the Staff ID exists
        $sql = "SELECT * FROM staff WHERE staffID = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("s", $staff_id); // Bind the staff ID
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Staff ID exists, update the password
            $update_sql = "UPDATE staff SET staffPassword = ? WHERE staffID = ?";
            $update_stmt = $connect->prepare($update_sql);
            $update_stmt->bind_param("ss", $new_password, $staff_id); // Bind the new password and staff ID
            $update_stmt->execute();

            // Check if the update was successful
            if ($update_stmt->affected_rows === 1) {
                $success_message = "Password successfully updated.";
            } else {
                $error_message = "Error updating password. Please try again.";
            }

            $update_stmt->close(); // Close the update statement
        } else {
            $error_message = "Staff ID not found.";
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
    <link rel="stylesheet" href="../css/form.css">
</head>

<body>

    <div class="form-container">
        <h2>Forgot Password</h2>

        <!-- Show success or error message -->
        <?php
        if (isset($error_message))
            echo "<p class='error-message'>$error_message</p>";
        if (isset($success_message))
            echo "<p class='success-message'>$success_message</p>";
        ?>

        <!-- Forgot Password Form -->
        <form action="forgot_pass.php" method="POST">
            <label for="staff_id">Staff ID:</label>
            <input type="text" id="staff_id" name="staff_id" required><br>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required><br>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>

            <button type="submit">Submit</button>
        </form>

        <p><a href="login.php">Back to Login</a></p>
    </div>

</body>

</html>