<?php
session_start();
@include '../link/db_connect.php';

if (isset($_POST['updateOrder'])) {
    $orderID = $_POST['orderID'];
    $orderStatus = $_POST['orderStatus'];

    $update = "UPDATE `order` SET orderStatus = '$orderStatus' WHERE orderID = '$orderID'";
    if (mysqli_query($conn, $update)) {
        $_SESSION['status'] = "Order updated successfully.";
    } else {
        $_SESSION['error'] = "Update failed: " . mysqli_error($conn);
    }
    header('Location: orderStudent.php');
}

if ($_FILES['uploadFileName']['size'] > 5 * 1024 * 1024) {
    echo "File exceeds 5MB limit.";
    exit;
}

