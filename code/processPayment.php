<?php
session_start();
@include '../link/db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Student') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_SESSION['userID'];

    // 1. Get customer ID
    $custQuery = mysqli_query($conn, "SELECT custID, membershipPoints FROM customer WHERE userID = '$userID'");
    $custData = mysqli_fetch_assoc($custQuery);
    $custID = $custData['custID'];
    $currentPoints = $custData['membershipPoints'];

    // 2. Get posted data
    $orderIDs = explode(',', $_POST['orderIDs']);
    $paymentMethod = $_POST['paymentMethod'];
    $finalAmount = floatval($_POST['finalAmount']);
    $pointsUsed = intval($_POST['pointsToUse']);

    // Validate
    if ($pointsUsed > $currentPoints) {
        die("Error: You cannot use more points than you have.");
    }

    $paymentDate = date('Y-m-d H:i:s');

    // 3. Deduct points and calculate new earned points (RM0.20 = 1 point)
    $remainingPoints = $currentPoints - $pointsUsed;
    $earnedPoints = floor($finalAmount / 0.20);
    $newTotalPoints = $remainingPoints + $earnedPoints;

    // Update points in customer table
    $updatePoints = $conn->prepare("UPDATE customer SET membershipPoints = ? WHERE custID = ?");
    $updatePoints->bind_param("ii", $newTotalPoints, $custID);
    $updatePoints->execute();

    // 4. Insert payment for each order
    foreach ($orderIDs as $orderID) {
        // Insert payment record
        $stmt = $conn->prepare("INSERT INTO payment (orderID, custID, paymentMethod, amount, paymentDate) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $orderID, $custID, $paymentMethod, $finalAmount, $paymentDate);
        $stmt->execute();

        $paymentID = $stmt->insert_id;

        // Update order with new payment status if needed
        $updateOrder = $conn->prepare("UPDATE `order` SET paymentID = ?, orderStatus = 'Paid' WHERE orderID = ?");
        $updateOrder->bind_param("ii", $paymentID, $orderID);
        $updateOrder->execute();

        // Insert status history
        $statusStmt = $conn->prepare("INSERT INTO statushistory (orderID, orderStatus, statusDate) VALUES (?, 'Paid', ?)");
        $statusStmt->bind_param("is", $orderID, $paymentDate);
        $statusStmt->execute();
    }

    // Redirect to orderStudent.php with success message
    header("Location: orderStudent.php?payment=success");
    exit;
} else {
    echo "Invalid request.";
}
?>
