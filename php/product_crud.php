<?php
session_start();
include "connection.php";

function generateProductID($connect)
{
    $sql = "SELECT MAX(CAST(SUBSTRING(productID, 4) AS UNSIGNED)) AS maxID FROM product WHERE productID LIKE 'PRO%'";
    $result = $connect->query($sql);
    $newNumber = $result && $result->num_rows > 0 ? $result->fetch_assoc()['maxID'] + 1 : 1;
    return 'PRO' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}

function titleCase($string)
{
    return ucwords(strtolower(trim($string)));
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Add Product
if ($action === 'add') {
    $productID = generateProductID($connect);

    // Apply title case to category and model
    $category = titleCase($_POST['category']);
    $model = titleCase($_POST['model']);

    $stmt = $connect->prepare("INSERT INTO product (productID, suppID, shelf, category, brand, model, price, qty) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssd", $productID, $_POST['suppID'], $_POST['shelf'], $category, $_POST['brand'], $model, $_POST['price'], $_POST['qty']);
    $stmt->execute() ? header("Location: product.php?success=added") : header("Location: product.php?error=add");
}

// Edit Product
elseif ($action === 'edit') {
    // Apply title case to category and model
    $category = titleCase($_POST['category']);
    $model = titleCase($_POST['model']);

    $stmt = $connect->prepare("UPDATE product SET suppID=?, shelf=?, category=?, brand=?, model=?, price=?, qty=? WHERE productID=?");
    $stmt->bind_param("ssssssds", $_POST['suppID'], $_POST['shelf'], $category, $_POST['brand'], $model, $_POST['price'], $_POST['qty'], $_POST['id']);
    $stmt->execute() ? header("Location: product.php?success=updated") : header("Location: product.php?error=update");
}

// Delete Product
elseif ($action === 'delete') {
    $stmt = $connect->prepare("DELETE FROM product WHERE productID = ?");
    $stmt->bind_param("s", $_POST['id']);
    $stmt->execute() ? header("Location: product.php?success=deleted") : header("Location: product.php?error=delete");
}
?>