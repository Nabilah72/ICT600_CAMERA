<?php
// Start the session and include the database connection
session_start();
include "connection.php";

// Initialize a message variable to display feedback
$message = "";
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear the message after displaying
}

// Fetch all user records from the database
$sql = "SELECT * FROM users";
$result = $connect->query($sql);

// Handle query error
if (!$result) {
    die("Query failed: " . $connect->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <!-- Link to external for CSS -->
    <link rel="stylesheet" href="../css/cruds.css">
</head>

<body>
    <div class="wrapper">
        <!-- Include sidebar navigation -->
        <?php include 'sidebar.php'; ?>
        <div class="container">
            <h1>User Management</h1>
            <!-- Search box for filtering user table -->
            <input type="text" id="searchInput" placeholder="Search User..." class="search-box">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th class="sortable">No.</th>
                            <th class="sortable">User ID</th>
                            <th class="sortable">Full Name</th>
                            <th class="sortable">Telephone</th>
                            <th class="sortable">Email</th>
                            <th class="sortable">Role</th>
                            <th class="sortable">Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Display user records dynamically
                        if ($result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . htmlspecialchars($row['userID']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['userName']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['userPhone']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['userEmail']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['userRole']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['userStatus']) . "</td>";

                                // Button for editing user details
                                echo "<td>
                                <button 
                                    class='btn btn-warning editBtn'
                                    data-id='{$row['userID']}'
                                    data-name='{$row['userName']}'
                                    data-phone='{$row['userPhone']}'
                                    data-email='{$row['userEmail']}'
                                    data-role='{$row['userRole']}'
                                    data-status='{$row['userStatus']}'>
                                    Edit
                                </button>
                            </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>No user records found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for editing user -->
    <div class="popup-modal" id="popupModal">
        <div class="popup-content">
            <!-- Close modal button -->
            <span class="close-btn" id="closeModal">&times;</span>
            <h2>Edit User Details</h2>
            <!-- Form to submit updated user details -->
            <form action="user_crud.php" method="POST">
                <!-- Hidden fields to send action type and user ID -->
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="userID" id="editUserID">

                <!-- Display User ID (read-only) -->
                <div class="form-group">
                    <label>User ID</label>
                    <input type="text" id="editUserIDDisplay" disabled>
                </div>

                <!-- Full Name (read-only) -->
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="userName" id="editUserName" readonly>
                </div>

                <!-- Phone (read-only) -->
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="userPhone" id="editUserPhone" readonly>
                </div>

                <!-- Email (read-only) -->
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="userEmail" id="editUserEmail" readonly>
                </div>

                <!-- Role selection (editable) -->
                <div class="form-group">
                    <label>Role</label>
                    <select name="userRole" id="editUserRole" required>
                        <option value="Staff">Staff</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>

                <!-- Status selection (editable) -->
                <div class="form-group">
                    <label>Status</label>
                    <select name="userStatus" id="editUserStatus" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <!-- Submit and cancel buttons -->
                <div class="form-actions">
                    <button class="blueBtn" type="submit">Save Changes</button>
                    <button type="button" id="cancelModal">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Display success/failure message as modal -->
    <?php if (!empty($message)): ?>
        <div class="modal" id="alertModal" style="display: flex;">
            <div class="modal-content">
                <p><?php echo htmlspecialchars($message); ?></p>
                <button class="btn" id="closeAlertBtn">Close</button>
            </div>
        </div>
    <?php endif; ?>

    <!-- JavaScript for search/sort table -->
    <script src="../js/searchsort.js"></script>

    <script>
        // DOM references for modal and form fields
        const popupModal = document.getElementById('popupModal');
        const closeModalBtn = document.getElementById('closeModal');
        const cancelModalBtn = document.getElementById('cancelModal');

        const editFields = {
            id: document.getElementById('editUserID'),
            idDisplay: document.getElementById('editUserIDDisplay'),
            name: document.getElementById('editUserName'),
            phone: document.getElementById('editUserPhone'),
            email: document.getElementById('editUserEmail'),
            role: document.getElementById('editUserRole'),
            status: document.getElementById('editUserStatus'),
        };

        // Show the modal and fill the fields with user data
        function showModal(data) {
            popupModal.style.display = 'flex';
            setTimeout(() => popupModal.classList.add('show'), 10);

            editFields.id.value = data.id;
            editFields.idDisplay.value = data.id;
            editFields.name.value = data.name;
            editFields.phone.value = data.phone;
            editFields.email.value = data.email;
            editFields.role.value = data.role;
            editFields.status.value = data.status;
        }

        // Hide and reset modal
        function closeModal() {
            popupModal.classList.remove('show');
            setTimeout(() => {
                popupModal.style.display = 'none';
                // Reset all form fields after the modal fades out
                Object.values(editFields).forEach(field => {
                    if (field.tagName === 'INPUT' || field.tagName === 'SELECT') {
                        field.value = '';
                    }
                });
            }, 300); // Match with CSS animation delay
        }


        // Attach click event to each edit button
        document.querySelectorAll('.editBtn').forEach(button => {
            button.addEventListener('click', () => {
                showModal({
                    id: button.dataset.id,
                    name: button.dataset.name,
                    phone: button.dataset.phone,
                    email: button.dataset.email,
                    role: button.dataset.role,
                    status: button.dataset.status
                });
            });
        });

        // Attach modal close behavior
        closeModalBtn.addEventListener('click', closeModal);
        cancelModalBtn.addEventListener('click', closeModal);

        // Close modal if user clicks outside the popup
        window.addEventListener('click', (e) => {
            if (e.target === popupModal) closeModal();
        });

        // Close alert modal
        const alertBtn = document.getElementById('closeAlertBtn');
        const alertModal = document.getElementById('alertModal');
        if (alertBtn && alertModal) {
            alertBtn.addEventListener('click', () => {
                alertModal.style.display = 'none';
            });
        }
    </script>

</body>

</html>