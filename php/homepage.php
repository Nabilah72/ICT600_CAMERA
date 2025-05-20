<?php
session_start();
include "connection.php";

$users_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS count FROM users"))['count'];
$product_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS count FROM product"))['count'];
$supplier_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS count FROM supplier"))['count'];

?>
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
    <link rel="stylesheet" href="../css/crud.css">
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
            gap: 30px;
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
            box-shadow: 0 4px 8px #ccc;
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
            gap: 80px;
            flex-wrap: wrap;
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
                <div class="card" style="border-left: 10px solid; border-left-color: #6f42c1;">
                    <div class="card-header">
                        <span>Total Users</span>
                        <i class='bx bx-user'></i>
                    </div>
                    <div class="card-body">
                        <h2><?php echo $users_count; ?></h2>
                    </div>
                </div>

                <div class="card" style="border-left: 10px solid; border-left-color: #28a745;">
                    <div class="card-header">
                        <span>Total Product</span>
                        <i class='bx bx-camera'></i>
                    </div>
                    <div class="card-body">
                        <h2><?php echo $product_count; ?></h2>
                    </div>
                </div>

                <div class="card" style="border-left: 10px solid; border-left-color: #17a2b8;">
                    <div class="card-header">
                        <span>Total Suppliers</span>
                        <i class='bx bx-building-house'></i>
                    </div>
                    <div class="card-body">
                        <h2><?php echo $supplier_count; ?></h2>
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
                    <img src="../images/users.png" alt="Users">
                    <a href="user.php" class="btn-link">Go to Users</a>
                    <p>Manage user accounts</p>
                </div>
                <div class="quick-card">
                    <img src="../images/products.jpg" alt="Product">
                    <a href="product.php" class="btn-link">Go to Product</a>
                    <p>Manage product inventory</p>
                </div>
                <div class="quick-card">
                    <img src="../images/supplier.jpg" alt="Supplier">
                    <a href="supplier.php" class="btn-link">Go to Supplier</a>
                    <p>Manage supplier profile</p>
                </div>
                <div class="quick-card">
                    <img src="../images/profile.png" alt="Profile">
                    <a href="profile.php" class="btn-link">Go to Profile</a>
                    <p>Manage your own profile</p>
                </div>

            </div>

        </div>


</body>

</html>