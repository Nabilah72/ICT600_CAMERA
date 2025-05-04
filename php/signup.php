<?php
include "connection.php";

// Function to generate a unique staff ID starting with "STA"
function generateStaffID($connect)
{
    $sql = "SELECT staffID FROM staff WHERE staffID LIKE 'STA%' ORDER BY staffID DESC LIMIT 1";
    $result = $connect->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastID = $row['staffID'];
        $number = (int) substr($lastID, 3);
        $newNumber = $number + 1;
    } else {
        $newNumber = 1;
    }

    return 'STA' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}

function titleCase($string)
{
    return ucwords(strtolower(trim($string)));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = titleCase($_POST['name']);
    $password = $_POST['password'];
    $tel = preg_replace("/\D/", "", $_POST['tel']); // Remove non-numeric characters
    $email = strtolower(trim($_POST['email']));
    $role = 'Staff'; // Default role

    // Check for duplicate email
    $check_email_sql = "SELECT * FROM staff WHERE staffEmail = ?";
    $stmt = $connect->prepare($check_email_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result_email = $stmt->get_result();

    // Check for duplicate phone number
    $check_phone_sql = "SELECT * FROM staff WHERE staffPhone = ?";
    $stmt_phone = $connect->prepare($check_phone_sql);
    $stmt_phone->bind_param("s", $tel);
    $stmt_phone->execute();
    $result_phone = $stmt_phone->get_result();

    if ($result_email->num_rows > 0) {
        $error_message = "Email already exists.";
    } elseif ($result_phone->num_rows > 0) {
        $error_message = "Phone number already exists.";
    } else {
        $staff_id = generateStaffID($connect);

        $insert_sql = "INSERT INTO staff (staffID, staffName, staffPassword, staffPhone, staffEmail, staffRole) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $connect->prepare($insert_sql);
        $stmt_insert->bind_param("ssssss", $staff_id, $name, $password, $tel, $email, $role);

        if ($stmt_insert->execute()) {
            $success_message = "Registration successful. Your Staff ID is $staff_id. <a href='login.php'>Login</a>.";
        } else {
            $error_message = "Error during registration. Please try again.";
        }

        $stmt_insert->close();
    }

    $stmt->close();
    $stmt_phone->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="form-container">
        <h2>Sign Up</h2>

        <?php
        if (isset($error_message)) {
            echo "<p class='error-message'>$error_message</p>";
        }
        if (isset($success_message)) {
            echo "<p class='success-message'>$success_message</p>";
        }
        ?>

        <form action="signup.php" method="POST">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="tel">Tel:</label>
            <input type="tel" id="tel" name="tel" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <button type="submit">Sign Up</button>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>

</body>

</html>