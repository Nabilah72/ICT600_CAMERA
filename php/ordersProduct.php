<?php
include "connection.php";

// Fetch data from orders_product table
$ordersProduct = $connect->query("
    SELECT op.ordersProduct_ID, op.orderID, op.productID, op.unitPrice, op.qty, op.subtotal, 
           p.brand, p.model, o.date, o.time
    FROM orders_product op
    JOIN product p ON op.productID = p.productID
    JOIN orders o ON op.orderID = o.orderID");

$products = $connect->query("SELECT productID, brand, model FROM product");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Orders Products</title>
    <link rel="stylesheet" href="../css/crudd.css">
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="container">
            <h1>Order Products Management</h1>

            <input type="text" id="searchInput" placeholder="Search order products..." class="search-box"><br>

            <table>
                <thead>
                    <tr>
                        <th class="sortable">No.</th>
                        <th class="sortable">Order ID</th>
                        <th class="sortable">Product</th>
                        <th class="sortable">Unit Price (RM)</th>
                        <th class="sortable">Quantity</th>
                        <th class="sortable">Subtotal (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php while ($row = $ordersProduct->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['orderID'] ?></td>
                            <td><?= $row['brand'] . ' ' . $row['model'] ?></td>
                            <td><?= number_format($row['unitPrice'], 2) ?></td>
                            <td><?= $row['qty'] ?></td>
                            <td><?= number_format($row['subtotal'], 2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="../js/searchsort.js"></script>
</body>

</html>