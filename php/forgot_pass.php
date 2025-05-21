<?php
include "connection.php"; // DB connection

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = trim($_POST['user_id']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Server-side password strength validation
    $length_ok = strlen($new_password) >= 8;
    $upper_ok = preg_match('/[A-Z]/', $new_password);
    $number_ok = preg_match('/\d/', $new_password);
    $special_ok = preg_match('/[\W_]/', $new_password);

    if (!($length_ok && $upper_ok && $number_ok && $special_ok)) {
        $errors[] = "Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.";
    }

    // If no validation errors
    if (empty($errors)) {
        // verify user exists
        $stmt = $connect->prepare("SELECT 1 FROM users WHERE userID = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            // Hash and update new password
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $upd = $connect->prepare("UPDATE users SET userPassword = ? WHERE userID = ?");
            $upd->bind_param("ss", $hashed, $user_id);
            $upd->execute();

            if ($upd->affected_rows === 1) {
                $success_message = "Password successfully updated.";
            } else {
                $errors[] = "Error updating password. Please try again.";
            }
            $upd->close();
        } else {
            $errors[] = "User ID not found.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <!-- Link to external CSS files -->
    <link rel="stylesheet" href="../css/forms.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .strength-weak {
            background-color: #e74c3c;
        }

        .strength-medium {
            background-color: #f1c40f;
        }

        .strength-strong {
            background-color: #2ecc71;
        }
    </style>
</head>

<body>
    <h2>Forgot Password</h2>
    <div class="form-container">

        <!--  Show errors or success -->
        <?php foreach ($errors as $e): ?>
            <p class="error-message"><?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
        <?php if ($success_message): ?>
            <p class="success-message"><?= htmlspecialchars($success_message) ?></p>
        <?php endif; ?>

        <!--  Password reset form -->
        <form action="forgot_pass.php" method="POST">
            <div class="input-group">
                <input type="text" name="user_id" placeholder="User ID" required>
                <span class="icon"><i class='bx bxs-user'></i></span>
            </div>

            <!-- New password with strength meter -->
            <div class="input-group">
                <input id="new_password" type="password" name="new_password" placeholder="New Password" required>
                <span class="icon toggle-password" style="cursor:pointer;">
                    <i class='bx bxs-hide'></i>
                </span>
                <div class="strength-meter" id="strengthMeter">
                    <div class="strength-meter__fill" id="strengthFill"></div>
                </div>
                <div class="strength-text" id="strengthText"></div>
            </div>

            <!-- Confirm password -->
            <div class="input-group">
                <input id="confirm_password" type="password" name="confirm_password" placeholder="Confirm Password"
                    required>
                <span class="icon toggle-password" style="cursor:pointer;">
                    <i class='bx bxs-hide'></i>
                </span>
            </div>

            <button type="submit">Submit</button>
        </form>

        <p class="prompt"><a href="login.php">Back to login</a></p>
    </div>
    <script src="../js/togglepass.js"></script> <!-- External JS for toggle password -->
    <script>
        // Password strength logic
        const meter = document.getElementById('strengthMeter');
        const fill = document.getElementById('strengthFill');
        const text = document.getElementById('strengthText');
        const pwd = document.getElementById('new_password');

        pwd.addEventListener('input', () => {
            const val = pwd.value;
            let score = 0;
            if (val.length >= 8) score += 1;
            if (/[A-Z]/.test(val)) score += 1;
            if (/\d/.test(val)) score += 1;
            if (/[\W_]/.test(val)) score += 1;

            // Update strength bar
            fill.style.width = (score / 4 * 100) + '%';
            fill.className = 'strength-meter__fill ' + (
                score <= 1 ? 'strength-weak' :
                    score == 2 ? 'strength-medium' :
                        'strength-strong'
            );

            // Update text description
            text.textContent =
                score <= 1 ? 'Very Weak' :
                    score == 2 ? 'Weak' :
                        score == 3 ? 'Good' :
                            'Strong';
        });
    </script>
</body>

</html>