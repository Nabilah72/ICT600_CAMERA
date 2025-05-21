<?php
// Start the session and include the database connection
session_start();
include "connection.php";

// Get total number of users, products, and suppliers from the database
$users_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS count FROM users"))['count'];
$product_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS count FROM product"))['count'];
$supplier_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS count FROM supplier"))['count'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Shah Alam Camera</title>
    <!-- Link to external CSS files -->
    <link rel="stylesheet" href="../css/cruds.css">
    <link rel="stylesheet" href="../css/homepage.css">
    <!-- Boxicons for icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="container">
            <!-- Welcome message with logged-in staff name -->
            <div class="welcome-msg">
                Welcome, <strong><?php echo htmlspecialchars($_SESSION['staffName']); ?> !</strong>
            </div>
            <!-- Dashboard title and description -->
            <div class="section-title">
                <h3>Dashboard Overview</h3>
                <p>Overview of critical metrics and operational insights.</p>
            </div>
            <!-- Dashboard statistic cards -->
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
            <!-- Quick access section title -->
            <div class="section-title">
                <h3>Quick Access</h3>
                <p>Efficient navigation to essential system functions.</p>
            </div>
            <br>
            <!-- Quick access cards for navigation -->
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