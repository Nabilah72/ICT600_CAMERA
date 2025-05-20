<?php
session_start();
include "connection.php";

function generateSupplierID($connect)
{
    $sql = "SELECT MAX(CAST(SUBSTRING(suppID, 4) AS UNSIGNED)) AS maxID FROM supplier WHERE suppID LIKE 'SUP%'";
    $result = $connect->query($sql);
    $newNumber = $result && $result->num_rows > 0 ? $result->fetch_assoc()['maxID'] + 1 : 1;
    return 'SUP' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}

function titleCase($string)
{
    return ucwords(strtolower(trim($string)));
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add') {
    $suppID = generateSupplierID($connect);
    $name = titleCase($_POST['name']);
    if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        header("Location: supplier.php?error=invalidName");
        exit;
    }
    $phone = preg_replace("/\D/", "", $_POST['phone']);
    $email = strtolower(trim($_POST['email']));
    $address = titleCase($_POST['address']);
    $status = $_POST['suppStatus'];

    $stmt = $connect->prepare("INSERT INTO supplier (suppID, suppName, suppPhone, suppEmail, suppAddress, suppStatus) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $suppID, $name, $phone, $email, $address, $status);
    $stmt->execute() ? header("Location: supplier.php?success=added") : header("Location: supplier.php?error=add");
} elseif ($action === 'edit') {
    $stmt = $connect->prepare("SELECT * FROM supplier WHERE suppID = ?");
    $stmt->bind_param("s", $_POST['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $name = titleCase($_POST['name']);
        if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
            header("Location: supplier.php?error=invalidName");
            exit;
        }
        $phone = preg_replace("/\D/", "", $_POST['phone']);
        $email = strtolower(trim($_POST['email']));
        $address = titleCase($_POST['address']);
        $status = $_POST['suppStatus'];

        $stmt = $connect->prepare("UPDATE supplier SET suppName = ?, suppPhone = ?, suppEmail = ?, suppAddress = ?, suppStatus = ? WHERE suppID = ?");
        $stmt->bind_param("ssssss", $name, $phone, $email, $address, $status, $_POST['id']);
        $stmt->execute() ? header("Location: supplier.php?success=updated") : header("Location: supplier.php?error=update");
    } else {
        header("Location: supplier.php?error=invalidID");
    }
} elseif ($action === 'delete') {
    $stmt = $connect->prepare("DELETE FROM supplier WHERE suppID = ?");
    $stmt->bind_param("s", $_POST['id']);
    $stmt->execute() ? header("Location: supplier.php?success=deleted") : header("Location: supplier.php?error=delete");
    exit;
} else {
    header("Location: supplier.php");
    exit;
}
?>