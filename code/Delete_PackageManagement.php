<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'Staff') {
    header('Location: login.php');
    exit;
}

@include '../link/db_connect.php';

if (isset($_GET['packageID'])) {
    $packageID = mysqli_real_escape_string($conn, $_GET['packageID']);

    // Delete directly from the package table
    $deletePackage = "DELETE FROM package WHERE packageID = ?";
    $stmt = $conn->prepare($deletePackage);
    $stmt->bind_param("i", $packageID);

    if ($stmt->execute()) {
        $_SESSION['status'] = "Package deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting package: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header('Location: packageManagement.php');
    exit;
} else {
    $_SESSION['error'] = "No package selected for deletion.";
    header('Location: packageManagement.php');
    exit;
}
