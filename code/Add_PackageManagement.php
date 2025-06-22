<?php
session_start();

@include '../link/db_connect.php';

if (isset($_POST['addPackage'])) {
    $packageName = mysqli_real_escape_string($conn, $_POST['packageName']);
    $colorOption = mysqli_real_escape_string($conn, $_POST['colorOption']);
    $packagePrice = mysqli_real_escape_string($conn, $_POST['price']);
    $availabilityStatus = mysqli_real_escape_string($conn, $_POST['availabilityStatus']);
    $availabilityDate = mysqli_real_escape_string($conn, $_POST['availabilityDate']);

    // Fallback to today's date if no date was chosen
    if (empty($availabilityDate)) {
        $availabilityDate = date('Y-m-d');
    }

    $insertPackage = "INSERT INTO package (packageName, colorOption, price, availabilityStatus, availabilityDate) 
                      VALUES ('$packageName', '$colorOption', '$packagePrice', '$availabilityStatus', '$availabilityDate')";

    if (mysqli_query($conn, $insertPackage)) {
        $_SESSION['status'] = "Package added successfully!";
    } else {
        $_SESSION['error'] = "Error adding package: " . mysqli_error($conn);
    }

    header('Location: packageManagement.php');
    exit;
}
?>
