<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Student') {
    header('Location: login.php');
    exit;
}

@include '../link/db_connect.php';

$userID = $_SESSION['userID'];

// Get custID linked to this userID
$custQuery = $conn->prepare("SELECT custID FROM customer WHERE userID = ?");
$custQuery->bind_param("i", $userID);
$custQuery->execute();
$custResult = $custQuery->get_result();

if ($custRow = $custResult->fetch_assoc()) {
    $custID = $custRow['custID'];
} else {
    echo "No customer linked to this user.";
    exit;
}

// Main query to fetch order info
$query = "SELECT o.*, op.orderPackageQuantity, s.orderStatus, p.packageName, p.colorOption 
          FROM `order` o
          LEFT JOIN orderpackage op ON o.orderID = op.orderID
          LEFT JOIN package p ON op.packageID = p.packageID
          LEFT JOIN statushistory s ON o.orderID = s.orderID
          WHERE o.custID = ?
          ORDER BY o.orderID DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $custID);
$stmt->execute();
$result = $stmt->get_result();

// Check result for orders
if ($result->num_rows === 0) {
    echo "<tr><td colspan='10' class='text-center'>No orders found.</td></tr>";
}

// Fetch available packages
$packageOptions = [];
$packageQuery = "SELECT * FROM package WHERE availabilityStatus = 'Available'";
$packageResult = mysqli_query($conn, $packageQuery);

if (!$packageResult) {
    die("SQL Error in packageQuery: " . mysqli_error($conn));
}

