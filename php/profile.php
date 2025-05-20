<?php
session_start();
include "connection.php";

if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];

$alertMessage = "";
if (isset($_SESSION['alertMessage'])) {
    $alertMessage = $_SESSION['alertMessage'];
    unset($_SESSION['alertMessage']); // clear after showing once
}

// Fetch user details
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
    <link rel="stylesheet" href="../css/crud.css" />
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .wrapper {
            display: flex;
        }

        /* Sidebar will be included here */
        .content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            min-height: 100vh;
            background-color: #f9f9f9;
        }

        .profile-wrapper {
            width: 700px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        h1 {
            font-weight: 700;
            font-size: 2.2rem;
            margin-bottom: 30px;
            color: #222;
        }

        .profile-box {
            width: 100%;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .profile-box img {
            border: 2px solid #ccc;
            border-radius: 50%;
            margin-bottom: 20px;
            width: 150px;
            height: 150px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .profile-box img:hover {
            transform: scale(1.05);
        }

        .profile-details {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .profile-row {
            display: flex;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }

        .profile-row .label {
            text-align: left;
            font-weight: 600;
            color: #333;
            width: 150px;
        }

        .profile-row .value {
            flex: 1;
            text-align: left;
            color: #555;
        }

        .form-actions {
            margin-top: 30px;
            text-align: center;
        }

        button[type="submit"],
        button#editBtn {
            width: 100px;
            padding: 10px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: #000;
            background-color: #ffc107;
            box-shadow: 0 4px 8px rgba(74, 144, 226, 0.4);
        }

        button[type="submit"]:hover,
        button#editBtn:hover {
            background-color: #FFD700;
        }

        #cancelBtn {
            color: white;
            background-color: #333;
            width: 100px;
            font-size: 14px;
        }

        #cancelBtn:hover {
            color: black;
            background-color: white;
            width: 100px;
            font-size: 14px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .modal-content {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            max-width: 450px;
            width: 100%;
            position: relative;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-content h2 {
            margin-bottom: 25px;
            font-weight: 700;
            color: #222;
        }

        label {
            text-align: left;
            display: block;
            margin-top: 15px;
            font-weight: 600;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            margin-top: 6px;
            border: 1.8px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="file"]:focus {
            outline: none;
            border-color: #ffca2c;
            box-shadow: 0 0 5px rgba(153, 153, 153, 0.5);
        }

        .form-actions button[type="button"]:hover {
            background-color: #b3b3b3;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            color: #444;
            margin-top: 10px;
        }

        .checkbox-wrapper label {
            margin: 0;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        #alertModal {
            z-index: 10000;
            /* higher than other modals */
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="content">
            <div class="profile-wrapper">
                <h1>My Profile</h1>
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
                <div class="form-actions">
                    <button id="editBtn">Edit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="closeModal">&times;</span>
            <h2>Edit Profile</h2>
            <form method="post" action="profile_crud.php" enctype="multipart/form-data">
                <input type="hidden" name="userID" value="<?= htmlspecialchars($user['userID']) ?>" />

                <label for="userName">Full Name:</label>
                <input type="text" id="userName" name="userName" value="<?= htmlspecialchars($user['userName']) ?>"
                    required />

                <label for="userPhone">Phone:</label>
                <input type="text" id="userPhone" name="userPhone" value="<?= htmlspecialchars($user['userPhone']) ?>"
                    required />

                <label for="userEmail">Email:</label>
                <input type="email" id="userEmail" name="userEmail" value="<?= htmlspecialchars($user['userEmail']) ?>"
                    required />

                <label for="userImage">Profile Image:</label>
                <input type="file" id="userImage" name="userImage" accept="image/*" />

                <?php if (!empty($user['userImage'])): ?>
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="removeImage" name="removeImage" value="1" />
                        <label for="removeImage">Remove current image</label>
                    </div>
                <?php endif; ?>


                <div class="form-actions">
                    <button type="submit" name="action" value="update">Save</button>
                    <button type="button" id="cancelBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>
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

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
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