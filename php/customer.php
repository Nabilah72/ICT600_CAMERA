<?php
session_start();
include "connection.php";

// Fetch all customer records from the database
$sql = "SELECT * FROM customer";
$result = $connect->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Management</title>
</head>

<body>

    <?php include "../html/navbar.html"; ?>

    <div class="container">
        <h1>Customer Management</h1>

        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addCustomerModal">Add New
            Customer</button>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['custID']) ?></td>
                            <td><?= htmlspecialchars($row['custName']) ?></td>
                            <td><?= htmlspecialchars($row['custPhone']) ?></td>
                            <td><?= htmlspecialchars($row['custEmail']) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm editBtn" data-bs-toggle="modal"
                                    data-bs-target="#editCustomerModal" data-id="<?= $row['custID'] ?>"
                                    data-name="<?= $row['custName'] ?>" data-phone="<?= $row['custPhone'] ?>"
                                    data-email="<?= $row['custEmail'] ?>">
                                    Edit
                                </button>
                                <a href="customer_crud.php?action=delete&id=<?= $row['custID'] ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this customer?');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="customer_crud.php" method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name"
                            class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone"
                            class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email"
                            class="form-control" required></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" value="add" class="btn btn-primary">Add Customer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="customer_crud.php" method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="editCustomerID">

                     <!-- Uneditable fields for Product ID -->
                     <div class="mb-3">
                        <label class="form-label">Customer ID</label>
                        <input type="text" id="editCustIDDisplay" class="form-control" disabled>
                    </div>
                    <!-- Editable fields -->
                    <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name"
                            id="editCustomerName" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone"
                            id="editCustomerPhone" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email"
                            id="editCustomerEmail" class="form-control" required></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Customer</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.querySelectorAll('.editBtn').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('editCustomerID').value = button.dataset.id;
                document.getElementById('editCustIDDisplay').value = button.dataset.id;
                document.getElementById('editCustomerName').value = button.dataset.name;
                document.getElementById('editCustomerPhone').value = button.dataset.phone;
                document.getElementById('editCustomerEmail').value = button.dataset.email;
            });
        });
    </script>
</body>

</html>