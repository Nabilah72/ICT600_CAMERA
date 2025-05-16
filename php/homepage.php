<?php
session_start();
include "connection.php";

$product_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS count FROM product"))['count'];
$order_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS count FROM orders"))['count'];
$customer_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS count FROM customer"))['count'];
$sales_result = mysqli_query($connect, "SELECT SUM(subtotal) AS total FROM orders_product");
$total_sales = mysqli_fetch_assoc($sales_result)['total'] ?? 0;
$total_sales = number_format($total_sales, 2);
?>


<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
    <link rel="stylesheet" href="../css/cruds.css">
    <style>
        .container {
            text-align: left;
        }

        .welcome-msg {
            font-size: 25px;
            font-weight: 600;
            color: #222;
        }

        .dashboard-cards {
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 20px 0;
        }

        .card {
            flex: 1 1 180px;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            transition: 0.3s ease;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #555;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .card-header i {
            font-size: 28px;
            color: #888;
        }

        .card-body h2 {
            text-align: left;
            font-size: 24px;
            margin: 0;
            color: #333;
        }

        .card-header span {
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .dashboard-cards {
                flex-direction: column;
            }

            .card {
                width: 100%;
                height: 50%
            }
        }

        .section-title {
            margin-top: 40px;
            margin-bottom: 10px;
        }

        .section-title h3 {
            font-size: 20px;
            margin-bottom: 5px;
            color: #333;
        }

        .section-title p {
            font-size: 14px;
            color: #777;
        }

        .btn-link {
            text-align: center;
            width: 100%;
            display: inline-block;
            margin-top: 10px;
            padding: 10px 10px;
            background-color: #ffc107;
            color: #000;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }

        .btn-link:hover {
            background-color: #FFD700;
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(255, 193, 7, 0.6);
        }

        .quick-access-wrapper {
            display: flex;
            justify-content: center;
            gap: 100px;
            flex-wrap: wrap;
            margin: 20px 0;
        }

        .quick-card {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .quick-card img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
        }

        hr {
            border: none;
            border-top: 2px solid #ccc;
            margin: 20px 0;
            width: 100%;
            opacity: 0.6;
        }
    </style>

</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="container">
            <div class="welcome-msg">
                Welcome, <strong><?php echo htmlspecialchars($_SESSION['staffName']); ?> !</strong>
            </div>
            <div class="section-title">
                <h3>Dashboard Overview</h3>
                <p>Overview of critical metrics and operational insights.</p>
            </div>
            <div class="dashboard-cards">
                <div class="card" style="border-left: 8px solid; border-left-color: #6f42c1;">
                    <div class="card-header">
                        <span>Total Products</span>
                        <i class='bx bx-box'></i>
                    </div>
                    <div class="card-body">
                        <h2><?php echo $product_count; ?></h2>
                    </div>
                </div>

                <div class="card" style="border-left: 8px solid; border-left-color: #28a745;">
                    <div class="card-header">
                        <span>Total Orders</span>
                        <i class='bx bx-receipt'></i>
                    </div>
                    <div class="card-body">
                        <h2><?php echo $order_count; ?></h2>
                    </div>
                </div>

                <div class="card" style="border-left: 8px solid; border-left-color: #17a2b8;">
                    <div class="card-header">
                        <span>Total Customers</span>
                        <i class='bx bx-user'></i>
                    </div>
                    <div class="card-body">
                        <h2><?php echo $customer_count; ?></h2>
                    </div>
                </div>

                <div class="card" style="border-left: 8px solid; border-left-color: #ffc107;">
                    <div class="card-header">
                        <span>Total Sales</span>
                        <i class='bx bx-line-chart'></i>
                    </div>
                    <div class="card-body">
                        <h2>RM <?php echo $total_sales; ?></h2>
                    </div>
                </div>

            </div>
            <hr>
            <div class="section-title">
                <h3>Quick Access</h3>
                <p>Efficient navigation to essential system functions.</p>
            </div>
            <br>
            <div class="quick-access-wrapper">
                <div class="quick-card">
                    <img src="../images/product.jpg" alt="Product">
                    <a href="product.php" class="btn-link">Go to Products</a>
                    <p>Manage product inventory.</p>
                </div>
                <div class="quick-card">
                    <img src="../images/order.jpg" alt="Orders">
                    <a href="orders.php" class="btn-link">Go to Orders</a>
                    <p>Process customer's orders.</p>
                </div>
                <div class="quick-card">
                    <img src="../images/sales.jpg" alt="Sales">
                    <a href="ordersProduct.php" class="btn-link">Go to Sales</a>
                    <p>Analyze sales performance.</p>

                </div>
                <div class="quick-card">
                    <img src="../images/customers.jpg" alt="Customers">
                    <a href="customer.php" class="btn-link">Go to Customers</a>
                    <p>Manage customer profile.</p>
                </div>
            </div>

        </div>


</body>

</html>