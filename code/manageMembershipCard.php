<?php
session_start();
@include '../link/db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Student') {
    header('Location: login.php');
    exit;
}

$userID = $_SESSION['userID'];

// Get customer data
$customerQuery = "SELECT c.*, u.username FROM customer c 
                  JOIN user u ON c.userID = u.userID 
                  WHERE c.userID = '$userID'";
$customerResult = mysqli_query($conn, $customerQuery);
$customer = mysqli_fetch_assoc($customerResult);

if (!$customer) {
    echo "Customer record not found.";
    exit;
}

// Handle membership card application
if (isset($_POST['applyCard'])) {
    $cardBalance = floatval($_POST['cardBalance'] ?? 0);
    
    if ($cardBalance >= 10.00) { // Minimum balance requirement
        // Check if card already exists
        $checkCardQuery = "SELECT * FROM membershipcard WHERE custID = '{$customer['custID']}'";
        $checkResult = mysqli_query($conn, $checkCardQuery);
        
        if (mysqli_num_rows($checkResult) === 0) {
            $applyDate = date('Y-m-d');
            $balanceDate = date('Y-m-d');
            
            $insertCard = "INSERT INTO membershipcard (custID, cardBalance, balanceDate, applyDate, totalPoint, status)
                          VALUES ('{$customer['custID']}', '$cardBalance', '$balanceDate', '$applyDate', 0, 'Active')";
            
            if (mysqli_query($conn, $insertCard)) {
                $_SESSION['status'] = "Membership card applied successfully!";
            } else {
                $_SESSION['error'] = "Failed to apply for membership card.";
            }
        } else {
            $_SESSION['error'] = "You already have a membership card.";
        }
    } else {
        $_SESSION['error'] = "Minimum card balance is RM 10.00";
    }
    
    header("Location: manageMembershipCard.php");
    exit;
}

// Get existing membership card if any
$cardQuery = "SELECT * FROM membershipcard WHERE custID = '{$customer['custID']}'";
$cardResult = mysqli_query($conn, $cardQuery);
$membershipCard = mysqli_fetch_assoc($cardResult);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Card Management</title>
    <link rel="stylesheet" href="../link/style.css">
    <link rel="stylesheet" href="dashboardStudent.css">
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

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="material-symbols-outlined me-2">error</span>
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <h1><span>Membership Card Management</span></h1>

    <div class="row">
        <div class="col-md-8">
            <?php if ($membershipCard): ?>
                <!-- Existing Card Display -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Your Membership Card</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Card ID:</strong> #<?= $membershipCard['cardID'] ?></p>
                                <p><strong>Member Name:</strong> <?= htmlspecialchars($customer['name']) ?></p>
                                <p><strong>Student ID:</strong> <?= htmlspecialchars($customer['studentID']) ?></p>
                                <p><strong>Status:</strong> <span class="badge bg-success"><?= $membershipCard['status'] ?></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Card Balance:</strong> RM <?= number_format($membershipCard['cardBalance'], 2) ?></p>
                                <p><strong>Total Points:</strong> <?= $membershipCard['totalPoint'] ?></p>
                                <p><strong>Apply Date:</strong> <?= date('d M Y', strtotime($membershipCard['applyDate'])) ?></p>
                                <p><strong>Last Updated:</strong> <?= date('d M Y', strtotime($membershipCard['balanceDate'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top-up Section -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Top-up Card Balance</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="topupAmount" class="form-label">Top-up Amount (RM)</label>
                                        <input type="number" class="form-control" id="topupAmount" name="topupAmount" step="0.01" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="paymentMethod" class="form-label">Payment Method</label>
                                        <select class="form-select" id="paymentMethod" name="paymentMethod" required>
                                            <option value="">-- Select Method --</option>
                                            <option value="FPX">FPX</option>
                                            <option value="Credit Card">Credit Card</option>
                                            <option value="E-Wallet">E-Wallet</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="topupCard" class="btn btn-primary">Top-up Now</button>
                        </form>
                    </div>
                </div>

            <?php else: ?>
                <!-- Apply for New Card -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Apply for Membership Card</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-4">Get your EasyPrint membership card to enjoy exclusive benefits and rewards!</p>
                        
                        <div class="mb-4">
                            <h6>Benefits:</h6>
                            <ul>
                                <li>Earn points with every purchase</li>
                                <li>Special discounts for members</li>
                                <li>Priority service</li>
                                <li>Easy payment with card balance</li>
                            </ul>
                        </div>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="cardBalance" class="form-label">Initial Card Balance (RM)</label>
                                <input type="number" class="form-control" id="cardBalance" name="cardBalance" step="0.01" min="10" value="10.00" required>
                                <div class="form-text">Minimum initial balance: RM 10.00</div>
                            </div>
                            <button type="submit" name="applyCard" class="btn btn-success">Apply for Membership Card</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <!-- Member Info -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Member Information</h6>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> <?= htmlspecialchars($customer['name']) ?></p>
                    <p><strong>Student ID:</strong> <?= htmlspecialchars($customer['studentID']) ?></p>
                    <p><strong>Current Points:</strong> <?= $customer['membershipPoints'] ?></p>
                    <p><strong>EasyPay Balance:</strong> RM <?= number_format($customer['easyPayBalance'], 2) ?></p>
                    <p><strong>Status:</strong> <span class="badge bg-<?= $customer['verificationStatus'] === 'Verified' ? 'success' : 'warning' ?>"><?= $customer['verificationStatus'] ?></span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../link/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>