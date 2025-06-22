<?php
session_start();
@include '../link/db_connect.php';

if (isset($_POST['updateOrder'])) {
    $orderID = mysqli_real_escape_string($conn, $_POST['orderID']);
    $pickupDate = mysqli_real_escape_string($conn, $_POST['pickupDate']);
    $pickupTime = mysqli_real_escape_string($conn, $_POST['pickupTime']);
    $orderStatus = mysqli_real_escape_string($conn, $_POST['orderStatus']);

    // Begin transaction
    mysqli_autocommit($conn, false);
    
    try {
        // Update order table
        $updateOrder = "UPDATE `order` SET pickupDate = '$pickupDate', pickupTime = '$pickupTime' WHERE orderID = '$orderID'";
        
        if (!mysqli_query($conn, $updateOrder)) {
            throw new Exception("Failed to update order: " . mysqli_error($conn));
        }

        // Update status history if status is provided
        if (!empty($orderStatus)) {
            // Check if status history already exists for this order
            $checkStatus = "SELECT * FROM statushistory WHERE orderID = '$orderID'";
            $statusResult = mysqli_query($conn, $checkStatus);
            
            if (mysqli_num_rows($statusResult) > 0) {
                // Update existing status
                $updateStatus = "UPDATE statushistory SET orderStatus = '$orderStatus', statusDate = CURDATE() WHERE orderID = '$orderID'";
                if (!mysqli_query($conn, $updateStatus)) {
                    throw new Exception("Failed to update status: " . mysqli_error($conn));
                }
            } else {
                // Insert new status
                $insertStatus = "INSERT INTO statushistory (orderID, orderStatus, statusDate) VALUES ('$orderID', '$orderStatus', CURDATE())";
                if (!mysqli_query($conn, $insertStatus)) {
                    throw new Exception("Failed to insert status: " . mysqli_error($conn));
                }
            }
        }
        
        // Commit transaction
        mysqli_commit($conn);
        $_SESSION['status'] = "Order updated successfully.";
        
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        $_SESSION['error'] = "Update failed: " . $e->getMessage();
    }
    
    // Re-enable autocommit
    mysqli_autocommit($conn, true);
    
    // Redirect based on user role
    if ($_SESSION['role'] === 'Student') {
        header('Location: orderStudent.php');
    } else {
        header('Location: orderManagement.php');
    }
    exit;
}

// Handle file upload for students
if (isset($_FILES['uploadFileName']) && $_FILES['uploadFileName']['error'] === 0) {
    if ($_FILES['uploadFileName']['size'] > 5 * 1024 * 1024) {
        $_SESSION['error'] = "File exceeds 5MB limit.";
        header('Location: orderStudent.php');
        exit;
    }
    
    $uploadFile = uniqid('file_', true) . '_' . basename($_FILES['uploadFileName']['name']);
    $uploadPath = "../uploads/" . $uploadFile;
    
    if (!is_dir("../uploads/")) {
        mkdir("../uploads/", 0777, true);
    }
    
    if (move_uploaded_file($_FILES['uploadFileName']['tmp_name'], $uploadPath)) {
        $orderID = mysqli_real_escape_string($conn, $_POST['orderID']);
        $updateFile = "UPDATE `order` SET uploadFileName = '$uploadFile' WHERE orderID = '$orderID'";
        mysqli_query($conn, $updateFile);
    }
}
?>