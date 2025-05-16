<?php
session_start();
include "connection.php";

function generateCustomerID($connect)
{
    $sql = "SELECT MAX(CAST(SUBSTRING(custID, 4) AS UNSIGNED)) AS maxID
            FROM customer WHERE custID LIKE 'CUS%'";
    $result = $connect->query($sql);
    $newNumber = ($result && $result->num_rows > 0)
        ? $result->fetch_assoc()['maxID'] + 1
        : 1;
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
    $phone = trim($_POST['phone']);
    $email = strtolower(trim($_POST['email']));

    // 1) Validate name
    if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        header("Location: customer.php?error=invalidName");
        exit;
    }

    // 2) Validate phone format
    if (!preg_match('/^\d+$/', $phone)) {
        header("Location: customer.php?error=invalidPhone");
        exit;
    }

    // 3) Check duplicates
    $dupStmt = $connect->prepare(
        "SELECT custID FROM customer
         WHERE custPhone = ? OR custEmail = ?"
    );
    $dupStmt->bind_param("ss", $phone, $email);
    $dupStmt->execute();
    $dupStmt->store_result();
    if ($dupStmt->num_rows > 0) {
        // find which field is duplicate
        $dupStmt->bind_result($existingID);
        $dupStmt->fetch();
        // Check specifically
        $chk = $connect->prepare(
            "SELECT 1 FROM customer WHERE custPhone = ?"
        );
        $chk->bind_param("s", $phone);
        $chk->execute();
        $chk->store_result();
        if ($chk->num_rows > 0) {
            header("Location: customer.php?error=duplicatePhone");
            exit;
        }
        header("Location: customer.php?error=duplicateEmail");
        exit;
    }
    $dupStmt->close();

    // 4) Insert
    $stmt = $connect->prepare(
        "INSERT INTO customer
         (custID, custName, custPhone, custEmail)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssss", $custID, $name, $phone, $email);
    $stmt->execute()
        ? header("Location: customer.php?success=added")
        : header("Location: customer.php?error=add");

} elseif ($action === 'edit') {
    $id = $_POST['id'];
    $name = titleCase($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = strtolower(trim($_POST['email']));

    // Validate name & phone
    if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        header("Location: customer.php?error=invalidName");
        exit;
    }
    if (!preg_match('/^\d+$/', $phone)) {
        header("Location: customer.php?error=invalidPhone");
        exit;
    }

    // Check record exists
    $exists = $connect->prepare(
        "SELECT 1 FROM customer WHERE custID = ?"
    );
    $exists->bind_param("s", $id);
    $exists->execute();
    $exists->store_result();
    if ($exists->num_rows === 0) {
        header("Location: customer.php?error=invalidID");
        exit;
    }
    $exists->close();

    // Check duplicates in others
    $dupStmt = $connect->prepare(
        "SELECT custID, custPhone, custEmail
         FROM customer
         WHERE (custPhone = ? OR custEmail = ?) AND custID <> ?"
    );
    $dupStmt->bind_param("sss", $phone, $email, $id);
    $dupStmt->execute();
    $dupStmt->store_result();
    if ($dupStmt->num_rows > 0) {
        $dupStmt->bind_result($eID, $ePhone, $eEmail);
        $dupStmt->fetch();
        if ($ePhone === $phone) {
            header("Location: customer.php?error=duplicatePhone");
            exit;
        }
        header("Location: customer.php?error=duplicateEmail");
        exit;
    }
    $dupStmt->close();

    // Update
    $stmt = $connect->prepare(
        "UPDATE customer
         SET custName = ?, custPhone = ?, custEmail = ?
         WHERE custID = ?"
    );
    $stmt->bind_param("ssss", $name, $phone, $email, $id);
    $stmt->execute()
        ? header("Location: customer.php?success=updated")
        : header("Location: customer.php?error=update");

} elseif ($action === 'delete') {
    $id = $_POST['id'];
    $stmt = $connect->prepare(
        "DELETE FROM customer WHERE custID = ?"
    );
    $stmt->bind_param("s", $id);
    $stmt->execute()
        ? header("Location: customer.php?success=deleted")
        : header("Location: customer.php?error=delete");
}

exit;
