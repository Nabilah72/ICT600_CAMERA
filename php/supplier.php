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
    <link rel="stylesheet" href="../css/crud.css">
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="container">
            <h1>Supplier Management</h1>

            <!-- Show "Add New Supplier" button only if the user is an admin -->
            <?php if ($is_admin): ?>
                <button id="openAddModal">Add New Supplier</button><br><br>
            <?php endif; ?>

            <!-- Supplier Table -->
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Status</th>
                            <?php if ($is_admin): ?>
                                <th>Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['suppID']) ?></td>
                                <td><?= htmlspecialchars($row['suppName']) ?></td>
                                <td><?= htmlspecialchars($row['suppPhone']) ?></td>
                                <td><?= htmlspecialchars($row['suppEmail']) ?></td>
                                <td><?= htmlspecialchars($row['suppAddress']) ?></td>
                                <td><?= htmlspecialchars($row['suppStatus']) ?></td>
                                <?php if ($is_admin): ?>
                                    <td>
                                        <button class="editBtn" data-id="<?= $row['suppID'] ?>"
                                            data-name="<?= $row['suppName'] ?>" data-phone="<?= $row['suppPhone'] ?>"
                                            data-email="<?= $row['suppEmail'] ?>" data-address="<?= $row['suppAddress'] ?>"
                                            data-status="<?= $row['suppStatus'] ?>">
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
    </div>

    <!-- Add Supplier Modal -->
    <div class="popup-modal" id="addPopupModal">
        <div class="popup-content">
            <span class="close-btn" id="closeAddModal">&times;</span>
            <h2>Add Supplier</h2>
            <form action="supplier_crud.php" method="POST">
                <input type="hidden" name="action" value="add">

                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" id="addSupplierName" required>
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" id="addSupplierPhone" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="addSupplierEmail" required>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" id="addSupplierAddress" required></textarea>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="suppStatus" id="addSupplierStatus" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button class="blueBtn" type="submit">Add Supplier</button>
                    <button type="button" id="cancelModal">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div class="popup-modal" id="popupModal">
        <div class="popup-content">
            <span class="close-btn" id="closeModal">&times;</span>
            <h2>Edit Supplier</h2>
            <form action="supplier_crud.php" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editSupplierID">

                <div class="form-group">
                    <label>Supplier ID</label>
                    <input type="text" id="editSuppIDDisplay" disabled>
                </div>

                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" id="editSupplierName" required>
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" id="editSupplierPhone" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="editSupplierEmail" required>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" id="editSupplierAddress" required></textarea>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="suppStatus" id="editSupplierStatus" required>
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

    <!-- JavaScript -->
    <script>
        const popupModal = document.getElementById('popupModal');
        const closeModalBtn = document.getElementById('closeModal');
        const cancelModalBtn = document.getElementById('cancelModal');

        const editFields = {
            id: document.getElementById('editSupplierID'),
            idDisplay: document.getElementById('editSuppIDDisplay'),
            name: document.getElementById('editSupplierName'),
            phone: document.getElementById('editSupplierPhone'),
            email: document.getElementById('editSupplierEmail'),
            address: document.getElementById('editSupplierAddress'),
            status: document.getElementById('editSupplierStatus'),
        };

        function showModal(data) {
            popupModal.style.display = 'flex';
            setTimeout(() => popupModal.classList.add('show'), 10);

            editFields.id.value = data.id;
            editFields.idDisplay.value = data.id;
            editFields.name.value = data.name;
            editFields.phone.value = data.phone;
            editFields.email.value = data.email;
            editFields.address.value = data.address;
            editFields.status.value = data.status;
        }

        function closeModal() {
            popupModal.classList.remove('show');
            setTimeout(() => {
                popupModal.style.display = 'none';
                Object.values(editFields).forEach(field => {
                    if (field.tagName === 'INPUT' || field.tagName === 'TEXTAREA' || field.tagName === 'SELECT') {
                        field.value = '';
                    }
                });
            }, 300);
        }

        document.querySelectorAll('.editBtn').forEach(button => {
            button.addEventListener('click', () => {
                showModal({
                    id: button.dataset.id,
                    name: button.dataset.name,
                    phone: button.dataset.phone,
                    email: button.dataset.email,
                    address: button.dataset.address,
                    status: button.dataset.status
                });
            });
        });

        closeModalBtn.addEventListener('click', closeModal);
        cancelModalBtn.addEventListener('click', closeModal);
        window.addEventListener('click', (e) => {
            if (e.target === popupModal) closeModal();
        });

        // Add Modal
        const addModal = document.getElementById('addPopupModal');
        const openAddModalBtn = document.getElementById('openAddModal');
        const closeAddModalBtn = document.getElementById('closeAddModal');
        const cancelAddModalBtn = document.getElementById('cancelAddModal');

        if (openAddModalBtn) {
            openAddModalBtn.addEventListener('click', () => {
                addModal.style.display = 'flex';
                setTimeout(() => addModal.classList.add('show'), 10);
            });
        }

        function closeAddModal() {
            addModal.classList.remove('show');
            setTimeout(() => {
                addModal.style.display = 'none';
                document.querySelector('#addPopupModal form').reset();
            }, 300);
        }

        closeAddModalBtn.addEventListener('click', closeAddModal);
        cancelAddModalBtn.addEventListener('click', closeAddModal);
        window.addEventListener('click', (e) => {
            if (e.target === addModal) closeAddModal();
        });
    </script>
</body>

</html>