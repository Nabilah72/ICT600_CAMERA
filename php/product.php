<?php
session_start();
include "connection.php";

// Fetch all products with supplier name
$sql = "SELECT p.*, s.suppName FROM product p JOIN supplier s ON p.suppID = s.suppID ORDER BY p.productID ASC";
$result = $connect->query($sql);

// Get suppliers for dropdown
$suppResult = $connect->query("SELECT * FROM supplier");
$suppliers = $suppResult->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Product Management</title>
</head>

<body>

    <?php include "../html/navbar.html"; ?>

    <div class="container">
        <h1>Product Management</h1>

        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">Add New
            Product</button>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Supplier</th>
                        <th>Shelf</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Price (RM)</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['productID']) ?></td>
                            <td><?= htmlspecialchars($row['suppID']) ?></td>
                            <td><?= htmlspecialchars($row['shelf']) ?></td>
                            <td><?= htmlspecialchars($row['category']) ?></td>
                            <td><?= htmlspecialchars($row['brand']) ?></td>
                            <td><?= htmlspecialchars($row['model']) ?></td>
                            <td><?= htmlspecialchars($row['price']) ?></td>
                            <td><?= htmlspecialchars($row['qty']) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm editBtn" data-bs-toggle="modal"
                                    data-bs-target="#editProductModal" data-id="<?= $row['productID'] ?>"
                                    data-suppid="<?= $row['suppID'] ?>" data-shelf="<?= $row['shelf'] ?>"
                                    data-category="<?= $row['category'] ?>" data-brand="<?= $row['brand'] ?>"
                                    data-model="<?= $row['model'] ?>" data-price="<?= $row['price'] ?>"
                                    data-qty="<?= $row['qty'] ?>">
                                    Edit
                                </button>
                                <a href="product_crud.php?action=delete&id=<?= $row['productID'] ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="product_crud.php" method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Supplier</label>
                        <select name="suppID" class="form-select" required>
                            <?php foreach ($suppliers as $supp): ?>
                                <option value="<?= $supp['suppID'] ?>"><?= $supp['suppName'] ?> (<?= $supp['suppID'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Shelf</label><input type="text" name="shelf"
                            class="form-control" required></div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select" required>
                            <option value="Camera">Camera</option>
                            <option value="Lens">Lens</option>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Brand</label><input type="text" name="brand"
                            class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Model</label><input type="text" name="model"
                            class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Price</label><input type="number" step="0.01"
                            name="price" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Quantity</label><input type="number" name="qty"
                            class="form-control" required></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="product_crud.php" method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="editProductID">

                    <!-- Uneditable fields for Product ID -->
                    <div class="mb-3">
                        <label class="form-label">Product ID</label>
                        <input type="text" id="editProdIDDisplay" class="form-control" disabled>
                    </div>
                    <!-- Editable fields -->
                    <div class="mb-3">
                        <label class="form-label">Supplier</label>
                        <select name="suppID" id="editSuppID" class="form-select" required>
                            <?php foreach ($suppliers as $supp): ?>
                                <option value="<?= $supp['suppID'] ?>"><?= $supp['suppName'] ?> (<?= $supp['suppID'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Shelf</label><input type="text" name="shelf"
                            id="editShelf" class="form-control" required></div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" id="editCategory" class="form-select" required>
                            <option value="Camera">Camera</option>
                            <option value="Lens">Lens</option>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Brand</label><input type="text" name="brand"
                            id="editBrand" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Model</label><input type="text" name="model"
                            id="editModel" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Price</label><input type="number" step="0.01"
                            name="price" id="editPrice" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Quantity</label><input type="number" name="qty"
                            id="editQty" class="form-control" required></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('.editBtn').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('editProductID').value = button.dataset.id;
                document.getElementById('editProdIDDisplay').value = button.dataset.id;
                document.getElementById('editSuppID').value = button.dataset.suppid;
                document.getElementById('editShelf').value = button.dataset.shelf;
                const editCategory = document.getElementById('editCategory');
                editCategory.value = button.dataset.category;
                document.getElementById('editBrand').value = button.dataset.brand;
                document.getElementById('editModel').value = button.dataset.model;
                document.getElementById('editPrice').value = button.dataset.price;
                document.getElementById('editQty').value = button.dataset.qty;
            });
        });
    </script>
</body>

</html>