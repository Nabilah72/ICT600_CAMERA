<!-- sidebar.php -->
<style>
    .sidebar {
        width: 220px;
        background-color: #333;
        color: #fff;
        padding: 20px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
    }

    .sidebar h2 {
        margin-top: 0;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
    }

    .sidebar ul li {
        margin: 10px 0;
    }

    .sidebar ul li a {
        color: #fff;
        text-decoration: none;
        padding: 8px 12px;
        display: block;
        border-radius: 4px;
        transition: background 0.3s;
    }

    .sidebar ul li a:hover,
    .sidebar ul li a.active {
        background-color: #555;
    }

    .wrapper {
        display: flex;
        min-height: 100vh;
    }
</style>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="staff.php">Staff Management</a></li>
        <li><a href="supplier.php">Supplier Management</a></li>
        <li><a href="product.php">Product Management</a></li>
        <li><a href="order.php">Order Management</a></li>
        <li><a href="customer.php">Customer Management</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>