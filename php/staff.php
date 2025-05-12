<?php
session_start();
include "connection.php";

// Check if the user is an admin
$is_admin = (isset($_SESSION['staffRole']) && $_SESSION['staffRole'] === 'Admin');

// Fetch all staff records from the database
$sql = "SELECT * FROM staff";
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
    <title>Staff Management</title>
    <link rel="stylesheet" href="../css/crudd.css">
</head>

<body>

    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="container">
            <h1>Staff Management</h1>
            <input type="text" id="searchInput" placeholder="Search staff..." class="search-box">
            <table>
                <thead>
                    <tr>
                        <th class="sortable">No.</th>
                        <th class="sortable">ID</th>
                        <th class="sortable">Full Name</th>
                        <th class="sortable">Telephone</th>
                        <th class="sortable">Email</th>
                        <th class="sortable">Role</th>
                        <th class="sortable">Status</th>
                        <?php if ($is_admin): ?>
                            <th>Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['staffID']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['staffName']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['staffPhone']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['staffEmail']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['staffRole']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['staffStatus']) . "</td>";

                            if ($is_admin) {
                                echo "<td>
                                <button 
                                    class='btn btn-warning editBtn'
                                    data-id='{$row['staffID']}'
                                    data-name='{$row['staffName']}'
                                    data-phone='{$row['staffPhone']}'
                                    data-email='{$row['staffEmail']}'
                                    data-role='{$row['staffRole']}'
                                    data-status='{$row['staffStatus']}'>
                                    Edit
                                </button>
                            </td>";
                            }

                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='" . ($is_admin ? "7" : "6") . "' class='text-center'>No staff records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($is_admin): ?>
        <!-- Reusable Modal -->
        <div class="popup-modal" id="popupModal">
            <div class="popup-content">
                <span class="close-btn" id="closeModal">&times;</span>
                <h2>Edit Staff</h2>
                <form action="staff_crud.php" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="staffID" id="editStaffID">

                    <div class="form-group">
                        <label>Staff ID</label>
                        <input type="text" id="editStaffIDDisplay" disabled>
                    </div>

                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="staffName" id="editStaffName" required>
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="staffPhone" id="editStaffPhone" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="staffEmail" id="editStaffEmail" required>
                    </div>

                    <div class="form-group">
                        <label>Role</label>
                        <select name="staffRole" id="editStaffRole" required>
                            <option value="Staff">Staff</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="staffStatus" id="editStaffStatus" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button class="blueBtn" type="submit">Save Changes</button>
                        <button type="button" id="cancelModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
    <script src="../js/searchsort.js"></script>

    <script>
        const popupModal = document.getElementById('popupModal');
        const closeModalBtn = document.getElementById('closeModal');
        const cancelModalBtn = document.getElementById('cancelModal');

        const editFields = {
            id: document.getElementById('editStaffID'),
            idDisplay: document.getElementById('editStaffIDDisplay'),
            name: document.getElementById('editStaffName'),
            phone: document.getElementById('editStaffPhone'),
            email: document.getElementById('editStaffEmail'),
            role: document.getElementById('editStaffRole'),
            status: document.getElementById('editStaffStatus'),
        };

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
            }, 300); // Match with the CSS transition duration
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

        closeModalBtn.addEventListener('click', closeModal);
        cancelModalBtn.addEventListener('click', closeModal);

        window.addEventListener('click', (e) => {
            if (e.target === popupModal) closeModal();
        });
    </script>
</body>

</html>