<?php
session_start(); // Start session to store user login information
include "connection.php"; // Include the database connection

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get values from form input
    $staff_id = $_POST['staff_id'];
    $password = $_POST['password'];

    // Prepare SQL to prevent SQL injection
    $sql = "SELECT * FROM staff WHERE staffID = ? AND staffPassword = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ss", $staff_id, $password); // "ss" means both values are strings
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists and password matches
    if ($result->num_rows === 1) {
        // Login successful
        $row = $result->fetch_assoc(); // Fetch the row from the result
        $_SESSION['staff_id'] = $staff_id; // Store staff ID in session
        $_SESSION['staffName'] = $row['staffName']; // Store staff name in session
        $_SESSION['staffRole'] = $row['staffRole']; // Store the role here
        header("Location: ../html/homepage.html"); // Redirect to homepage
        exit();
    } else {
        // Invalid credentials
        $error_message = "Invalid Staff ID or Password.";
    }

    $stmt->close(); // Close the statement
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="../css/form.css">
</head>

<body>

    <div class="form-container">
        <h2>Login</h2>

        <!-- Display error message if login fails -->
        <?php if (isset($error_message))
            echo "<p class='error-message'>$error_message</p>"; ?>

        <!-- Login Form -->
        <form action="login.php" method="POST">
            <label for="staff_id">Staff ID:</label>
            <input type="text" id="staff_id" name="staff_id" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <button type="submit">Login</button>
        </form>

        <p><a href="forgot_pass.php">Forgot Password?</a></p>
        <p>Don't have an account? <a href="signup.php">Sign Up here</a>.</p>
    </div>

</body>

</html>