<?php
session_start();
include "connection.php";

// Fetch all order records from the database
$sql = "SELECT * FROM orders_product";
$result = $connect->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Management</title>
    <link rel="stylesheet" href="../css/table.css">
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="container">
            <h2>Order Management</h2>
            <button id="openAddModal">Add New Order</button><br><br>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product ID</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['orderID']) ?></td>
                            <td><?= htmlspecialchars($row['productID']) ?></td>
                            <td><?= htmlspecialchars($row['qty']) ?></td>
                            <td><?= htmlspecialchars($row['total']) ?></td>
                            <td>
                                <button class="editBtn" data-orderid="<?= $row['orderID'] ?>"
                                    data-productid="<?= $row['productID'] ?>" data-qty="<?= $row['qty'] ?>"
                                    data-total="<?= $row['total'] ?>">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Order Modal -->
    <div class="popup-modal" id="addModal">
        <div class="popup-content">
            <span class="close-btn" id="closeAdd">&times;</span>
            <h2>Add Order</h2>
            <form action="order_crud.php" method="POST">
                <input type="hidden" name="action" value="add">

                <div class="form-group">
                    <label>Product ID</label>
                    <input type="text" name="productID" required>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" name="qty" required>
                </div>
                <div class="form-group">
                    <label>Total</label>
                    <input type="number" step="0.01" name="total" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="blueBtn">Add Order</button>
                    <button type="button" id="cancelAdd">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Order Modal -->
    <div class="popup-modal" id="editModal">
        <div class="popup-content">
            <span class="close-btn" id="closeEdit">&times;</span>
            <h2>Edit Order</h2>
            <form action="order_crud.php" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="orderID" id="editOrderID">

                <div class="form-group">
                    <label>Product ID</label>
                    <input type="text" name="productID" id="editProductID" required>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" name="qty" id="editQty" required>
                </div>
                <div class="form-group">
                    <label>Total</label>
                    <input type="number" step="0.01" name="total" id="editTotal" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="blueBtn">Save Changes</button>
                    <button type="button" id="cancelEdit">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const addModal = document.getElementById('addModal');
        const editModal = document.getElementById('editModal');

        // Show modals
        document.getElementById('openAddModal').onclick = () => addModal.classList.add('show');
        document.getElementById('closeAdd').onclick = () => addModal.classList.remove('show');
        document.getElementById('cancelAdd').onclick = () => addModal.classList.remove('show');

        document.querySelectorAll('.editBtn').forEach(btn => {
            btn.onclick = () => {
                document.getElementById('editOrderID').value = btn.dataset.orderid;
                document.getElementById('editProductID').value = btn.dataset.productid;
                document.getElementById('editQty').value = btn.dataset.qty;
                document.getElementById('editTotal').value = btn.dataset.total;
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