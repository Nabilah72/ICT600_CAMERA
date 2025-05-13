<?php
session_start(); // Start the session to access session variables like userRole
?>

<!-- Boxicons CDN -->
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<style>
    .sidebar {
        width: 80px;
        background-color: #fff;
        padding: 20px 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        border-right: 1px solid #eee;
        transition: width 0.3s ease;
        height: 100vh;
        top: 0;
        left: 0;
        z-index: 1000;
    }

    .sidebar:hover {
        width: 220px;
        align-items: flex-start;
    }

    .logo-container {
        display: flex;
        text-align: center;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin-bottom: 20px;
    }

    .logo-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        transition: all 0.3s ease;
    }

    .sidebar:hover .logo-img {
        width: 50px;
        height: 50px;
        margin: 0 5px;
    }

    .logo-text {
        margin-right: 10px;
        font-size: 1.0rem;
        font-weight: bold;
        color: #333;
        display: none;
        white-space: nowrap;
    }

    .sidebar:hover .logo-text {
        display: inline;
    }

    .menu-top,
    .menu-bottom {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .menu-item {
        width: 100%;
        margin: 10px 0;
    }

    .menu-item a {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #333;
        text-decoration: none;
        padding: 10px;
        border-radius: 8px;
        transition: background 0.2s ease;
        white-space: nowrap;
    }

    .menu-item a i {
        font-size: 20px;
    }

    .menu-item a:hover,
    .menu-item a.active {
        background-color: #f0f0f0;
        color: #007bff;
    }

    .menu-label {
        display: none;
    }

    .sidebar:hover .menu-label {
        display: inline;
    }

    /* This ensures menu-bottom always sticks to bottom */
    .menu-bottom {
        margin-top: auto;
    }
</style>

<div class="sidebar">
    <div class="logo-container">
        <img src="../images/logo.png" alt="logo" class="logo-img">
        <span class="logo-text">S.A Camera</span>
    </div>

    <!-- Top Menu -->
    <div class="menu-top">
        <div class="menu-item"><a href="../html/homepage.html"><i class='bx bxs-home'></i> <span
                    class="menu-label">Home</span></a></div>

        <!-- Conditional display based on userRole -->
        <?php if ($_SESSION['userRole'] == 'Admin'): ?>
            <div class="menu-item"><a href="../php/user.php"><i class='bx bxs-user'></i> <span
                        class="menu-label">User</span></a></div>
            <div class="menu-item"><a href="../php/supplier.php"><i class='bx bxs-truck'></i> <span
                        class="menu-label">Supplier</span></a></div>
        <?php endif; ?>

        <div class="menu-item"><a href="../php/product.php"><i class='bx bxs-camera'></i><span
                    class="menu-label">Product</span></a></div>
        <div class="menu-item"><a href="../php/orders.php"><i class='bx bxs-cart'></i><span
                    class="menu-label">Order</span></a></div>
        <div class="menu-item"><a href="../php/ordersProduct.php"><i class='bx bxs-chart line' ></i><span
                    class="menu-label">Sales</span></a></div>
        <div class="menu-item"><a href="../php/customer.php"><i class='bx bxs-group'></i> <span
                    class="menu-label">Customer</span></a></div>
    </div>
    <div class="menu-bottom">
        <div class="menu-item"><a href="../php/logout.php"><i class=' bx bxs-log-out'></i> <span
                    class="menu-label">Logout</span></a></div>
    </div>
</div>