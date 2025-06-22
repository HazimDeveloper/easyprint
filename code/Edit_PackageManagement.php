<?php
session_start();
@include '../link/db_connect.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if (isset($_POST['updatePackage'])) {
    // Collect and sanitize form data
    $packageID = mysqli_real_escape_string($conn, $_POST['packageID']);
    $packageName = mysqli_real_escape_string($conn, $_POST['packageName']);
    $colorOption = mysqli_real_escape_string($conn, $_POST['colorOption']);
    $packagePrice = mysqli_real_escape_string($conn, $_POST['price']);
    $availabilityStatus = mysqli_real_escape_string($conn, $_POST['status']);
    $availabilityDate = mysqli_real_escape_string($conn, $_POST['availabilityDate']);

    // Fallback to today's date if left empty
    if (empty($availabilityDate)) {
        $availabilityDate = date('Y-m-d');
    }

    // Update the package table directly (availability now in the same table)
    $updatePackage = "UPDATE package 
                      SET packageName = '$packageName', 
                          colorOption = '$colorOption', 
                          price = '$packagePrice', 
                          availabilityStatus = '$availabilityStatus', 
                          availabilityDate = '$availabilityDate' 
                      WHERE packageID = '$packageID'";

    if (mysqli_query($conn, $updatePackage)) {
        $_SESSION['status'] = "Package updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating package: " . mysqli_error($conn);
    }

    header('Location: packageManagement.php');
    exit;
}
?>
