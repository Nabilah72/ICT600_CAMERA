<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<?php
include "connection.php";

// Function to generate a unique user ID starting with "USR"
function generateUserID($connect)
{
    $sql = "SELECT userID FROM users WHERE userID LIKE 'USR%' ORDER BY userID DESC LIMIT 1";
    $result = $connect->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastID = $row['userID'];
        $number = (int) substr($lastID, 3);
        $newNumber = $number + 1;
    } else {
        $newNumber = 1;
    }

    return 'USR' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}

function titleCase($string)
{
    return ucwords(strtolower(trim($string)));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = titleCase($_POST['name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $tel = preg_replace("/\D/", "", $_POST['tel']);
    $email = strtolower(trim($_POST['email']));
    $role = 'Staff'; // Change to User role

    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check for duplicate email
        $check_email_sql = "SELECT * FROM users WHERE userEmail = ?"; // Change staffEmail to userEmail
        $stmt = $connect->prepare($check_email_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result_email = $stmt->get_result();

        // Check for duplicate phone number
        $check_phone_sql = "SELECT * FROM users WHERE userPhone = ?"; // Change staffPhone to userPhone
        $stmt_phone = $connect->prepare($check_phone_sql);
        $stmt_phone->bind_param("s", $tel);
        $stmt_phone->execute();
        $result_phone = $stmt_phone->get_result();

        if ($result_email->num_rows > 0) {
            $error_message = "Email already exists.";
        } elseif ($result_phone->num_rows > 0) {
            $error_message = "Phone number already exists.";
        } else {
            $user_id = generateUserID($connect); // Changed to generateUserID

            $insert_sql = "INSERT INTO users (userID, userName, userPassword, userPhone, userEmail, userRole) 
                           VALUES (?, ?, ?, ?, ?, ?)"; // Change staff to user
            $stmt_insert = $connect->prepare($insert_sql);
            $stmt_insert->bind_param("ssssss", $user_id, $name, $password, $tel, $email, $role); // Change staff to user

            if ($stmt_insert->execute()) {
                $success_message = "Registration successful. Your User ID is $user_id. <a href='login.php'>Login</a>.";
            } else {
                $error_message = "Error during registration. Please try again.";
            }

            $stmt_insert->close();
        }

        $stmt->close();
        $stmt_phone->close();
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Sign Up</title>
    <link rel="stylesheet" href="../css/forms.css">
</head>

<body>
    <h2>Sign Up</h2>

    <div class="form-container">

        <?php
        if (isset($error_message)) {
            echo "<p class='error-message'>$error_message</p>";
        }
        if (isset($success_message)) {
            echo "<p class='success-message'>$success_message</p>";
        }
        ?>

        <form action="signup.php" method="POST">
            <div class="input-group">
                <input type="text" name="name" placeholder="Full Name" required>
                <span class="icon"><i class='bx bxs-user'></i></span>
            </div>

            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
                <span class="icon"><i class='bx bxs-envelope'></i></span>
            </div>

            <div class="input-group">
                <input type="tel" name="tel" placeholder="Telephone" required>
                <span class="icon"><i class='bx bxs-phone'></i></span>
            </div>

            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
                <span class="icon"><i class='bx bxs-lock'></i></span>
            </div>

            <div class="input-group">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <span class="icon"><i class='bx bxs-lock'></i></span>
            </div>

            <button type="submit">Sign Up</button>
            <hr>
        </form>

        <p class="prompt">Already have an account?
            <a href="login.php">Login here</a>
        </p>
    </div>

</body>

</html>