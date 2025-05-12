<?php
session_start();
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'add') {
    $custID = $_POST['custID'];
    $staffID = $_POST['staffID'];
    $payStatus = $_POST['payStatus'];
    $productIDs = $_POST['productID'];
    $quantities = $_POST['qty'];

    // Start transaction to ensure atomicity
    $connect->begin_transaction();

    try {
        // Insert order into the orders table
        $orderSQL = "INSERT INTO orders (custID, staffID, date, time, totalAmount, payStatus) 
                     VALUES (?, ?, NOW(), NOW(), 0, ?)";
        $stmt = $connect->prepare($orderSQL);
        $stmt->bind_param("iis", $custID, $staffID, $payStatus);
        $stmt->execute();
        $orderID = $stmt->insert_id; // Get the last inserted orderID

        // Calculate total amount and insert products into orders_product table
        $totalAmount = 0;
        foreach ($productIDs as $index => $productID) {
            $qty = (int) $quantities[$index];

            // Get the unit price from the product table
            $priceSQL = "SELECT price FROM product WHERE productID = ?";
            $priceStmt = $connect->prepare($priceSQL);
            $priceStmt->bind_param("i", $productID);
            $priceStmt->execute();
            $priceResult = $priceStmt->get_result();
            $row = $priceResult->fetch_assoc();
            $unitPrice = $row['price'];
            $subtotal = $unitPrice * $qty;
            $totalAmount += $subtotal;

            // Insert into orders_product table
            $insertProductSQL = "INSERT INTO orders_product (orderID, productID, unitPrice, qty, subtotal) 
                                 VALUES (?, ?, ?, ?, ?)";
            $insertStmt = $connect->prepare($insertProductSQL);
            $insertStmt->bind_param("iidid", $orderID, $productID, $unitPrice, $qty, $subtotal);
            $insertStmt->execute();
        }

        // Update the total amount in the orders table
        $updateOrderSQL = "UPDATE orders SET totalAmount = ? WHERE orderID = ?";
        $updateStmt = $connect->prepare($updateOrderSQL);
        $updateStmt->bind_param("di", $totalAmount, $orderID);
        $updateStmt->execute();

        // Commit the transaction
        $connect->commit();

        header("Location: orders.php?success=added");
        exit();
    } catch (Exception $e) {
        $connect->rollback(); // Rollback transaction on error
        header("Location: orders.php?error=failed");
        exit();
    }
}
?>
