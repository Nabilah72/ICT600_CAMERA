<?php
session_start();
include "connection.php";

$orders = $connect->query("
    SELECT o.*, c.custName AS custName, s.staffName AS staffName
    FROM orders o
    JOIN customer c ON o.custID = c.custID
    JOIN staff s ON o.staffID = s.staffID");

$staffs = $connect->query("SELECT staffID, staffName FROM staff");
$customers = $connect->query("SELECT custID, custName FROM customer");
$products = $connect->query("SELECT productID, brand, model, price, qty FROM product");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../css/crudd.css">
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="container">
            <h1>Order Management</h1>

            <input type="text" id="searchInput" placeholder="Search orders..." class="search-box"><br>

            <button id="openAddModal">Add New Order</button><br><br>

            <table>
                <thead>
                    <tr>
                        <th class="sortable">No.</th>
                        <th class="sortable">Order ID</th>
                        <th class="sortable">Customer</th>
                        <th class="sortable">Staff</th>
                        <th class="sortable">Date</th>
                        <th class="sortable">Time</th>
                        <th class="sortable">Amount (RM)</th>
                        <th class="sortable">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php while ($row = $orders->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['orderID'] ?></td>
                            <td><?= $row['custName'] ?></td>
                            <td><?= $row['staffName'] ?></td>
                            <td><?= $row['date'] ?></td>
                            <td><?= $row['time'] ?></td>
                            <td><?= number_format($row['totalAmount'], 2) ?></td>
                            <td><?= $row['payStatus'] ?></td>
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
            <h2>Add New Order</h2>
            <form method="POST" action="orders_crud.php">
                <input type="hidden" name="action" value="add_order">

                <div class="form-group">
                    <label>Customer</label>
                    <select name="custID" required>
                        <option value="">Select Customer</option>
                        <?php while ($row = $customers->fetch_assoc()): ?>
                            <option value="<?= $row['custID'] ?>"><?= $row['custName'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Staff</label>
                    <select name="staffID" required>
                        <option value="">Select Staff</option>
                        <?php while ($row = $staffs->fetch_assoc()): ?>
                            <option value="<?= $row['staffID'] ?>"><?= $row['staffName'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div id="product-section">
                    <label>Products</label>
                    <div class="product-row row mb-2">
                        <div class="col-md-4">
                            <select name="productID[]" required>
                                <option value="">Select Product</option>
                                <?php
                                $products->data_seek(0);
                                while ($p = $products->fetch_assoc()):
                                    ?>
                                    <option value="<?= $p['productID'] ?>">
                                        <?= $p['brand'] . ' ' . $p['model'] ?> (Stock: <?= $p['qty'] ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="qty[]" class="form-control" placeholder="Qty" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-row">Remove</button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary mb-3" id="add-product">Add Another Product</button>
                <div class="form-actions">
                    <button type="submit" class="blueBtn">Place Order</button>
                    <button type="button" id="cancelAdd">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/searchsort.js"></script>
    <script>
        const addModal = document.getElementById('addModal');
        document.getElementById('openAddModal').onclick = () => addModal.classList.add('show');
        document.getElementById('closeAdd').onclick = () => addModal.classList.remove('show');
        document.getElementById('cancelAdd').onclick = () => addModal.classList.remove('show');

        document.getElementById("add-product").addEventListener("click", function () {
            const section = document.getElementById("product-section");
            const row = section.querySelector(".product-row").cloneNode(true);
            row.querySelector("select").value = "";
            row.querySelector("input").value = "";
            section.appendChild(row);
        });

        document.addEventListener("click", function (e) {
            if (e.target.classList.contains("remove-row")) {
                const row = e.target.closest(".product-row");
                if (document.querySelectorAll(".product-row").length > 1) {
                    row.remove();
                }
            }
        });
    </script>
</body>

</html>