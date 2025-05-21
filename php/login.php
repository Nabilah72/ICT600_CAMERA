<?php
// Start the session and include the database connection
session_start();
include "connection.php";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['staff_id'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE userID = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        // Verify hashed password
        if (password_verify($password, $row['userPassword'])) {
            $_SESSION['staff_id'] = $user_id;
            $_SESSION['staffName'] = $row['userName'];
            $_SESSION['userRole'] = $row['userRole'];
            header("Location: ../php/homepage.php");
            exit();
        } else {
            $error_message = "Invalid User ID or Password.";
        }
    } else {
        $error_message = "Invalid User ID or Password.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <!-- External CSS and icons -->
    <link rel="stylesheet" href="../css/forms.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <h2 class="page-title">Login</h2>
    <!-- Show error message if login fails -->
    <div class="form-container">
        <?php if (isset($error_message))
            echo "<p class='error-message'>$error_message</p>"; ?>

        <!-- Login form -->
        <form action="login.php" method="POST">
            <div class="input-group">
                <input type="text" name="staff_id" placeholder="User ID" required> <!-- Change Staff ID to User ID -->
                <span class="icon"><i class='bx bxs-user'></i></span>
            </div>

            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
                <span class="icon toggle-password" style="cursor:pointer;">
                    <i class='bx bxs-hide'></i>
                </span>
            </div>

            <!-- Forgot password link -->
            <div class="link-right">
                <a href="forgot_pass.php">Forgot Password?</a>
            </div>

            <!-- Submit button -->
            <button type="submit">Login</button>
            <hr>
        </form>

        <!-- Sign up prompt -->
        <p class="prompt">Don't have an account?
            <a href="signup.php">Sign Up here</a>
        </p>
    </div>
    <script src="../js/togglepass.js"></script> <!-- External JS for toggle password -->
</body>

</html>