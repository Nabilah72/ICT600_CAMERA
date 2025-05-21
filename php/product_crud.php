<?php
// Start the session and include the database connection
session_start();
include "connection.php";

// Generate a new Product ID
function generateProductID($connect)
{
    $sql = "
        SELECT MAX(CAST(SUBSTRING(productID, 4) AS UNSIGNED)) AS maxID
        FROM product
        WHERE productID LIKE 'PRO%'
    ";
    $result = $connect->query($sql);
    $newNumber = $result && $result->num_rows > 0
        ? $result->fetch_assoc()['maxID'] + 1
        : 1;
    return 'PRO' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}

// Convert text to Title Case
function titleCase($string)
{
    return ucwords(strtolower(trim($string)));
}

// Determine action: add, edit, or delete
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Add new product
if ($action === 'add') {
    $productID = generateProductID($connect);
    $category = titleCase($_POST['category']);
    $model = titleCase($_POST['model']);

    $stmt = $connect->prepare("
        INSERT INTO product
        (productID, suppID, shelf, category, brand, model, price, qty)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "ssssssdi",
        $productID,
        $_POST['suppID'],
        $_POST['shelf'],
        $category,
        $_POST['brand'],
        $model,
        $_POST['price'],
        $_POST['qty']
    );
    $stmt->execute()
        ? header("Location: product.php?success=added")
        : header("Location: product.php?error=add");
}
// Update existing product
elseif ($action === 'edit') {
    $category = titleCase($_POST['category']);
    $model = titleCase($_POST['model']);

    $stmt = $connect->prepare("
        UPDATE product
        SET suppID  = ?, shelf  = ?, category= ?,brand = ?,model= ?,price= ?, qty = ? WHERE productID = ?
    ");
    $stmt->bind_param(
        "sssssdis",
        $_POST['suppID'],
        $_POST['shelf'],
        $category,
        $_POST['brand'],
        $model,
        $_POST['price'],
        $_POST['qty'],
        $_POST['id']
    );
    $stmt->execute()
        ? header("Location: product.php?success=updated")
        : header("Location: product.php?error=update");
}
// Delete product by ID
elseif ($action === 'delete') {
    $stmt = $connect->prepare("DELETE FROM product WHERE productID = ?");
    $stmt->bind_param("s", $_POST['id']);
    $stmt->execute()
        ? header("Location: product.php?success=deleted")
        : header("Location: product.php?error=delete");
}
?>