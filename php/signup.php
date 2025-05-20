<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<?php
include "connection.php";

// Generate user ID starting with "USR"
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
    $raw_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $tel = preg_replace("/\D/", "", $_POST['tel']); // Allow only digits
    $email = strtolower(trim($_POST['email']));
    $role = 'Staff';

    // Validate name (only letters and spaces)
    if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $error_message = "Name must contain only letters and spaces.";
    }
    // Validate phone (only digits)
    elseif (!preg_match("/^\d{10,15}$/", $tel)) {
        $error_message = "Telephone must be 10-15 digits.";
    }
    // Check password match
    elseif ($raw_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    }
    // Check password strength
    elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $raw_password)) {
        $error_message = "Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.";
    } else {
        // Check duplicate email
        $check_email_sql = "SELECT * FROM users WHERE userEmail = ?";
        $stmt = $connect->prepare($check_email_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result_email = $stmt->get_result();

        // Check duplicate phone
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
            $user_id = generateUserID($connect);
            $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (userID, userName, userPassword, userPhone, userEmail, userRole) 
                           VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_insert = $connect->prepare($insert_sql);
            $stmt_insert->bind_param("ssssss", $user_id, $name, $hashed_password, $tel, $email, $role);

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
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../css/forms.css">
    <style>
        #strengthMessage {
            margin-top: 0;
            margin-left: 20px;
            text-align: left;
            font-size: 0.9em;
        }

        .strength-weak {
            color: #e74c3c;
        }

        .strength-medium {
            color: #f1c40f;
        }

        .strength-strong {
            color: #2ecc71;
        }
    </style>
</head>

<body>
    <h2>Sign Up</h2>
    <div class="form-container">
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
        <?php elseif (!empty($success_message)): ?>
            <p class="success-message"><?= $success_message ?></p>
        <?php endif; ?>

        <form action="signup.php" method="POST" novalidate>
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
                <span class="icon"><i class='bx bxs-lock'></i></span>
                <div id="strengthMessage"></div>
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

    <script>
        const pwdInput = document.getElementById('password');
        const msg = document.getElementById('strengthMessage');

        pwdInput.addEventListener('input', () => {
            const val = pwdInput.value;
            let strength = 0;
            if (val.length >= 8) strength++;
            if (/[A-Z]/.test(val)) strength++;
            if (/[a-z]/.test(val)) strength++;
            if (/[0-9]/.test(val)) strength++;
            if (/[\W_]/.test(val)) strength++;

            if (strength <= 2) {
                msg.textContent = 'Weak password';
                msg.className = 'strength-weak';
            } else if (strength === 3 || strength === 4) {
                msg.textContent = 'Medium strength';
                msg.className = 'strength-medium';
            } else if (strength === 5) {
                msg.textContent = 'Strong password';
                msg.className = 'strength-strong';
            } else {
                msg.textContent = '';
            }
        });
    </script>
</body>

</html>