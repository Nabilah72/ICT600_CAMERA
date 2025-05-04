<?php
session_start();

// If logout button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_unset();       // Remove all session variables
    session_destroy();     // Destroy the session
    header("Location: login.php"); // Redirect to login
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Settings</title>
</head>

<body>

    <?php include "../html/navbar.html"; ?>

    <div style="margin-left: 220px; padding: 20px;">
        <h2>Settings</h2>

        <form method="POST" action="settings.php">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>

</body>

</html>