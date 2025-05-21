<?php
// Include database connection
include "connection.php";

// Function to generate a new userID with format 'USR###'
function generateUserID($connect)
{
    // Get the latest userID starting with 'USR' in descending order
    $sql = "SELECT userID FROM users WHERE userID LIKE 'USR%' ORDER BY userID DESC LIMIT 1";
    $result = $connect->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastID = $row['userID'];
        // Extract numeric part and increment
        $number = (int) substr($lastID, 3);
        $newNumber = $number + 1;
    } else {
        // If no user found, start from 1
        $newNumber = 1;
    }
    // Return new ID with zero-padded 3 digits
    return 'USR' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}

// Function to convert string to Title Case
function titleCase($string)
{
    return ucwords(strtolower(trim($string)));
}

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and format inputs
    $name = titleCase($_POST['name']);
    $raw_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    // Remove non-digit characters from phone
    $tel = preg_replace("/\D/", "", $_POST['tel']);
    // Lowercase and trim email
    $email = strtolower(trim($_POST['email']));
    $role = 'Staff'; // Default role assigned

    // Validate inputs
    if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $error_message = "Name must contain only letters and spaces.";
    } elseif (!preg_match("/^\d{10,15}$/", $tel)) {
        $error_message = "Telephone must be 10-15 digits.";
    } elseif ($raw_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $raw_password)) {
        // Password must have uppercase, lowercase, number, symbol, min length 8
        $error_message = "Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.";
    } else {
        // Check if email already exists in DB
        $check_email_sql = "SELECT * FROM users WHERE userEmail = ?";
        $stmt = $connect->prepare($check_email_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result_email = $stmt->get_result();
        // Check if phone number already exists in DB
        $check_phone_sql = "SELECT * FROM users WHERE userPhone = ?";
        $stmt_phone = $connect->prepare($check_phone_sql);
        $stmt_phone->bind_param("s", $tel);
        $stmt_phone->execute();
        $result_phone = $stmt_phone->get_result();

        if ($result_email->num_rows > 0) {
            $error_message = "Email already exists.";
        } elseif ($result_phone->num_rows > 0) {
            $error_message = "Phone number already exists.";
        } else {
            // Generate new user ID
            $user_id = generateUserID($connect);
            // Hash password securely
            $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);
            // Insert new user into DB
            $insert_sql = "INSERT INTO users (userID, userName, userPassword, userPhone, userEmail, userRole) 
                           VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_insert = $connect->prepare($insert_sql);
            $stmt_insert->bind_param("ssssss", $user_id, $name, $hashed_password, $tel, $email, $role);

            if ($stmt_insert->execute()) {
                // Success message with user ID and login link
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
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <!-- External CSS and icons -->
    <link rel="stylesheet" href="../css/forms.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* Position the icon inside input fields */
        .input-group .icon {
            position: absolute;
            right: 15px;
            top: 10px;
            color: #333;
        }
    </style>
</head>

<body>
    <h2>Sign Up</h2>
    <div class="form-container">

        <!-- Display error or success messages -->
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
        <?php elseif (!empty($success_message)): ?>
            <p class="success-message"><?= $success_message ?></p>
        <?php endif; ?>

        <!-- Registration form -->
        <form action="signup.php" method="POST">
            <div class="input-group">
                <input type="text" name="name" placeholder="Full Name" required pattern="[A-Za-z\s]+"
                    title="Letters and spaces only">
                <span class="icon"><i class='bx bxs-user'></i></span>
            </div>

            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
                <span class="icon"><i class='bx bxs-envelope'></i></span>
            </div>

            <div class="input-group">
                <input type="tel" name="tel" placeholder="Telephone" required pattern="\d+" title="Digits only">
                <span class="icon"><i class='bx bxs-phone'></i></span>
            </div>

            <div class="input-group">
                <input id="password" type="password" name="password" placeholder="Password" required>
                <span class="icon toggle-password" style="cursor:pointer;">
                    <i class='bx bxs-hide'></i>
                </span>
                <!-- Password strength meter -->
                <div class="strength-meter">
                    <div class="strength-meter__fill" id="strengthBar"></div>
                </div>
                <div id="strengthMessage"></div>
            </div>

            <div class="input-group">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <span class="icon toggle-password" style="cursor:pointer;">
                    <i class='bx bxs-hide'></i>
                </span>
            </div>

            <button type="submit">Sign Up</button>
            <hr>
        </form>

        <p class="prompt">Already have an account? <a href="login.php">Login here</a></p>
    </div>
    <script src="../js/togglepass.js"></script> <!-- External JS for toggle password -->

    <script>
        // Password strength meter logic
        const pwdInput = document.getElementById('password');
        const msg = document.getElementById('strengthMessage');
        const bar = document.getElementById('strengthBar');

        pwdInput.addEventListener('input', () => {
            const val = pwdInput.value;
            let strength = 0;
            // Check for length, uppercase, lowercase, number, symbol
            if (val.length >= 8) strength++;
            if (/[A-Z]/.test(val)) strength++;
            if (/[a-z]/.test(val)) strength++;
            if (/[0-9]/.test(val)) strength++;
            if (/[\W_]/.test(val)) strength++;

            // Set meter width, color, and message according to strength
            let width = '0%';
            let color = '#e74c3c';
            let text = 'Weak password';
            let className = 'strength-weak';

            if (strength <= 2) {
                width = '25%';
            } else if (strength === 3 || strength === 4) {
                width = '60%';
                color = '#f1c40f';
                text = 'Medium strength';
                className = 'strength-medium';
            } else if (strength === 5) {
                width = '100%';
                color = '#2ecc71';
                text = 'Strong password';
                className = 'strength-strong';
            }

            bar.style.width = width;
            bar.style.backgroundColor = color;
            msg.textContent = text;
            msg.className = className;
        });
    </script>
</body>

</html>