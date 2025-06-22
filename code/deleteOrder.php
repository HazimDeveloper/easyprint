<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Student') {
    header('Location: login.php');
    exit;
}

@include '../link/db_connect.php';

if (isset($_GET['orderID'])) {
    $orderID = mysqli_real_escape_string($conn, $_GET['orderID']);

    // Step 1: Delete from orderpackage table (foreign key dependency)
    $deleteOrderPackage = "DELETE FROM orderpackage WHERE orderID = ?";
    $stmt = $conn->prepare($deleteOrderPackage);
    $stmt->bind_param("i", $orderID);
    $stmt->execute();

    // Step 2: Delete from order table
    $deleteOrder = "DELETE FROM `order` WHERE orderID = ?";
    $stmt = $conn->prepare($deleteOrder);
    $stmt->bind_param("i", $orderID);

    if ($stmt->execute()) {
        $_SESSION['status'] = "Order deleted successfully!";
        header('Location: orderStudent.php');
        exit;
    } else {
        $_SESSION['error'] = "Error deleting order: " . mysqli_error($conn);
        header('Location: orderStudent.php');
    }
} else {
    $_SESSION['error'] = "Invalid request. Order ID not found.";
    header('Location: orderStudent.php');
}
?>
