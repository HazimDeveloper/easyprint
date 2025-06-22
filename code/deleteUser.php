<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'Staff') {
    header('Location: login.php');
    exit;
}

@include '../link/db_connect.php';

if (isset($_GET['userID'])) {
    $userID = $_GET['userID'];

    // Delete the user
    $query = "DELETE FROM user WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);

    if ($stmt->execute()) {
        header("Location: userManagement.php?message=User deleted successfully.");
        exit;
    } else {
        echo "Error deleting user.";
    }
} else {
    echo "Invalid request.";
}
?>
