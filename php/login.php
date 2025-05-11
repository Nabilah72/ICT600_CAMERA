<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<?php
session_start();
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_id = $_POST['staff_id'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM staff WHERE staffID = ? AND staffPassword = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ss", $staff_id, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['staff_id'] = $staff_id;
        $_SESSION['staffName'] = $row['staffName'];
        $_SESSION['staffRole'] = $row['staffRole'];
        header("Location: ../html/homepage.html");
        exit();
    } else {
        $error_message = "Invalid Staff ID or Password.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/forms.css">
</head>

<body>
    <h2 class="page-title">Login</h2>
    <div class="form-container">
        <?php if (isset($error_message))
            echo "<p class='error-message'>$error_message</p>"; ?>

        <form action="login.php" method="POST">
            <div class="input-group">
                <input type="text" name="staff_id" placeholder="Staff ID" required>
                <span class="icon"><i class='bx bxs-user'></i></span>
            </div>

            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
                <span class="icon"><i class='bx bxs-lock'></i></span>
            </div>

            <div class="link-right">
                <a href="forgot_pass.php">Forgot Password?</a>
            </div>

            <button type="submit">Login</button>
            <hr>
        </form>

        <p class="prompt">Don't have an account?
            <a href="signup.php">Sign Up here</a>
        </p>
    </div>
</body>

</html>