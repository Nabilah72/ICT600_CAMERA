<?php
include "connection.php";

$sql = "SELECT p.*, s.suppName FROM product p JOIN supplier s ON p.suppID = s.suppID ORDER BY p.productID ASC";
$result = $connect->query($sql);

$suppResult = $connect->query("SELECT * FROM supplier");
$suppliers = $suppResult->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Product Management</title>
    <link rel="stylesheet" href="../css/cruds.css">
</head>

<body>

    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="container">
            <h1>Product Management</h1>
            <input type="text" id="searchInput" placeholder="Search product..." class="search-box"><br>

            <button id="openAddModal">Add Product</button><br><br>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th class="sortable">No.</th>
                            <th class="sortable">ID</th>
                            <th class="sortable">Supplier</th>
                            <th class="sortable">Shelf</th>
                            <th class="sortable">Category</th>
                            <th class="sortable">Brand</th>
                            <th class="sortable">Model</th>
                            <th class="sortable">Price (RM)</th>
                            <th class="sortable">Quantity</th>
                            <th>Action</th>
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
                                <td><?= htmlspecialchars($row['price']) ?></td>
                                <td><?= htmlspecialchars($row['qty']) ?></td>
                                <td>
                                    <button class="editBtn" data-id="<?= $row['productID'] ?>"
                                        data-suppid="<?= $row['suppID'] ?>" data-shelf="<?= $row['shelf'] ?>"
                                        data-category="<?= $row['category'] ?>" data-brand="<?= $row['brand'] ?>"
                                        data-model="<?= $row['model'] ?>" data-price="<?= $row['price'] ?>"
                                        data-qty="<?= $row['qty'] ?>">Edit</button>
                                    <form action="product_crud.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $row['productID'] ?>">
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

    <!-- Add Product Modal -->
    <div class="popup-modal" id="addModal">
        <div class="popup-content">
            <span class="close-btn" id="closeAdd">&times;</span>
            <h2>Add Product</h2>
            <form action="product_crud.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label>Supplier</label>
                    <select name="suppID" required>
                        <?php foreach ($suppliers as $supp): ?>
                            <option value="<?= $supp['suppID'] ?>"><?= $supp['suppName'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Shelf</label><input type="text" name="shelf" required></div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" required>
                        <option value="Camera">Camera</option>
                        <option value="Lens">Lens</option>
                    </select>
                </div>
                <div class="form-group"><label>Brand</label><input type="text" name="brand" required></div>
                <div class="form-group"><label>Model</label><input type="text" name="model" required></div>
                <div class="form-group"><label>Price</label><input type="number" step="0.01" name="price" required>
                </div>
                <div class="form-group"><label>Quantity</label><input type="number" name="qty" required></div>
                <div class="form-actions">
                    <button type="submit" class="blueBtn">Add Product</button>
                    <button type="button" id="cancelAdd">Cancel</button>
                </div>


            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="popup-modal" id="editModal">
        <div class="popup-content">
            <span class="close-btn" id="closeEdit">&times;</span>
            <h2>Edit Product Details</h2>
            <form action="product_crud.php" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editProductID">
                <div class="form-group">
                    <label>Supplier</label>
                    <select name="suppID" id="editSuppID" required>
                        <?php foreach ($suppliers as $supp): ?>
                            <option value="<?= $supp['suppID'] ?>"><?= $supp['suppName'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Shelf</label><input type="text" name="shelf" id="editShelf" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" id="editCategory" required>
                        <option value="Camera">Camera</option>
                        <option value="Lens">Lens</option>
                    </select>
                </div>
                <div class="form-group"><label>Brand</label><input type="text" name="brand" id="editBrand" required>
                </div>
                <div class="form-group"><label>Model</label><input type="text" name="model" id="editModel" required>
                </div>
                <div class="form-group"><label>Price</label><input type="number" step="0.01" name="price" id="editPrice"
                        required></div>
                <div class="form-group"><label>Quantity</label><input type="number" name="qty" id="editQty" required>
                </div>
                <div class="form-actions">
                    <button class="blueBtn" type="submit">Save Changes</button>
                    <button type="button" id="cancelEdit">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <script src="../js/searchsort.js"></script>
    <script>
        // Add modal handlers
        const addModal = document.getElementById('addModal');
        document.getElementById('openAddModal').onclick = () => addModal.classList.add('show');
        document.getElementById('closeAdd').onclick = () => addModal.classList.remove('show');
        document.getElementById('cancelAdd').onclick = () => addModal.classList.remove('show');

        // Edit modal handlers
        const editModal = document.getElementById('editModal');
        const editFields = {
            id: document.getElementById('editProductID'),
            suppID: document.getElementById('editSuppID'),
            shelf: document.getElementById('editShelf'),
            category: document.getElementById('editCategory'),
            brand: document.getElementById('editBrand'),
            model: document.getElementById('editModel'),
            price: document.getElementById('editPrice'),
            qty: document.getElementById('editQty'),
        };

        function openEditModal(data) {
            editFields.id.value = data.id;
            editFields.suppID.value = data.suppid;
            editFields.shelf.value = data.shelf;
            editFields.category.value = data.category;
            editFields.brand.value = data.brand;
            editFields.model.value = data.model;
            editFields.price.value = data.price;
            editFields.qty.value = data.qty;
            editModal.classList.add('show');
        }

        document.querySelectorAll('.editBtn').forEach(btn => {
            btn.onclick = () => {
                openEditModal({
                    id: btn.dataset.id,
                    suppid: btn.dataset.suppid,
                    shelf: btn.dataset.shelf,
                    category: btn.dataset.category,
                    brand: btn.dataset.brand,
                    model: btn.dataset.model,
                    price: btn.dataset.price,
                    qty: btn.dataset.qty,
                });
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