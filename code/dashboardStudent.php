<?php
session_start();
@include '../link/db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Student') {
    header('Location: login.php');
    exit;
}

$userID = $_SESSION['userID'];

// Fetch student points
$pointsQuery = "SELECT membershipPoints FROM customer WHERE userID = '$userID'";
$pointsResult = mysqli_query($conn, $pointsQuery);
$pointsRow = mysqli_fetch_assoc($pointsResult);
$currentPoints = $pointsRow['membershipPoints'] ?? 0;

// Fetch EasyPay balance
$balanceQuery = "SELECT easyPayBalance FROM customer WHERE userID = '$userID'";
$balanceResult = mysqli_query($conn, $balanceQuery);
$easyPayBalance = 0.00;
if ($balanceResult && mysqli_num_rows($balanceResult) > 0) {
    $balanceRow = mysqli_fetch_assoc($balanceResult);
    $easyPayBalance = floatval($balanceRow['easyPayBalance']);
}

// Handle top-up submission
if (isset($_POST['topupEasyPay'])) {
    $amount = floatval($_POST['topupAmount'] ?? 0);
    $method = $_POST['method'] ?? '';

    if ($amount > 0 && in_array($method, ['FPX', 'Credit Card', 'E-Wallet'])) {
        $easyPayBalance += $amount;
        $updateBalance = "UPDATE customer SET easyPayBalance = '$easyPayBalance' WHERE userID = '$userID'";
        mysqli_query($conn, $updateBalance);
        $_SESSION['status'] = "Top-up of RM$amount via $method was successful!";
        header("Location: dashboardStudent.php");
        exit;
    } else {
        $_SESSION['status'] = "Invalid top-up amount or method.";
        header("Location: dashboardStudent.php");
        exit;
    }
}

// Fetch order history
$orderQuery = "SELECT o.*, p.packageName, p.colorOption, op.orderPackageQuantity, s.orderStatus
              FROM `order` o
              JOIN customer c ON o.custID = c.custID
              JOIN orderpackage op ON o.orderID = op.orderID
              JOIN package p ON op.packageID = p.packageID
              LEFT JOIN statushistory s ON o.orderID = s.orderID
              WHERE c.userID = '$userID'
              ORDER BY o.orderID DESC";
$orderResult = mysqli_query($conn, $orderQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../link/style.css">
    <link rel="stylesheet" href="dashboardStudent.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../link/sidebarStudent.php'; ?>
<?php include '../link/headerStudent.php'; ?>
<div class="main_content">
    <?php if (isset($_SESSION['status'])): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="material-symbols-outlined me-2">check_circle</span>
            <?= $_SESSION['status']; unset($_SESSION['status']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <h1><span>Student Dashboard</span></h1>

    <div class="row">
        <!-- Membership Card -->
        <div class="col-lg-4 col-sm-12">
            <div class="card face">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-subtitle mb-2">Membership</h6>
                        <h3 class="card-title">Points: <?= $currentPoints ?></h3>
                        <p class="text-muted small">Active</p>
                    </div>
                    <span class="material-symbols-outlined">verified</span>
                </div>
            </div>
        </div>

        <!-- EasyPay Wallet -->
        <div class="col-lg-4 col-sm-12">
            <div class="card payment">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-subtitle mb-2">EasyPay Wallet</h6>
                        <h3 class="card-title" id="walletBalance">RM <?= number_format($easyPayBalance, 2) ?></h3>
                    </div>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#topupModal">Top Up</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Top-Up Modal -->
    <div class="modal fade" id="topupModal" tabindex="-1" aria-labelledby="topupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <form method="POST" id="topupForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="topupModalLabel">Top Up EasyPay Wallet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="topupAmount" class="form-label">Amount (RM)</label>
                        <input type="number" id="topupAmount" name="topupAmount" step="0.01" min="1" class="form-control" required>
                        <select name="method" id="method" class="form-select" required>
                            <option value="">-- Select Payment Method --</option>
                            <option value="FPX">FPX</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="E-Wallet">E-Wallet</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="topupEasyPay" class="btn btn-primary">Simulate Payment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Order History Table -->
    <div class="row pt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Order History</div>
                <div class="card-body table-responsive">
                    <table class="table table-striped" id="studentOrders">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Package</th>
                                <th>Pickup Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = mysqli_fetch_assoc($orderResult)): ?>
                            <tr>
                                <td>#<?= $row['orderID'] ?></td>
                                <td><?= $row['packageName'] ?></td>
                                <td><?= $row['pickupDate'] ?></td>
                                <td><span class="badge bg-info"><?= $row['orderStatus'] ?? 'Pending' ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../link/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        $('#studentOrders').DataTable({ pageLength: 5 });

        $('#topupForm').on('submit', function (e) {
            e.preventDefault();

            const amount = $('#topupAmount').val();
            const method = $('#method').val();

            $.ajax({
                url: 'topupHandler.php', // Because it's in the same folder as dashboardStudent.php
                method: 'POST',
                data: {
                    topupAmount: amount,
                    method: method
                },
                success: function (response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        $('#walletBalance').text('RM ' + response.newBalance); // Update balance text
                        $('#topupModal').modal('hide');
                        $('#topupForm')[0].reset(); // Optional: reset the form
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert('Something went wrong. Please try again.');
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function () {
        $('#studentOrders').DataTable({ pageLength: 5 });
    });
</script>
</body>
</html>