while ($pkg = mysqli_fetch_assoc($packageResult)) {
    $packageOptions[] = $pkg;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="../link/style.css">
    <link rel="stylesheet" href="packageManagement.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
</head>
<body>
<?php include '../link/sidebarStudent.php'; ?>
<?php include '../link/headerStudent.php'; ?>

<div class="main_content">
    <div class="text">
        <span>My Orders</span>
    </div>

 <!-- Available Packages Section -->
 <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <strong>Available Printing Packages</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Package Name</th>
                        <th>Color Option</th>
                        <th>Price Per Page (RM)</th>
                        <th>Availability Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($packageOptions as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['packageName']); ?></td>
                            <td><?= htmlspecialchars($row['colorOption']); ?></td>
                            <td><?= number_format($row['price'], 2); ?></td>
                            <td><?= date("d M Y", strtotime($row['availabilityDate'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>


    <div class="card">
        <div class="d-flex align-items-center pb-3">
            <button type="button" class="btn btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#addOrderModal">
                <i class="material-symbols-outlined me-1">add</i>Add Order
            </button>
        </div>
        <div class="table-responsive">
            <table id="myTable" class="table table-hover pt-3 pb-3">
            <thead class="thead-color">
                <tr>
                    <th>Select</th>
                    <th>Order ID</th>
                    <th>Package</th>
                    <th>Quantity</th>
                    <th>Pickup Date</th>
                    <th>Pickup Time</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Edit</th>
                </tr>
            </thead>
                <tbody class="tbody-color">
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td>
                            <?php if ($row['orderStatus'] === null || $row['orderStatus'] === 'Pending') : ?>
                                <input type="checkbox" class="orderCheckbox" data-amount="<?= $row['totalAmount']; ?>" data-id="<?= $row['orderID']; ?>">
                            <?php else : ?>
                                <!-- Cannot pay for processed/completed orders -->
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $row['orderID']; ?></td>

                        <td><?= $row['packageName'] . " (" . $row['colorOption'] . ")"; ?></td>
                        <td><?= $row['orderPackageQuantity']; ?></td>
                        <td><?= $row['pickupDate']; ?></td>
                        <td><?= $row['pickupTime']; ?></td>
                        <td><?= basename($row['uploadFileName']); ?></td>
                        <td><span class="badge bg-<?= $row['orderStatus'] === 'Completed' ? 'success' : ($row['orderStatus'] === 'Processing' ? 'warning text-dark' : 'secondary') ?>"><?= $row['orderStatus'] ?? 'Pending'; ?></span></td>
                        <td>RM <?= number_format($row['totalAmount'], 2); ?></td>
                        <td>
                            <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#editOrderModal<?= $row['orderID']; ?>">Edit</button>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editOrderModal<?= $row['orderID']; ?>" tabindex="-1" aria-labelledby="editOrderModalLabel<?= $row['orderID']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <form class="modal-content" action="editOrder.php" method="POST" enctype="multipart/form-data">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Order #<?= $row['orderID']; ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="orderID" value="<?= $row['orderID']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Package</label>
                                        <select class="form-select" name="orderType" required>
                                            <?php foreach ($packageOptions as $pkg): ?>
                                                <option value="<?= $pkg['packageID']; ?>" <?= ($pkg['packageName'] === $row['packageName'] && $pkg['colorOption'] === $row['colorOption']) ? 'selected' : ''; ?>>
                                                    <?= $pkg['packageName']; ?> (<?= $pkg['colorOption']; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" class="form-control" name="orderPackageQuantity" value="<?= $row['orderQuantity']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Pickup Date</label>
                                        <input type="date" class="form-control" name="pickupDate" value="<?= $row['pickupDate']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Pickup Time</label>
                                        <input type="time" class="form-control" name="pickupTime" value="<?= $row['pickupTime']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Upload File (Max 5MB)</label>
                                        <input type="file" class="form-control" name="uploadFileName" accept=".pdf,.doc,.docx,.jpg,.png">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="updateOrder" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
                </tbody>

                <tfoot>
            <tr>
        <td colspan="8" class="text-end fw-bold">Total Selected Amount:</td>
        <td colspan="2"><span id="totalAmountDisplay">RM 0.00</span></td>
    </tr>
    <tr>
        <td colspan="10" class="text-end">
            <button id="payButton" class="btn btn-success" disabled data-bs-toggle="modal" data-bs-target="#paymentModal">
                Pay Selected
            </button>
        </td>
    </tr>
</tfoot>

            </table>
        </div>
    </div>
</div>

<!-- Add Order Modal -->
<div class="modal fade" id="addOrderModal" tabindex="-1" aria-labelledby="addOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="addOrder.php" method="POST" enctype="multipart/form-data" id="addOrderForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="custID" value="<?= $custID; ?>" />
                        <div class="mb-3">
                            <label for="packageID" class="form-label">Package</label>
                            <select class="form-select" id="packageID" name="packageID" required>
                                <option value="" disabled selected>-- Select Package --</option>
                                <?php foreach ($packageOptions as $pkg): ?>
                                    <option value="<?= $pkg['packageID']; ?>" data-price="<?= $pkg['price']; ?>">
                                        <?= htmlspecialchars($pkg['packageName']); ?> (<?= htmlspecialchars($pkg['colorOption']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" min="1" value="1" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pickup Date</label>
                            <input type="date" name="pickupDate" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pickup Time</label>
                            <input type="time" name="pickupTime" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload File (max 5MB)</label>
                            <input type="file" name="uploadFile" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png" required />
                        </div>
                        <input type="hidden" name="price" id="priceInput" value="" />
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="addOrder">Submit Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="processPayment.php" id="paymentForm">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="orderIDs" id="orderIDsInput">

        <!-- Order Summary Table -->
        <div class="table-responsive mb-3">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Package</th>
                <th>Amount</th>
              </tr>
            </thead>
            <tbody id="orderSummaryBody">
              <!-- Will be inserted dynamically -->
            </tbody>
          </table>
        </div>

        <!-- Payment Method -->
        <div class="mb-3">
          <label class="form-label">Payment Method</label>
          <select class="form-select" name="paymentMethod" required>
            <option value="" disabled selected>-- Select Method --</option>
            <option value="card">Card</option>
            <option value="online_banking">Online Banking</option>
            <option value="ewallet">E-Wallet</option>
          </select>
        </div>

        <!-- Membership Points -->
        <div class="mb-3">
          <label class="form-label">Apply Membership Points</label>
          <input type="number" class="form-control" id="pointsToUse" name="pointsToUse" min="0" step="1" value="0">
          <div class="form-text">Your current points: <strong id="currentPoints">--</strong> (1 point = RM0.01)</div>
        </div>

        <!-- Final Total -->
        <div class="alert alert-info">
          Final Amount to Pay: <strong id="finalAmountText">RM 0.00</strong>
          <input type="hidden" name="finalAmount" id="finalAmountInput">
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Confirm & Pay</button>
      </div>
    </form>
  </div>
</div>

<!-- Payment Receipt Modal -->
<div class="modal fade" id="paymentReceiptModal" tabindex="-1" aria-labelledby="paymentReceiptLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentReceiptLabel">Payment Receipt</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="receiptContent">
        <p><strong>Paid Orders:</strong> <span id="receiptOrders"></span></p>
        <p><strong>Total Amount:</strong> RM <span id="receiptTotal"></span></p>
        <p><strong>Payment Method:</strong> <span id="receiptMethod"></span></p>
        <p><strong>Points Used:</strong> <span id="receiptPointsUsed"></span> (RM <span id="receiptPointsDiscount"></span>)</p>
        <p><strong>Points Earned:</strong> <span id="receiptPointsEarned"></span></p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" onclick="window.print()">Print</button>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#myTable').DataTable({
            pageLength: 6,
            lengthMenu: [6, 12, 18]
        });
    });
</script>

<script>
    // When package changes, update hidden price input for backend use
    const packageSelect = document.getElementById('packageID');
    const priceInput = document.getElementById('priceInput');

    packageSelect.addEventListener('change', function () {
        const selectedOption = packageSelect.options[packageSelect.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        priceInput.value = price || '';
    });

    // Trigger initial price update on modal show/reset
    const addOrderModal = document.getElementById('addOrderModal');
    addOrderModal.addEventListener('show.bs.modal', () => {
        packageSelect.value = '';
        priceInput.value = '';
        // Reset other inputs if desired
    });
</script>

<script>
document.getElementById("addOrderButton").addEventListener("click", function () {
    const container = document.getElementById("orderFieldsContainer");
    const firstSet = container.querySelector(".order-field-set");
    const newSet = firstSet.cloneNode(true);

    newSet.querySelectorAll("input, select").forEach(el => {
        if (el.tagName === "INPUT") el.value = "";
        if (el.tagName === "SELECT") el.selectedIndex = 0;
    });

    container.appendChild(newSet);
});
</script>

<script>
    const checkboxes = document.querySelectorAll(".orderCheckbox");
    const totalDisplay = document.getElementById("totalAmountDisplay");
    const payButton = document.getElementById("payButton");

    function updateTotal() {
        let total = 0;
        let selectedOrders = 0;

        checkboxes.forEach(cb => {
            if (cb.checked) {
                total += parseFloat(cb.dataset.amount);
                selectedOrders++;
            }
        });

        totalDisplay.textContent = "RM " + total.toFixed(2);
        payButton.disabled = selectedOrders === 0;
    }

    checkboxes.forEach(cb => {
        cb.addEventListener("change", updateTotal);
    });

    // Optional: Collect selected orderIDs when clicking pay
    payButton.addEventListener("click", function () {
        const selectedIDs = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.dataset.id);

        console.log("Selected order IDs to pay:", selectedIDs); // Replace this with actual usage in Step 3
    });
</script>

<script>
    const currentPoints = 120; // TODO: Replace this with PHP dynamic value
    document.getElementById("currentPoints").textContent = currentPoints;

    payButton.addEventListener("click", function () {
        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked);

        let total = 0;
        const summaryBody = document.getElementById("orderSummaryBody");
        summaryBody.innerHTML = "";

        const orderIDs = [];

        selected.forEach(cb => {
            const row = cb.closest("tr");
            const orderID = cb.dataset.id;
            const amount = parseFloat(cb.dataset.amount);
            const packageText = row.children[2].textContent;

            summaryBody.innerHTML += `
                <tr>
                    <td>${orderID}</td>
                    <td>${packageText}</td>
                    <td>RM ${amount.toFixed(2)}</td>
                </tr>
            `;

            orderIDs.push(orderID);
            total += amount;
        });

        document.getElementById("orderIDsInput").value = orderIDs.join(",");
        updateFinalAmount(total);

        document.getElementById("pointsToUse").addEventListener("input", function () {
            updateFinalAmount(total);
        });
    });

    function updateFinalAmount(originalTotal) {
        const pointsUsed = parseInt(document.getElementById("pointsToUse").value || 0);
        const maxUsable = Math.floor(originalTotal / 0.01); // Max RM-to-points

        if (pointsUsed > currentPoints) {
            document.getElementById("pointsToUse").value = currentPoints;
            return updateFinalAmount(originalTotal);
        }
        if (pointsUsed > maxUsable) {
            document.getElementById("pointsToUse").value = maxUsable;
            return updateFinalAmount(originalTotal);
        }

        const discount = pointsUsed * 0.01;
        const finalAmount = Math.max(0, originalTotal - discount);

        document.getElementById("finalAmountText").textContent = "RM " + finalAmount.toFixed(2);
        document.getElementById("finalAmountInput").value = finalAmount.toFixed(2);
    }
</script>

<script>
function showReceiptModal(orders, total, method, pointsUsed, discount, pointsEarned) {
    document.getElementById('receiptOrders').textContent = orders.join(', ');
    document.getElementById('receiptTotal').textContent = total.toFixed(2);
    document.getElementById('receiptMethod').textContent = method;
    document.getElementById('receiptPointsUsed').textContent = pointsUsed;
    document.getElementById('receiptPointsDiscount').textContent = discount.toFixed(2);
    document.getElementById('receiptPointsEarned').textContent = pointsEarned;
    var receiptModal = new bootstrap.Modal(document.getElementById('paymentReceiptModal'));
    receiptModal.show();
}
</script>

<script>
    document.getElementById("currentPoints").textContent = "<?= $currentPoints ?>";
</script>


<?php include '../link/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#myTable').DataTable({
            pageLength: 6,
            lengthMenu: [6, 12, 18]
        });
    });
</script>

</body>
</html>
