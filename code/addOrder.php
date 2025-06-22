<?php
session_start();
@include '../link/db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Student') {
    echo "unauthorized"; exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Required field validation
    if (
        empty($_POST['custID']) || empty($_POST['packageID']) || empty($_POST['quantity']) ||
        empty($_POST['pickupDate']) || empty($_POST['pickupTime']) || empty($_POST['price'])
    ) {
        echo "missing fields"; exit;
    }

    $custID     = $_POST['custID'];
    $packageID  = $_POST['packageID'];
    $quantity   = $_POST['quantity'];
    $pickupDate = $_POST['pickupDate'];
    $pickupTime = $_POST['pickupTime'];
    $price      = $_POST['price'];
    $total      = $price * $quantity;

    // ✅ File upload handling
    $uploadFile = '';
    if (isset($_FILES['uploadFile']) && $_FILES['uploadFile']['error'] === 0) {
        $uploadFile = uniqid('file_', true) . '_' . basename($_FILES['uploadFile']['name']);
        $uploadPath = "../uploads/" . $uploadFile;
        if (!move_uploaded_file($_FILES['uploadFile']['tmp_name'], $uploadPath)) {
            echo "file upload failed"; exit;
        }
    } else {
        echo "no file"; exit;
    }

    // ✅ Insert into `order`
    $stmt = $conn->prepare("INSERT INTO `order` 
        (staffID, custID, orderDate, orderType, orderQuantity, pickupDate, pickupTime, uploadFileName, totalAmount)
        VALUES (NULL, ?, CURDATE(), 'Print', ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissssd", $custID, $quantity, $pickupDate, $pickupTime, $uploadFile, $total);
    if (!$stmt->execute()) {
        echo "order insert failed: " . $stmt->error; exit;
    }
    $orderID = $stmt->insert_id;

    // ✅ Insert into `orderpackage`
    $stmt2 = $conn->prepare("INSERT INTO orderpackage (orderID, packageID, orderPackageQuantity)
                             VALUES (?, ?, ?)");
    $stmt2->bind_param("iii", $orderID, $packageID, $quantity);
    if (!$stmt2->execute()) {
        echo "package insert failed: " . $stmt2->error; exit;
    }

    echo "success";
} else {
    echo "invalid request";
}
?>
