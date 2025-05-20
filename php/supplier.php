<?php
session_start();
include "connection.php";

// Prepare alert message from query parameters
$alertMessage = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added':
            $alertMessage = "Supplier successfully added.";
            break;
        case 'updated':
            $alertMessage = "Supplier successfully updated.";
            break;
        case 'deleted':
            $alertMessage = "Supplier successfully deleted.";
            break;
    }
} elseif (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'add':
            $alertMessage = "Error adding supplier. Please try again.";
            break;
        case 'update':
            $alertMessage = "Error updating supplier. Please try again.";
            break;
        case 'delete':
            $alertMessage = "Error deleting supplier. Please try again.";
            break;
    }
}

// Fetch all supplier records from the database
$sql = "SELECT * FROM supplier ORDER BY suppID";
$result = $connect->query($sql) or die("Query failed: " . $connect->error);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Supplier Management</title>
    <link rel="stylesheet" href="../css/crud.css">
    <style>
        /* Hide modals initially */
        .popup-modal,
        #alertModal {
            display: none;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="container">
            <h1>Supplier Management</h1>
            <input type="text" id="searchInput" placeholder="Search supplier..." class="search-box">
            <button id="openAddModal" class="blueBtn">Add Supplier</button><br><br>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th class="sortable">No.</th>
                            <th class="sortable">Supplier ID</th>
                            <th class="sortable">Name</th>
                            <th class="sortable">Phone</th>
                            <th class="sortable">Email</th>
                            <th class="sortable">Address</th>
                            <th class="sortable">Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['suppID']) ?></td>
                                <td><?= htmlspecialchars($row['suppName']) ?></td>
                                <td><?= htmlspecialchars($row['suppPhone']) ?></td>
                                <td><?= htmlspecialchars($row['suppEmail']) ?></td>
                                <td><?= htmlspecialchars($row['suppAddress']) ?></td>
                                <td><?= htmlspecialchars($row['suppStatus']) ?></td>
                                <td>
                                    <button class="editBtn" data-id="<?= htmlspecialchars($row['suppID']) ?>"
                                        data-name="<?= htmlspecialchars($row['suppName']) ?>"
                                        data-phone="<?= htmlspecialchars($row['suppPhone']) ?>"
                                        data-email="<?= htmlspecialchars($row['suppEmail']) ?>"
                                        data-address="<?= htmlspecialchars($row['suppAddress']) ?>"
                                        data-status="<?= htmlspecialchars($row['suppStatus']) ?>">Edit</button>
                                    <form action="supplier_crud.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($row['suppID']) ?>">
                                        <button type="submit" class="deleteBtn"
                                            onclick="return confirm('Delete this supplier?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Supplier Modal -->
    <div class="popup-modal" id="supplierModal">
        <div class="popup-content">
            <span class="close-btn" id="closeModal">&times;</span>
            <h2 id="modalTitle">Add Supplier</h2>
            <form action="supplier_crud.php" method="POST">
                <input type="hidden" name="action" value="add" id="formAction">
                <input type="hidden" name="id" id="supplierID">

                <div class="form-group">
                    <label>Name <span class="required">*</span></label>
                    <input type="text" name="name" id="nameField" required>
                </div>

                <div class="form-group">
                    <label>Phone <span class="required">*</span></label>
                    <input type="text" name="phone" id="phoneField" required>
                </div>

                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" id="emailField" required>
                </div>

                <div class="form-group">
                    <label>Address <span class="required">*</span></label>
                    <textarea name="address" id="addressField" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="suppStatus" id="statusField" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button class="blueBtn" type="submit" id="submitBtn">Save</button>
                    <button type="button" id="cancelBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alert Modal -->
    <?php if (!empty($alertMessage)): ?>
        <div class="modal" id="alertModal">
            <div class="modal-content">
                <p><?= htmlspecialchars($alertMessage) ?></p>
                <button class="btn" id="closeAlertBtn">Close</button>
            </div>
        </div>
    <?php endif; ?>

    <script src="../js/searchsort.js"></script>
    <script>
        // Modal elements
        const supplierModal = document.getElementById('supplierModal');
        const openAddBtn = document.getElementById('openAddModal');
        const closeBtn = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const modalTitle = document.getElementById('modalTitle');
        const formAction = document.getElementById('formAction');
        const supplierID = document.getElementById('supplierID');

        const fields = {
            name: document.getElementById('nameField'),
            phone: document.getElementById('phoneField'),
            email: document.getElementById('emailField'),
            address: document.getElementById('addressField'),
            status: document.getElementById('statusField'),
        };

        function openModal(mode, data = {}) {
            supplierModal.style.display = 'flex';
            setTimeout(() => supplierModal.classList.add('show'), 10);

            modalTitle.textContent = mode === 'edit' ? 'Edit Supplier' : 'Add Supplier';
            formAction.value = mode;
            document.getElementById('submitBtn').textContent = mode === 'edit' ? 'Save Changes' : 'Add Supplier';

            if (mode === 'edit') {
                supplierID.value = data.id;
                fields.name.value = data.name;
                fields.phone.value = data.phone;
                fields.email.value = data.email;
                fields.address.value = data.address;
                fields.status.value = data.status;
            } else {
                supplierID.value = '';
                Object.values(fields).forEach(f => f.value = '');
            }
        }

        function closeModal() {
            supplierModal.classList.remove('show');
            setTimeout(() => { supplierModal.style.display = 'none'; }, 300);
        }

        openAddBtn.addEventListener('click', () => openModal('add'));
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        window.addEventListener('click', e => { if (e.target === supplierModal) closeModal(); });

        document.querySelectorAll('.editBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                openModal('edit', {
                    id: btn.dataset.id,
                    name: btn.dataset.name,
                    phone: btn.dataset.phone,
                    email: btn.dataset.email,
                    address: btn.dataset.address,
                    status: btn.dataset.status
                });
            });
        });

        // Alert modal handler
        const alertModal = document.getElementById('alertModal');
        const closeAlertBtn = document.getElementById('closeAlertBtn');
        if (alertModal) {
            alertModal.style.display = 'flex';
            closeAlertBtn.addEventListener('click', () => { alertModal.style.display = 'none'; });
        }
    </script>
</body>

</html>