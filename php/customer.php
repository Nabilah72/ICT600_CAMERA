<?php
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
    <?php include 'alert.php'; ?>
    <link rel="stylesheet" href="../css/cruds.css">
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="container">
            <h1>Customer Management</h1>
            <input type="text" id="searchInput" placeholder="Search customer..." class="search-box"><br>

            <button id="openAddModal">Add Customer</button><br><br>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th class="sortable">No.</th>
                            <th class="sortable">ID</th>
                            <th class="sortable">Name</th>
                            <th class="sortable">Phone</th>
                            <th class="sortable">Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['custID']) ?></td>
                                <td><?= htmlspecialchars($row['custName']) ?></td>
                                <td><?= htmlspecialchars($row['custPhone']) ?></td>
                                <td><?= htmlspecialchars($row['custEmail']) ?></td>
                                <td>
                                    <button class="editBtn" data-id="<?= $row['custID'] ?>"
                                        data-name="<?= $row['custName'] ?>" data-phone="<?= $row['custPhone'] ?>"
                                        data-email="<?= $row['custEmail'] ?>">
                                        Edit
                                    </button>

                                    <form action="customer_crud.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $row['custID'] ?>">
                                        <button type="submit" class="deleteBtn"
                                            onclick="return confirm('Delete this product?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="popup-modal" id="addModal">
        <div class="popup-content">
            <span class="close-btn" id="closeAdd">&times;</span>
            <h2>Add Customer</h2>
            <form action="customer_crud.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group"> <label>Name <span class="required">*</span></label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group"> <label>Phone <span class="required">*</span></label>
                    <input type="text" name="phone" required>
                </div>
                <div class="form-group"> <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="blueBtn">Add Customer</button>
                    <button type="button" id="cancelAdd">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div class="popup-modal" id="editModal">
        <div class="popup-content">
            <span class="close-btn" id="closeEdit">&times;</span>
            <h2>Edit Customer Details</h2>
            <form action="customer_crud.php" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editCustomerID">
                <div class="form-group">
                    <label>Customer ID</label>
                    <input type="text" id="editCustIDDisplay" disabled>
                </div>

                <div class="form-group"><label>Name</label><input type="text" name="name" id="editName" required></div>
                <div class="form-group"><label>Phone</label><input type="text" name="phone" id="editPhone" required>
                </div>
                <div class="form-group"><label>Email</label><input type="email" name="email" id="editEmail" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="blueBtn">Save Changes</button>
                    <button type="button" id="cancelEdit">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <script src="../js/searchsort.js"></script>

    <script>
        const addModal = document.getElementById('addModal');
        const editModal = document.getElementById('editModal');

        document.getElementById('openAddModal').onclick = () => addModal.classList.add('show');
        document.getElementById('closeAdd').onclick = () => addModal.classList.remove('show');
        document.getElementById('cancelAdd').onclick = () => addModal.classList.remove('show');

        document.querySelectorAll('.editBtn').forEach(btn => {
            btn.onclick = () => {
                document.getElementById('editCustomerID').value = btn.dataset.id;
                document.getElementById('editCustIDDisplay').value = btn.dataset.id;
                document.getElementById('editName').value = btn.dataset.name;
                document.getElementById('editPhone').value = btn.dataset.phone;
                document.getElementById('editEmail').value = btn.dataset.email;
                editModal.classList.add('show');
            };
        });

        document.getElementById('closeEdit').onclick = () => editModal.classList.remove('show');
        document.getElementById('cancelEdit').onclick = () => editModal.classList.remove('show');

        window.onclick = (e) => {
            if (e.target === addModal) addModal.classList.remove('show');
            if (e.target === editModal) editModal.classList.remove('show');
        };
    </script>

</body>

</html>