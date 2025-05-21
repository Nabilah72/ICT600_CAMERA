<?php
// Start the session and include the database connection
session_start();
include "connection.php";

// Redirect to login if not logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];

// Handle alert message (e.g., profile updated)
$alertMessage = "";
if (isset($_SESSION['alertMessage'])) {
    $alertMessage = $_SESSION['alertMessage'];
    unset($_SESSION['alertMessage']); // Clear message after showing once
}

// Fetch user details from database
$sql = "SELECT * FROM users WHERE userID = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profile</title>
    <link rel="stylesheet" href="../css/cruds.css" />
    <link rel="stylesheet" href="../css/profile.css" />
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="content">
            <div class="profile-wrapper">
                <h1>My Profile</h1>
                <!-- Display user profile -->
                <div class="profile-box">
                    <img src="<?= $user['userImage'] ? '../uploads/users/' . htmlspecialchars($user['userImage']) : '../images/default.png' ?>"
                        alt="User Image">
                    <div class="profile-details">
                        <div class="profile-row">
                            <span class="label">User ID:</span>
                            <span class="value"><?= htmlspecialchars($user['userID']) ?></span>
                        </div>
                        <div class="profile-row">
                            <span class="label">Full Name:</span>
                            <span class="value"><?= htmlspecialchars($user['userName']) ?></span>
                        </div>
                        <div class="profile-row">
                            <span class="label">Phone:</span>
                            <span class="value"><?= htmlspecialchars($user['userPhone']) ?></span>
                        </div>
                        <div class="profile-row">
                            <span class="label">Email:</span>
                            <span class="value"><?= htmlspecialchars($user['userEmail']) ?></span>
                        </div>
                        <div class="profile-row">
                            <span class="label">Role:</span>
                            <span class="value"><?= htmlspecialchars($user['userRole']) ?></span>
                        </div>
                        <div class="profile-row">
                            <span class="label">Status:</span>
                            <span class="value"><?= htmlspecialchars($user['userStatus']) ?></span>
                        </div>
                    </div>
                </div>
                <!-- Button to trigger edit modal -->
                <div class="form-actions">
                    <button id="editBtn">Edit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="closeModal">&times;</span>
            <h2>Edit Profile</h2>
            <form method="post" action="profile_crud.php" enctype="multipart/form-data">
                <input type="hidden" name="userID" value="<?= htmlspecialchars($user['userID']) ?>" />

                <!-- Editable fields -->
                <label for="userName">Full Name:</label>
                <input type="text" id="userName" name="userName" value="<?= htmlspecialchars($user['userName']) ?>"
                    required />

                <label for="userPhone">Phone:</label>
                <input type="tel" id="userPhone" name="userPhone" value="<?= htmlspecialchars($user['userPhone']) ?>"
                    required />

                <label for="userEmail">Email:</label>
                <input type="email" id="userEmail" name="userEmail" value="<?= htmlspecialchars($user['userEmail']) ?>"
                    required />

                <!-- Image upload -->
                <label for="userImage">Profile Image:</label>
                <input type="file" id="userImage" name="userImage" accept="image/*" />

                <!-- Remove image option if an image is already uploaded -->
                <?php if (!empty($user['userImage'])): ?>
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="removeImage" name="removeImage" value="1" />
                        <label for="removeImage">Remove current image</label>
                    </div>
                <?php endif; ?>

                <!-- Modal buttons -->
                <div class="form-actions">
                    <button type="submit" name="action" value="update">Save</button>
                    <button type="button" id="cancelBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alert modal if any -->
    <?php if (!empty($alertMessage)): ?>
        <div class="modal" id="alertModal">
            <div class="modal-content">
                <p><?= htmlspecialchars($alertMessage) ?></p>
                <button class="btn" id="closeAlertBtn">Close</button>
            </div>
        </div>
    <?php endif; ?>

    <script>
        const editBtn = document.getElementById('editBtn');
        const modal = document.getElementById('editModal');
        const closeModalBtn = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const alertModal = document.getElementById('alertModal');
        const closeAlertBtn = document.getElementById('closeAlertBtn');

        // Open the edit modal
        editBtn.addEventListener('click', () => {
            modal.style.display = 'flex';
        });

        // Close the edit modal
        closeModalBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        cancelBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        // Handle alert modal if it exists
        if (alertModal) {
            alertModal.style.display = 'flex';
            closeAlertBtn.addEventListener('click', () => {
                alertModal.style.display = 'none';
            });
        }
    </script>
</body>

</html>