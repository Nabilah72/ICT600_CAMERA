<?php
session_start();
include "connection.php";

function generateCustomerID($connect)
{
    $sql = "SELECT MAX(CAST(SUBSTRING(custID, 5) AS UNSIGNED)) AS maxID FROM customer WHERE custID LIKE 'CUS%'";
    $result = $connect->query($sql);
    $newNumber = $result && $result->num_rows > 0 ? $result->fetch_assoc()['maxID'] + 1 : 1;
    return 'CUS' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}

function titleCase($string)
{
    return ucwords(strtolower(trim($string)));
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add') {
    $custID = generateCustomerID($connect);
    $name = titleCase($_POST['name']);
    $phone = preg_replace("/\D/", "", subject: $_POST['phone']);
    $email = strtolower(trim($_POST['email']));

    $stmt = $connect->prepare("INSERT INTO customer (custID, custName, custPhone, custEmail) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $custID, $name, $phone, $email);
    $stmt->execute() ? header("Location: customer.php?success=added") : header("Location: customer.php?error=add");
} 

elseif ($action === 'edit') {
    $stmt = $connect->prepare("SELECT * FROM customer WHERE custID = ?");
    $stmt->bind_param("s", $_POST['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $name = titleCase($_POST['name']);
        $phone = preg_replace("/\D/", "", subject: $_POST['phone']);
        $email = strtolower(trim($_POST['email']));

        $stmt = $connect->prepare("UPDATE customer SET custName = ?, custPhone = ?, custEmail = ? WHERE custID = ?");
        $stmt->bind_param("ssss", $name, $phone, $email, $_POST['id']);
        $stmt->execute() ? header("Location: customer.php?success=updated") : header("Location: customer.php?error=update");
    } else {
        header("Location: customer.php?error=invalidID");
    }
} 

elseif ($action === 'delete') {
    $stmt = $connect->prepare("DELETE FROM customer WHERE custID = ?");
    $stmt->bind_param("s", $_GET['id']);
    $stmt->execute() ? header("Location: customer.php?success=deleted") : header("Location: customer.php?error=delete");
}
?>