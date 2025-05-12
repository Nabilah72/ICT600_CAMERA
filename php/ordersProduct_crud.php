<?php
include "connection.php";

// Handle Add Operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'add') {
    $orderID = $_POST['orderID'];
    $productIDs = $_POST['productID'];
    $qtys = $_POST['qty'];

    $successCount = 0;
    $duplicateCount = 0;

    foreach ($productIDs as $index => $productID) {
        $qty = (int) $qtys[$index];

        // Check for duplicate (orderID, productID)
        $checkSQL = "SELECT * FROM orders_product WHERE orderID = ? AND productID = ?";
        $stmt = $connect->prepare($checkSQL);
        $stmt->bind_param("ii", $orderID, $productID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $duplicateCount++;
            continue;
        }

        // Get unit price from product table
        $priceSQL = "SELECT price FROM product WHERE productID = ?";
        $priceStmt = $connect->prepare($priceSQL);
        $priceStmt->bind_param("i", $productID);
        $priceStmt->execute();
        $priceResult = $priceStmt->get_result();
        $row = $priceResult->fetch_assoc();
        $unitPrice = $row['price'];
        $subtotal = $unitPrice * $qty;

        // Insert into orders_product
        $insertSQL = "INSERT INTO orders_product (orderID, productID, unitPrice, qty, subtotal)
                      VALUES (?, ?, ?, ?, ?)";
        $insertStmt = $connect->prepare($insertSQL);
        $insertStmt->bind_param("iidid", $orderID, $productID, $unitPrice, $qty, $subtotal);
        $insertStmt->execute();

        $successCount++;
    }

    if ($successCount > 0) {
        header("Location: ordersProduct.php?success=added ($successCount) products");
    } else {
        header("Location: ordersProduct.php?error=none_added_or_duplicates ($duplicateCount) duplicates found");
    }
    exit();
}

// Handle Edit Operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'edit') {
    $orderProductID = $_POST['orderProductID'];
    $productID = $_POST['productID'];
    $qty = $_POST['qty'];

    // Get the unit price from the product table
    $priceSQL = "SELECT price FROM product WHERE productID = ?";
    $priceStmt = $connect->prepare($priceSQL);
    $priceStmt->bind_param("i", $productID);
    $priceStmt->execute();
    $priceResult = $priceStmt->get_result();
    $row = $priceResult->fetch_assoc();
    $unitPrice = $row['price'];
    $subtotal = $unitPrice * $qty;

    // Update the order_product record
    $updateSQL = "UPDATE orders_product SET productID = ?, unitPrice = ?, qty = ?, subtotal = ?
                  WHERE orderProductID = ?";
    $updateStmt = $connect->prepare($updateSQL);
    $updateStmt->bind_param("iididi", $productID, $unitPrice, $qty, $subtotal, $orderProductID);
    $updateStmt->execute();

    header("Location: ordersProduct.php?success=updated");
    exit();
}

// Handle Delete Operation
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['orderProductID'])) {
    $orderProductID = $_GET['orderProductID'];

    // Delete the record from orders_product table
    $deleteSQL = "DELETE FROM orders_product WHERE orderProductID = ?";
    $deleteStmt = $connect->prepare($deleteSQL);
    $deleteStmt->bind_param("i", $orderProductID);
    $deleteStmt->execute();

    header("Location: ordersProduct.php?success=deleted");
    exit();
}

// Fetch the orders and their products for display (view operation)
$ordersSQL = "SELECT o.orderID, o.date, o.time, op.orderProductID, op.productID, op.qty, op.unitPrice, op.subtotal 
              FROM orders o 
              JOIN orders_product op ON o.orderID = op.orderID";
$ordersResult = $connect->query($ordersSQL);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders Products</title>
</head>

<body>
    <h1>Manage Orders Products</h1>

    <!-- Display Success/Error messages -->
    <?php if (isset($_GET['success'])): ?>
        <div style="color: green;"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <!-- Add New Order Product Form -->
    <h2>Add New Product to Order</h2>
    <form action="ordersProduct_crud.php?action=add" method="POST">
        <label for="orderID">Order ID:</label>
        <input type="number" name="orderID" required><br>

        <label for="productID">Product ID:</label>
        <input type="number" name="productID[]" required><br>

        <label for="qty">Quantity:</label>
        <input type="number" name="qty[]" required><br>

        <button type="submit">Add Product</button>
    </form>

    <!-- Edit and Delete Orders Products Table -->
    <h2>Existing Order Products</h2>
    <table border="1">
        <tr>
            <th>Order ID</th>
            <th>Product ID</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Subtotal</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $ordersResult->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['orderID']; ?></td>
                <td><?php echo $row['productID']; ?></td>
                <td><?php echo $row['qty']; ?></td>
                <td><?php echo $row['unitPrice']; ?></td>
                <td><?php echo $row['subtotal']; ?></td>
                <td>
                    <!-- Edit Form -->
                    <form action="ordersProduct_crud.php?action=edit" method="POST" style="display: inline;">
                        <input type="hidden" name="orderProductID" value="<?php echo $row['orderProductID']; ?>">
                        <input type="number" name="productID" value="<?php echo $row['productID']; ?>" required>
                        <input type="number" name="qty" value="<?php echo $row['qty']; ?>" required>
                        <button type="submit">Edit</button>
                    </form>

                    <!-- Delete Link -->
                    <a
                        href="ordersProduct_crud.php?action=delete&orderProductID=<?php echo $row['orderProductID']; ?>">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>