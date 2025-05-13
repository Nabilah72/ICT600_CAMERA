<?php

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
    <link rel="stylesheet" href="../css/crudd.css">
</head>

<body>
    <div class="wrapper">
        <?php include "sidebar.php"; ?>
        <div class="container">
            <h2>Settings</h2>

            <form method="POST" action="logout.php">
                <button style="background-color:red" type="submit" name="logout">Logout</button>
            </form>
        </div>
    </div>

</body>

</html>