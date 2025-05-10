<?php
session_start();
include "connection.php";

// Fetch all customer records from the database
$sql = "SELECT * FROM orders_product";
$result = $connect->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Order</title>
</head>

<body>
    <?php include "../html/navbar.html"; ?>

    <h2>Order Management</h2>

</body>

</html>