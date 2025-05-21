<!-- Boxicons CDN for icons -->
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<style>
    /* Sidebar container */
    .sidebar {
        width: 80px;
        /* collapsed width */
        background-color: #333;
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
        /* above other content */
    }

    /* Expand sidebar on hover */
    .sidebar:hover {
        width: 190px;
        align-items: flex-start;
    }

    /* Logo container styling */
    .logo-container {
        display: flex;
        text-align: center;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin-bottom: 20px;
    }

    /* Logo image styling */
    .logo-img {
        cursor: pointer;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        transition: all 0.3s ease;
    }

    /* Logo link styling */
    .logo-link {
        text-decoration: none;
        color: inherit;
        display: inline-block;
    }

    /* Enlarge logo image on sidebar hover */
    .sidebar:hover .logo-img {
        width: 50px;
        height: 50px;
        margin: 0 5px;
    }

    /* Logo text styling - hidden by default */
    .logo-text {
        color: white;
        margin-right: 10px;
        font-size: 1.0rem;
        font-weight: bold;
        display: none;
        white-space: nowrap;
    }

    /* Show logo text on sidebar hover */
    .sidebar:hover .logo-text {
        display: inline;
    }

    /* Container for top and bottom menu items */
    .menu-top,
    .menu-bottom {
        display: flex;
        flex-direction: column;
        width: 90%;
        margin: 10px;
    }

    /* Individual menu item container */
    .menu-item {
        width: 100%;
        margin: 10px 0;
    }

    /* Menu links styling */
    .menu-item a {
        display: flex;
        align-items: center;
        gap: 10px;
        color: white;
        text-decoration: none;
        padding: 10px;
        border-radius: 8px;
        transition: background 0.2s ease;
        white-space: nowrap;
        justify-content: center;
        /* center icon/text when collapsed */
    }

    /* Icon size and color */
    .menu-item a i {
        font-size: 23px;
        color: white;
    }

    /* Hover and active states for menu links */
    .menu-item a:hover,
    .menu-item a.active {
        background-color: #ffc107;
        /* highlight color */
        color: #000;
    }

    /* Menu label text - hidden by default */
    .menu-label {
        display: none;
    }

    /* Push bottom menu to bottom */
    .menu-bottom {
        margin-top: auto;
    }

    /* Show menu labels on sidebar hover */
    .sidebar:hover .menu-label {
        display: inline;
    }

    /* Align menu items text left and fill width on hover */
    .sidebar:hover .menu-item a {
        justify-content: flex-start;
        width: 100%;
    }
</style>

<div class="sidebar">
    <!-- Logo with link -->
    <a href="../php/homepage.php" class="logo-link">
        <div class="logo-container">
            <img src="../images/logo.png" alt="logo" class="logo-img">
            <span class="logo-text">S.A Camera</span>
        </div>
    </a>

    <!-- Top menu items -->
    <div class="menu-top">
        <div class="menu-item"><a href="../php/homepage.php"><i class='bx bxs-home'></i> <span
                    class="menu-label">Home</span></a></div>
        <div class="menu-item"><a href="../php/user.php"><i class='bx bxs-user'></i> <span
                    class="menu-label">User</span></a></div>
        <div class="menu-item"><a href="../php/product.php"><i class='bx bxs-camera'></i><span
                    class="menu-label">Product</span></a></div>
        <div class="menu-item"><a href="../php/supplier.php"><i class='bx bxs-truck'></i> <span
                    class="menu-label">Supplier</span></a></div>
    </div>

    <!-- Bottom menu items (profile and logout) -->
    <div class="menu-bottom">
        <div class="menu-item"><a href="../php/profile.php"><i class='bx bxs-user'></i> <span
                    class="menu-label">Profile</span></a></div>
        <div class="menu-item">
            <!-- Logout with confirmation -->
            <a href="../index.html" onclick="return confirmLogout();">
                <i class='bx bxs-log-out'></i>
                <span class="menu-label">Logout</span>
            </a>
        </div>
    </div>
</div>

<script>
    // Confirm logout dialog
    function confirmLogout() {
        return confirm("Are you sure you want to logout?");
    }
</script>