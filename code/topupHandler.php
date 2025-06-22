<?php
session_start();
@include '../link/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Student') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$userID = $_SESSION['userID'];
$amount = floatval($_POST['topupAmount'] ?? 0);
$method = $_POST['method'] ?? '';



if ($amount > 0 && in_array($method, ['FPX', 'Credit Card', 'E-Wallet'])) {
    // Get current balance
    $query = "SELECT easyPayBalance FROM customer WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $currentBalance = 0;
    if ($row = $result->fetch_assoc()) {
        $currentBalance = floatval($row['easyPayBalance']);
    }

    // Update balance
    $newBalance = $currentBalance + $amount;
    $update = "UPDATE customer SET easyPayBalance = ? WHERE userID = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("di", $newBalance, $userID);

    error_log("Amount: $amount");
error_log("Method: $method");
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => "Top-up of RM$amount via $method was successful!",
            'newBalance' => number_format($newBalance, 2)
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid top-up amount or method.',
            'debug' => [
                'amount' => $amount,
                'method' => $method
            ]
        ]);
    
    }
}
?>
