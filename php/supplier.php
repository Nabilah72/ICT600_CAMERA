<?php
session_start();
include "connection.php";

// Check if the current user is an admin
$is_admin = (isset($_SESSION['staffRole']) && $_SESSION['staffRole'] === 'Admin');

// Fetch all supplier records from the database
$sql = "SELECT * FROM supplier";
$result = $connect->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Supplier Management</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <!-- Navigation Bar -->
    <?php include "../html/navbar.html"; ?>

    <div class="container">
        <h1>Supplier Management</h1>

        <!-- Show "Add New Supplier" button only if the user is an admin -->
        <?php if ($is_admin): ?>
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addSupplierModal">Add New
                Supplier</button>
        <?php endif; ?>

        <!-- Supplier Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Status</th>
                        <?php if ($is_admin): ?>
                            <th>Action</th> <!-- Edit/Delete actions shown only to admin -->
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loop through each supplier and display in the table -->
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['suppID']) ?></td>
                            <td><?= htmlspecialchars($row['suppName']) ?></td>
                            <td><?= htmlspecialchars($row['suppPhone']) ?></td>
                            <td><?= htmlspecialchars(string: $row['suppEmail']) ?></td>
                            <td><?= htmlspecialchars($row['suppAddress']) ?></td>
                            <td><?= htmlspecialchars($row['suppStatus']) ?></td>
                            <?php if ($is_admin): ?>
                                <td>
                                    <!-- Edit Button: Triggers Edit Modal with pre-filled data -->
                                    <button class="btn btn-warning btn-sm editBtn" data-bs-toggle="modal"
                                        data-bs-target="#editSupplierModal" data-id="<?= $row['suppID'] ?>"
                                        data-name="<?= $row['suppName'] ?>" data-phone="<?= $row['suppPhone'] ?>"
                                        data-email="<?= $row['suppEmail'] ?>" data-address="<?= $row['suppAddress'] ?>"
                                        data-suppStatus="<?= $row['suppStatus'] ?>">
                                        Edit
                                    </button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Supplier Modal (Only visible to Admins) -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="supplier_crud.php" method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Supplier input fields -->
                    <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name"
                            class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone"
                            class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email"
                            class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Address</label><textarea name="address"
                            class="form-control" required></textarea></div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="suppStatus" id="editSupplierStatus" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" value="add" class="btn btn-primary">Add Supplier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Supplier Modal (Pre-filled with selected supplier's data) -->
    <div class="modal fade" id="editSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="supplier_crud.php" method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Hidden fields to pass action and supplier ID -->
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="editSupplierID">
                    <!-- Uneditable fields for Supplier ID -->
                    <div class="mb-3">
                            <label class="form-label">Supplier ID</label>
                            <input type="text" id="editSuppIDDisplay" class="form-control" disabled>
                        </div>
                    <!-- Editable fields pre-filled via JavaScript -->
                    <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name"
                            id="editSupplierName" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone"
                            id="editSupplierPhone" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email"
                            id="editSupplierEmail" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Address</label><textarea name="address"
                            id="editSupplierAddress" class="form-control" required></textarea></div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="suppStatus" id="editSupplierStatus" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Supplier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript to populate Edit Supplier modal fields dynamically -->
    <script>
        document.querySelectorAll('.editBtn').forEach(button => {
            button.addEventListener('click', () => {
                // Fill modal form with supplier data from data attributes
                document.getElementById('editSupplierID').value = button.dataset.id;
                document.getElementById('editSuppIDDisplay').value = button.dataset.id;
                document.getElementById('editSupplierName').value = button.dataset.name;
                document.getElementById('editSupplierPhone').value = button.dataset.phone;
                document.getElementById('editSupplierEmail').value = button.dataset.email;
                document.getElementById('editSupplierAddress').value = button.dataset.address;
                document.getElementById('editSupplierStatus').value = button.dataset.suppStatus;
            });
        });
    </script>
</body>

</html>