<?php
include "connection.php";

$orders = $connect->query("SELECT o.orderID, c.custName FROM orders o JOIN customer c ON o.custID = c.custID");
$products = $connect->query("SELECT * FROM product");

$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Orders Product</title>
    <link rel="stylesheet" href="../css/crudd.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="container">
            <h1>Orders - Product List</h1>
            <input type="text" id="searchInput" placeholder="Search staff..." class="search-box"><br>

            <button id="openAddModal">Add Order Item</button><br><br>

            <?php if ($success): ?>
                <div class="message success"><?= ucfirst($success) ?>!</div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="message error"><?= ucfirst($error) ?>. Please try again.</div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th class="sortable">No.</th>
                        <th class="sortable">Order ID</th>
                        <th class="sortable">Product</th>
                        <th class="sortable">Unit Price</th>
                        <th class="sortable">Quantity</th>
                        <th class="sortable">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT op.*, p.brand, p.model FROM orders_product op
                      JOIN product p ON op.productID = p.productID";
                    $result = $connect->query($query);
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['orderID']}</td>
                        <td>{$row['brand']} - {$row['model']}</td>
                        <td>RM " . number_format($row['unitPrice'], 2) . "</td>
                        <td>{$row['qty']}</td>
                        <td>RM " . number_format($row['subtotal'], 2) . "</td>
                      </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="popup-modal" id="addModal">
        <div class="popup-content">
            <span class="close-btn" id="closeAdd">&times;</span>
            <h2>Add Product to Order</h2>
            <form action="ordersProduct_crud.php?action=add" method="POST" id="ordersProductForm">
                <div class="form-group">
                    <label>Order</label>
                    <select name="orderID" required>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                            <option value="<?= $order['orderID'] ?>"><?= $order['orderID'] ?> - <?= $order['custName'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div id="productContainer">
                    <div class="product-group">
                        <select name="productID[]" class="product-select" required>
                            <?php
                            $products->data_seek(0);
                            while ($prod = $products->fetch_assoc()):
                                ?>
                                <option value="<?= $prod['productID'] ?>"><?= $prod['brand'] ?> - <?= $prod['model'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <input type="number" name="qty[]" placeholder="Quantity" min="1" required>
                        <button type="button" class="removeBtn"><i class='bx bxs-x-circle'></i></button>
                    </div>
                </div>

                <button type="button" id="addProductBtn">+ Add Product</button>

                <div class="form-actions">
                    <button type="submit" class="blueBtn">Submit</button>
                    <button type="button" id="cancelAdd">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/searchsort.js"></script>
    <script>
        const addModal = document.getElementById('addModal');
        document.getElementById('openAddModal').onclick = () => addModal.classList.add('show');
        document.getElementById('closeAdd').onclick = () => addModal.classList.remove('show');
        document.getElementById('cancelAdd').onclick = () => addModal.classList.remove('show');
        window.onclick = (e) => { if (e.target === addModal) addModal.classList.remove('show'); };

        const container = document.getElementById('productContainer');
        const addBtn = document.getElementById('addProductBtn');

        addBtn.onclick = () => {
            const clone = container.firstElementChild.cloneNode(true);
            clone.querySelector('input').value = '';
            clone.querySelector('select').selectedIndex = 0;
            container.appendChild(clone);
        };

        container.addEventListener('click', function (e) {
            if (e.target.classList.contains('bx') && container.children.length > 1) {
                e.target.closest('.product-group').remove();
            }
        });

        document.getElementById('ordersProductForm').addEventListener('submit', function (e) {
            const selects = Array.from(document.querySelectorAll('.product-select'));
            const values = selects.map(select => select.value);
            const hasDuplicate = values.some((v, i) => values.indexOf(v) !== i);
            if (hasDuplicate) {
                e.preventDefault();
                alert("Duplicate product selected. Please choose unique products.");
            }
        });
    </script>
</body>

</html>