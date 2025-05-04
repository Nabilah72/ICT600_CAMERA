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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Include navigation bar -->
    <?php include "../html/navbar.html"; ?>

    <div class="container mt-4">
        <h1>Staff Management</h1>

        <!-- Staff table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Telephone</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <?php if ($is_admin): ?>
                            <th>Action</th> <!-- Only show Action column for admins -->
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Loop through each staff and display their details
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['staffID']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['staffName']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['staffPhone']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['staffEmail']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['staffRole']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['staffStatus']) . "</td>";

                            // Show Edit button if user is an admin
                            if ($is_admin) {
                                echo "<td>
                                    <button 
                                        class='btn btn-warning btn-sm editBtn'
                                        data-bs-toggle='modal' 
                                        data-bs-target='#editStaffModal'
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
                        // Show message if no staff records are found
                        echo "<tr><td colspan='" . ($is_admin ? "7" : "6") . "' class='text-center'>No staff records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Staff Modal - only visible to admins -->
    <?php if ($is_admin): ?>
        <div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="staff_crud.php" method="POST" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Staff</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Hidden fields to submit data -->
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="staffID" id="editStaffID">

                        <!-- Disabled field to display staff ID -->
                        <div class="mb-3">
                            <label class="form-label">Staff ID</label>
                            <input type="text" id="editStaffIDDisplay" class="form-control" disabled>
                        </div>

                        <!-- Editable staff fields -->
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="staffName" id="editStaffName" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="staffPhone" id="editStaffPhone" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="staffEmail" id="editStaffEmail" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="staffRole" id="editStaffRole" required>
                                <option value="Staff">Staff</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="staffStatus" id="editStaffStatus" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Modal footer with action buttons -->
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update Staff</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Bootstrap JS for modal functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Populate modal fields with staff data when Edit button is clicked -->
    <script>
        document.querySelectorAll('.editBtn').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('editStaffID').value = this.dataset.id;
                document.getElementById('editStaffIDDisplay').value = this.dataset.id;
                document.getElementById('editStaffName').value = this.dataset.name;
                document.getElementById('editStaffPhone').value = this.dataset.phone;
                document.getElementById('editStaffEmail').value = this.dataset.email;
                document.getElementById('editStaffRole').value = this.dataset.role;
                document.getElementById('editStaffStatus').value = this.dataset.status;
            });
        });
    </script>
</body>

</html>