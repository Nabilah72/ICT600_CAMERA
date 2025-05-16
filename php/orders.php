<?php
session_start();
include "connection.php";

$orders = $connect->query("
    SELECT o.*, c.custName AS custName, u.userName AS userName
    FROM orders o
    JOIN customer c ON o.custID = c.custID
    JOIN users u ON o.userID = u.userID");

$users = $connect->query("SELECT userID, userName FROM users");
$customers = $connect->query("SELECT custID, custName FROM customer");
$products = $connect->query("SELECT productID, brand, model, price, qty FROM product");
?>


<!DOCTYPE html>
<html>

<head>
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../css/cruds.css">
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="container">
            <h1>Order Management</h1>

            <input type="text" id="searchInput" placeholder="Search orders..." class="search-box"><br>

            <button id="openAddModal">Add Order</button><br><br>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th class="sortable">No.</th>
                            <th class="sortable">Order ID</th>
                            <th class="sortable">Customer</th>
                            <th class="sortable">User</th>
                            <th class="sortable">Date</th>
                            <th class="sortable">Time</th>
                            <th class="sortable">Amount (RM)</th>
                            <th class="sortable">Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php while ($row = $orders->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['orderID'] ?></td>
                                <td><?= $row['custName'] ?></td>
                                <td><?= $row['userName'] ?></td>
                                <td><?= $row['date'] ?></td>
                                <td><?= $row['time'] ?></td>
                                <td><?= number_format($row['totalAmount'], 2) ?></td>
                                <td><?= $row['payStatus'] ?></td>
                                <td>
                                    <button class="edit-btn" data-orderid="<?= $row['orderID'] ?>"
                                        data-customer="<?= $row['custName'] ?>" data-user="<?= $row['userName'] ?>"
                                        data-date="<?= $row['date'] ?>" data-time="<?= $row['time'] ?>"
                                        data-amount="<?= $row['totalAmount'] ?>" data-status="<?= $row['payStatus'] ?>">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Add Order Modal -->
    <div class="popup-modal" id="addModal">
        <div class="popup-content">
            <span class="close-btn" id="closeAdd">&times;</span>
            <h2>Add Order</h2>
            <form method="POST" action="orders_crud.php">
                <input type="hidden" name="action" value="add_order">

                <div class="form-group">
                    <label>Customer <span class="required">*</span></label>
                    <select name="custID" required>
                        <option value="">Select Customer</option>
                        <?php while ($row = $customers->fetch_assoc()): ?>
                            <option value="<?= $row['custID'] ?>"><?= $row['custName'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>User</label>
                    <input type="text" value="<?= $_SESSION['staffName'] ?>" disabled>
                    <input type="hidden" name="userID" value="<?= $_SESSION['staff_id'] ?>">

                </div>

                <div id="product-section">
                    <label>Products <span class="required">*</span></label>
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

    <!-- Edit Order Modal -->
    <div class="popup-modal" id="editModal">
        <div class="popup-content">
            <span class="close-btn" id="closeEdit">&times;</span>
            <h2>Edit Payment Status</h2>
            <form method="POST" action="orders_crud.php">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="orderID" id="edit-orderID">

                <div class="form-group">
                    <label>Order ID</label>
                    <input type="text" id="edit-orderID-display" disabled>
                </div>

                <div class="form-group">
                    <label>Customer</label>
                    <input type="text" id="edit-customer" disabled>
                </div>

                <div class="form-group">
                    <label>User</label>
                    <input type="text" id="edit-user" disabled>
                </div>

                <div class="form-group">
                    <label>Date</label>
                    <input type="text" id="edit-date" disabled>
                </div>

                <div class="form-group">
                    <label>Time</label>
                    <input type="text" id="edit-time" disabled>
                </div>

                <div class="form-group">
                    <label>Amount (RM)</label>
                    <input type="text" id="edit-amount" disabled>
                </div>

                <div class="form-group">
                    <label>Payment Status</label>
                    <select name="payStatus" id="edit-payStatus" required>
                        <option value="Unpaid">Unpaid</option>
                        <option value="Pending">Pending</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="blueBtn">Update Status</button>
                    <button type="button" id="cancelEdit">Cancel</button>
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

        // Handle Edit button click
        document.querySelectorAll(".edit-btn").forEach(btn => {
            btn.addEventListener("click", () => {
                document.getElementById("edit-orderID").value = btn.dataset.orderid;
                document.getElementById("edit-orderID-display").value = btn.dataset.orderid;
                document.getElementById("edit-customer").value = btn.dataset.customer;
                document.getElementById("edit-user").value = btn.dataset.user;
                document.getElementById("edit-date").value = btn.dataset.date;
                document.getElementById("edit-time").value = btn.dataset.time;
                document.getElementById("edit-amount").value = parseFloat(btn.dataset.amount).toFixed(2);
                document.getElementById("edit-payStatus").value = btn.dataset.status;

                document.getElementById("editModal").classList.add("show");
            });
        });

        // Handle closing edit modal
        document.getElementById("closeEdit").onclick = () => document.getElementById("editModal").classList.remove("show");
        document.getElementById("cancelEdit").onclick = () => document.getElementById("editModal").classList.remove("show");

    </script>
</body>

</html>