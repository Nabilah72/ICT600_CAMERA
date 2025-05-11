<?php
session_start();
include "connection.php";

// Function to generate a unique Supplier ID in the format SUP001, SUP002, etc.
function generateSupplierID($connect)
{
    // Get the highest numeric part of the current supplier IDs
    $sql = "SELECT MAX(CAST(SUBSTRING(suppID, 4) AS UNSIGNED)) AS maxID FROM supplier WHERE suppID LIKE 'SUP%'";
    $result = $connect->query($sql);

    // If a maximum ID is found, increment it; otherwise, start from 1
    $newNumber = $result && $result->num_rows > 0 ? $result->fetch_assoc()['maxID'] + 1 : 1;

    // Return new supplier ID with leading zeros
    return 'SUP' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}

function titleCase($string)
{
    return ucwords(strtolower(trim($string)));
}

// Determine the action requested (add, edit, or delete)
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Handle "Add Supplier" action
if ($action === 'add') {
    // Generate a new supplier ID
    $suppID = generateSupplierID($connect);
    // Get form data
    $name = titleCase($_POST['name']);
    if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        header("Location: customer.php?error=invalidName");
        exit;
    }
    $phone = preg_replace("/\D/", "", subject: $_POST['phone']);
    $email = strtolower(trim($_POST['email']));
    $address = titleCase($_POST['address']);
    $status = $_POST['suppStatus'];

    // Prepare and execute the insert statement
    $stmt = $connect->prepare("INSERT INTO supplier (suppID, suppName, suppPhone, suppEmail, suppAddress, suppStatus) VALUES (?, ?, ?, ?, ?,?)");
    $stmt->bind_param("ssssss", $suppID, $name, $phone, $email, $address, $status);

    // Redirect based on success or failure
    $stmt->execute() ? header("Location: supplier.php?success=added") : header("Location: supplier.php?error=add");
}

// Handle "Edit Supplier" action
elseif ($action === 'edit') {
    $stmt = $connect->prepare("SELECT * FROM supplier WHERE suppID = ?");
    $stmt->bind_param("s", $_POST['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $name = titleCase($_POST['name']);
        if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
            header("Location: customer.php?error=invalidName");
            exit;
        }
        $phone = preg_replace("/\D/", "", subject: $_POST['phone']);
        $email = strtolower(trim($_POST['email']));
        $address = titleCase($_POST['address']);
        $status = $_POST['suppStatus'];

        $stmt = $connect->prepare("UPDATE supplier SET suppName = ?, suppPhone = ?, suppEmail = ?, suppAddress = ?, suppStatus = ? WHERE suppID = ?");
        $stmt->bind_param("ssssss", $name, $phone, $email, $address, $status, $_POST['id']);
        $stmt->execute() ? header("Location: supplier.php?success=updated") : header("Location: supplier.php?error=update");
    } else {
        header("Location: supplier.php?error=invalidID");
    }
}
?>