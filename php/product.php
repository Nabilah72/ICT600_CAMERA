<?php
// Start the session and include the database connection
session_start();
include "connection.php";

// Prepare alert message based on URL parameters
$alertMessage = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added':
            $alertMessage = "Product successfully added.";
            break;
        case 'updated':
            $alertMessage = "Product successfully updated.";
            break;
        case 'deleted':
            $alertMessage = "Product successfully deleted.";
            break;
    }
} elseif (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'add':
            $alertMessage = "Error adding product. Please try again.";
            break;
        case 'update':
            $alertMessage = "Error updating product. Please try again.";
            break;
        case 'delete':
            $alertMessage = "Error deleting product. Please try again.";
            break;
    }
}

// Fetch all products with supplier names
$sql = "SELECT p.*, s.suppName
        FROM product p
        JOIN supplier s ON p.suppID = s.suppID
        ORDER BY p.productID";
$result = $connect->query($sql) or die("Query failed: " . $connect->error);

// Fetch all suppliers for the dropdown
$suppResult = $connect->query("SELECT * FROM supplier") or die("Query failed: " . $connect->error);
$suppliers = $suppResult->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Product Management</title>
    <!-- External CSS -->
    <link rel="stylesheet" href="../css/cruds.css">
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="container">
            <h1>Product Management</h1>

            <!-- Search box & Add button -->
            <input type="text" id="searchInput" placeholder="Search product..." class="search-box">
            <button id="openAddModal" class="blueBtn">Add Product</button><br><br>

            <!-- Product table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th class="sortable">No.</th>
                            <th class="sortable">Product ID</th>
                            <th class="sortable">Supplier</th>
                            <th class="sortable">Shelf</th>
                            <th class="sortable">Category</th>
                            <th class="sortable">Brand</th>
                            <th class="sortable">Model</th>
                            <th class="sortable">Price (RM)</th>
                            <th class="sortable">Qty</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['productID']) ?></td>
                                <td><?= htmlspecialchars($row['suppName']) ?></td>
                                <td><?= htmlspecialchars($row['shelf']) ?></td>
                                <td><?= htmlspecialchars($row['category']) ?></td>
                                <td><?= htmlspecialchars($row['brand']) ?></td>
                                <td><?= htmlspecialchars($row['model']) ?></td>
                                <td><?= number_format($row['price'], 2) ?></td>
                                <td><?= htmlspecialchars($row['qty']) ?></td>
                                <td>
                                    <!-- Edit button stores data -->
                                    <button class="editBtn" data-id="<?= htmlspecialchars($row['productID']) ?>"
                                        data-suppid="<?= htmlspecialchars($row['suppID']) ?>"
                                        data-shelf="<?= htmlspecialchars($row['shelf']) ?>"
                                        data-category="<?= htmlspecialchars($row['category']) ?>"
                                        data-brand="<?= htmlspecialchars($row['brand']) ?>"
                                        data-model="<?= htmlspecialchars($row['model']) ?>"
                                        data-price="<?= htmlspecialchars($row['price']) ?>"
                                        data-qty="<?= htmlspecialchars($row['qty']) ?>">
                                        Edit
                                    </button>
                                    <!-- Delete form -->
                                    <form action="product_crud.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($row['productID']) ?>">
                                        <button type="submit" class="deleteBtn"
                                            onclick="return confirm('Delete this product?');">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div class="popup-modal" id="productModal">
        <div class="popup-content">
            <span class="close-btn" id="closeModal">&times;</span>
            <h2 id="modalTitle">Add Product</h2>
            <form action="product_crud.php" method="POST">
                <input type="hidden" name="action" value="add" id="formAction">
                <input type="hidden" name="id" id="productID">

                <!-- Supplier dropdown -->
                <div class="form-group">
                    <label>Supplier <span class="required">*</span></label>
                    <select name="suppID" id="suppID" required>
                        <?php foreach ($suppliers as $supp): ?>
                            <option value="<?= htmlspecialchars($supp['suppID']) ?>">
                                <?= htmlspecialchars($supp['suppName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Shelf & Category fields -->
                <div class="form-group">
                    <label>Shelf <span class="required">*</span></label>
                    <input type="text" name="shelf" id="shelf" required>
                </div>

                <div class="form-group">
                    <label>Category <span class="required">*</span></label>
                    <input type="text" name="category" id="category" required>
                </div>
                <!-- Brand & Model fields -->
                <div class="form-row">
                    <div class="form-group half-width">
                        <label>Brand <span class="required">*</span></label>
                        <input type="text" name="brand" id="brand" required>
                    </div>
                    <div class="form-group half-width">
                        <label>Model <span class="required">*</span></label>
                        <input type="text" name="model" id="model" required>
                    </div>
                </div>
                <!-- Price & Quantity fields -->
                <div class="form-row">
                    <div class="form-group half-width">
                        <label>Price (RM) <span class="required">*</span></label>
                        <input type="number" step="0.01" name="price" id="price" required>
                    </div>
                    <div class="form-group half-width">
                        <label>Quantity <span class="required">*</span></label>
                        <input type="number" name="qty" id="qty" required>
                    </div>
                </div>
                <!-- Form actions -->
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

    <script src="../js/searchsort.js"></script> <!-- Table search & sort -->
    <script>
        // Modal element references
        const productModal = document.getElementById('productModal');
        const openAddBtn = document.getElementById('openAddModal');
        const closeBtn = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const modalTitle = document.getElementById('modalTitle');
        const formAction = document.getElementById('formAction');
        const productID = document.getElementById('productID');

        // Form fields mapping
        const fields = {
            suppID: document.getElementById('suppID'),
            shelf: document.getElementById('shelf'),
            category: document.getElementById('category'),
            brand: document.getElementById('brand'),
            model: document.getElementById('model'),
            price: document.getElementById('price'),
            qty: document.getElementById('qty'),
        };

        // Open modal in add or edit mode
        function openModal(mode, data = {}) {
            productModal.style.display = 'flex';
            setTimeout(() => productModal.classList.add('show'), 10);

            modalTitle.textContent = mode === 'edit' ? 'Edit Product' : 'Add Product';
            formAction.value = mode;
            document.getElementById('submitBtn').textContent = mode === 'edit' ? 'Save Changes' : 'Add Product';

            if (mode === 'edit') {
                productID.value = data.id;
                fields.suppID.value = data.suppid;
                fields.shelf.value = data.shelf;
                fields.category.value = data.category;
                fields.brand.value = data.brand;
                fields.model.value = data.model;
                fields.price.value = data.price;
                fields.qty.value = data.qty;
            } else {
                productID.value = '';
                Object.values(fields).forEach(field => field.value = '');
            }
        }

        // Close modal with animation
        function closeModal() {
            productModal.classList.remove('show');
            setTimeout(() => { productModal.style.display = 'none'; }, 300);
        }

        // Event listeners for modal controls
        openAddBtn.addEventListener('click', () => openModal('add'));
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        window.addEventListener('click', e => { if (e.target === productModal) closeModal(); });

        // Edit buttons populate modal with existing data
        document.querySelectorAll('.editBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                openModal('edit', {
                    id: btn.dataset.id,
                    suppid: btn.dataset.suppid,
                    shelf: btn.dataset.shelf,
                    category: btn.dataset.category,
                    brand: btn.dataset.brand,
                    model: btn.dataset.model,
                    price: btn.dataset.price,
                    qty: btn.dataset.qty,
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