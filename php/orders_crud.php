<?php
session_start();
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add_order') {
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $custID = $_POST['custID'];
    $staffID = $_POST['userID'];
    $date = date('Y-m-d');
    $time = date('H:i:s');
    $payStatus = 'Unpaid';

    $productIDs = $_POST['productID'];
    $quantities = $_POST['qty'];

    // Validate matching lengths
    if (count($productIDs) !== count($quantities)) {
        die("Product and quantity mismatch.");
    }

    // Prevent duplicate selections
    if (count($productIDs) !== count(array_unique($productIDs))) {
        die("Duplicate product selections are not allowed.");
    }

    $connect->begin_transaction();

    try {
        // 1. Insert into orders (auto-incremented orderID will be used)
        $insertOrder = $connect->prepare("
            INSERT INTO orders (custID, userID, date, time, totalAmount, payStatus)
            VALUES (?, ?, ?, ?, 0, ?)
        ");
        $insertOrder->bind_param("sssss", $custID, $staffID, $date, $time, $payStatus);
        $insertOrder->execute();

        $orderID = $connect->insert_id; // auto-incremented orderID

        $totalAmount = 0;

        // 2. Process each product
        for ($i = 0; $i < count($productIDs); $i++) {
            $pid = $productIDs[$i];
            $qty = (int) $quantities[$i];

            // Fetch product price and stock
            $stmt = $connect->prepare("SELECT price, qty FROM product WHERE productID = ?");
            $stmt->bind_param("s", $pid);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();

            if (!$product)
                throw new Exception("Product ID $pid not found.");
            if ($qty > $product['qty'])
                throw new Exception("Insufficient stock for product $pid.");

            $unitPrice = $product['price'];
            $subtotal = $unitPrice * $qty;
            $totalAmount += $subtotal;

            // Insert into orders_product
            $stmtInsert = $connect->prepare("
                INSERT INTO orders_product (orderID, productID, unitPrice, qty, subtotal)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmtInsert->bind_param("isdid", $orderID, $pid, $unitPrice, $qty, $subtotal);
            $stmtInsert->execute();

            // Deduct stock
            $stmtUpdate = $connect->prepare("UPDATE product SET qty = qty - ? WHERE productID = ?");
            $stmtUpdate->bind_param("is", $qty, $pid);
            $stmtUpdate->execute();
        }

        // 3. Update total amount
        $stmtTotal = $connect->prepare("UPDATE orders SET totalAmount = ? WHERE orderID = ?");
        $stmtTotal->bind_param("di", $totalAmount, $orderID);
        $stmtTotal->execute();

        $connect->commit();
        header("Location: orders.php?msg=success");
        exit();

    } catch (Exception $e) {
        $connect->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>