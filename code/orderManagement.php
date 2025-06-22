<?php
session_start();
@include '../link/db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'Staff') {
    header('Location: login.php');
    exit;
}

// Fetch all orders with customer & package details
$sql = "SELECT o.orderID, c.name AS customerName, o.orderPackage, o.orderQuantity, 
               o.pickupDate, o.pickupTime, o.totalAmount, s.orderStatus
        FROM `order` o
        JOIN customer c ON o.custID = c.custID
        LEFT JOIN statushistory s ON o.orderID = s.orderID
        ORDER BY o.orderID DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Management</title>
  <link rel="stylesheet" href="../link/style.css">
  <link rel="stylesheet" href="orderManagement.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>

<body>
<?php include '../link/sidebarStaff.php'; ?>
<?php include '../link/headerStaff.php'; ?>

<div class="main_content">
  <div class="text">
    <span>Order Management</span>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table id="orderTable" class="table table-striped table-hover">
        <thead class="thead-color">
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Package</th>
            <th>Quantity</th>
            <th>Pickup Date</th>
            <th>Pickup Time</th>
            <th>Status</th>
            <th>Total (RM)</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $row['orderID'] ?></td>
            <td><?= $row['customerName'] ?></td>
            <td><?= $row['orderPackage'] ?></td>
            <td><?= $row['orderQuantity'] ?></td>
            <td><?= $row['pickupDate'] ?></td>
            <td><?= $row['pickupTime'] ?></td>
            <td><span class="badge bg-info"><?= $row['orderStatus'] ?? 'Pending' ?></span></td>
            <td><?= number_format($row['totalAmount'], 2) ?></td>
            <td>
              <!-- Edit Button -->
              <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['orderID'] ?>">
                <i class="material-symbols-outlined">edit</i>
              </button>
              <!-- Delete -->
              <a href="deleteOrder.php?orderID=<?= $row['orderID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this order?');">
                <i class="material-symbols-outlined">delete</i>
              </a>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editModal<?= $row['orderID'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <form class="modal-content" action="editOrder.php" method="POST">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Order #<?= $row['orderID'] ?></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="orderID" value="<?= $row['orderID'] ?>">
                  <div class="mb-3">
                    <label>Pickup Date</label>
                    <input type="date" name="pickupDate" class="form-control" value="<?= $row['pickupDate'] ?>" required>
                  </div>
                  <div class="mb-3">
                    <label>Pickup Time</label>
                    <input type="time" name="pickupTime" class="form-control" value="<?= $row['pickupTime'] ?>" required>
                  </div>
                  <div class="mb-3">
                    <label>Status</label>
                    <select class="form-select" name="orderStatus">
                      <option <?= $row['orderStatus'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                      <option <?= $row['orderStatus'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                      <option <?= $row['orderStatus'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" name="updateOrder" class="btn btn-primary">Save</button>
                </div>
              </form>
            </div>
          </div>
          <?php endwhile; ?>
        </tbody>
      </table>
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
    $('#orderTable').DataTable({
      pageLength: 6,
      lengthMenu: [6, 12, 18]
    });
  });
</script>
</body>
</html>
