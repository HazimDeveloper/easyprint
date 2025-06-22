<?php
session_start();
@include '../link/db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'Staff') {
    header('Location: login.php');
    exit;
}

// Fixed query - properly joining tables to get package info
$sql = "SELECT o.orderID, c.name AS customerName, p.packageName, p.colorOption,
               op.orderPackageQuantity, o.pickupDate, o.pickupTime, 
               o.totalAmount, sh.orderStatus
        FROM `order` o
        JOIN customer c ON o.custID = c.custID
        LEFT JOIN orderpackage op ON o.orderID = op.orderID
        LEFT JOIN package p ON op.packageID = p.packageID
        LEFT JOIN statushistory sh ON o.orderID = sh.orderID
        ORDER BY o.orderID DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Management</title>
  <link rel="stylesheet" href="../link/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
  
  <style>
    /* SIMPLE & FUNCTIONAL MODAL STYLING */
    
    /* Fix modal input functionality - CRITICAL */
    .modal-body input[type="date"],
    .modal-body input[type="time"],
    .modal-body select {
      background-color: #ffffff !important;
      border: 1px solid #ced4da !important;
      color: #495057 !important;
      cursor: text !important;
      pointer-events: auto !important;
      user-select: text !important;
    }

    .modal-body input[type="date"]:focus,
    .modal-body input[type="time"]:focus,
    .modal-body select:focus {
      background-color: #ffffff !important;
      border-color: #047857 !important;
      outline: 0 !important;
      box-shadow: 0 0 0 0.2rem rgba(4, 120, 87, 0.25) !important;
    }

    /* Simple Modal Enhancements */
    .modal-content {
      border-radius: 12px !important;
      border: none !important;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
    }

    .modal-header {
      background: linear-gradient(135deg, #047857, #065f46) !important;
      color: white !important;
      border-bottom: none !important;
      padding: 1.25rem 1.5rem !important;
    }

    .modal-title {
      font-weight: 600 !important;
      display: flex !important;
      align-items: center !important;
      gap: 0.5rem !important;
    }

    .modal-body {
      padding: 1.5rem !important;
    }

    .modal-footer {
      padding: 1rem 1.5rem !important;
      border-top: 1px solid #e5e7eb !important;
      background-color: #f8fafc !important;
    }

    /* Form Enhancements */
    .modal-body .form-label {
      font-weight: 500 !important;
      color: #374151 !important;
      margin-bottom: 0.5rem !important;
    }

    .modal-body .form-control,
    .modal-body .form-select {
      border-radius: 8px !important;
      border: 1px solid #d1d5db !important;
      padding: 0.625rem 0.75rem !important;
    }

    .modal-body .form-control:focus,
    .modal-body .form-select:focus {
      border-color: #047857 !important;
      box-shadow: 0 0 0 3px rgba(4, 120, 87, 0.1) !important;
    }

    /* Button Enhancements */
    .modal-footer .btn {
      border-radius: 8px !important;
      padding: 0.5rem 1rem !important;
      font-weight: 500 !important;
      cursor: pointer !important;
      pointer-events: auto !important;
    }

    .modal-footer .btn-primary {
      background-color: #047857 !important;
      border-color: #047857 !important;
    }

    .modal-footer .btn-primary:hover {
      background-color: #065f46 !important;
      border-color: #065f46 !important;
    }

    .modal-footer .btn-secondary {
      background-color: #6b7280 !important;
      border-color: #6b7280 !important;
    }

    .modal-footer .btn-secondary:hover {
      background-color: #4b5563 !important;
      border-color: #4b5563 !important;
    }

    /* Ensure modal works properly */
    .modal {
      z-index: 1055 !important;
    }

    .modal-backdrop {
      z-index: 1050 !important;
    }

    /* Fix any button issues */
    .modal button {
      cursor: pointer !important;
      pointer-events: auto !important;
    }

    .modal form {
      pointer-events: auto !important;
    }

    /* Table styling */
    .table td {
      vertical-align: middle;
    }

    .btn-sm .material-symbols-outlined {
      font-size: 16px;
    }

    /* Loading state */
    .btn-loading {
      opacity: 0.7;
      cursor: not-allowed;
      position: relative;
    }

    .btn-loading::after {
      content: '';
      position: absolute;
      width: 1rem;
      height: 1rem;
      border: 2px solid transparent;
      border-top: 2px solid currentColor;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
    }

    @keyframes spin {
      0% { transform: translate(-50%, -50%) rotate(0deg); }
      100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
  </style>
</head>
</head>

<body>
<?php include '../link/sidebarStaff.php'; ?>
<?php include '../link/headerStaff.php'; ?>

<div class="main_content">
  <!-- Success/Error Messages -->
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
        <tbody class="tbody-color">
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $row['orderID'] ?></td>
            <td><?= htmlspecialchars($row['customerName']) ?></td>
            <td><?= htmlspecialchars(($row['packageName'] ?? 'N/A') . ' (' . ($row['colorOption'] ?? 'N/A') . ')') ?></td>
            <td><?= $row['orderPackageQuantity'] ?? 0 ?></td>
            <td><?= $row['pickupDate'] ?></td>
            <td><?= $row['pickupTime'] ?></td>
            <td><span class="badge bg-info"><?= $row['orderStatus'] ?? 'Pending' ?></span></td>
            <td><?= number_format($row['totalAmount'], 2) ?></td>
            <td>
              <!-- Edit Button -->
              <button class="btn btn-sm btn-secondary edit-btn" 
                      data-bs-toggle="modal" 
                      data-bs-target="#editModal<?= $row['orderID'] ?>"
                      data-order-id="<?= $row['orderID'] ?>"
                      title="Edit Order">
                <i class="material-symbols-outlined">edit</i>
              </button>
              <!-- Delete -->
              <a href="deleteOrder.php?orderID=<?= $row['orderID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this order?');">
                <i class="material-symbols-outlined">delete</i>
              </a>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editModal<?= $row['orderID'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $row['orderID'] ?>" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editModalLabel<?= $row['orderID'] ?>">
                    <span class="material-symbols-outlined">edit_note</span>
                    Edit Order #<?= $row['orderID'] ?>
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="editOrder.php" method="POST">
                  <div class="modal-body">
                    <input type="hidden" name="orderID" value="<?= $row['orderID'] ?>">
                    
                    <div class="mb-3">
                      <label class="form-label">📅 Pickup Date</label>
                      <input type="date" name="pickupDate" class="form-control" value="<?= $row['pickupDate'] ?>" required>
                    </div>
                    
                    <div class="mb-3">
                      <label class="form-label">⏰ Pickup Time</label>
                      <input type="time" name="pickupTime" class="form-control" value="<?= $row['pickupTime'] ?>" required>
                    </div>
                    
                    <div class="mb-3">
                      <label class="form-label">🚩 Order Status</label>
                      <select class="form-select" name="orderStatus" required>
                        <option value="Pending" <?= ($row['orderStatus'] ?? 'Pending') == 'Pending' ? 'selected' : '' ?>>🟡 Pending</option>
                        <option value="Processing" <?= ($row['orderStatus'] ?? 'Pending') == 'Processing' ? 'selected' : '' ?>>🔄 Processing</option>
                        <option value="Completed" <?= ($row['orderStatus'] ?? 'Pending') == 'Completed' ? 'selected' : '' ?>>✅ Completed</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Cancel</button>
                    <button type="submit" name="updateOrder" class="btn btn-primary">💾 Save Changes</button>
                  </div>
                </form>
              </div>
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
    // Initialize DataTable with standard settings
    $('#orderTable').DataTable({
      pageLength: 6,
      lengthMenu: [6, 12, 18],
      responsive: true
    });

    // Handle modal show event - FOCUS ON INPUT FUNCTIONALITY
    $('.modal').on('show.bs.modal', function (e) {
      console.log('Modal showing:', this.id);
      
      // Ensure all inputs are enabled and focusable
      $(this).find('input, select, textarea').each(function() {
        $(this).prop('disabled', false);
        $(this).attr('readonly', false);
        $(this).css({
          'pointer-events': 'auto',
          'background-color': '#fff',
          'cursor': 'text'
        });
      });
    });

    // Handle modal shown event (after animation)
    $('.modal').on('shown.bs.modal', function (e) {
      console.log('Modal shown:', this.id);
      
      // Focus on first input field
      $(this).find('input[type="date"]').first().focus();
      
      // Re-enable all form controls
      $(this).find('.form-control, .form-select').each(function() {
        this.style.setProperty('background-color', '#ffffff', 'important');
        this.style.setProperty('cursor', 'text', 'important');
        this.style.setProperty('pointer-events', 'auto', 'important');
        $(this).removeAttr('readonly disabled');
      });
    });

    // Handle form submission with loading state
    $('form[action="editOrder.php"]').on('submit', function(e) {
      console.log('Form submitting');
      const form = this;
      const submitBtn = $(form).find('button[type="submit"]');
      
      // Add loading state
      submitBtn.addClass('btn-loading').prop('disabled', true).text('Saving...');
      
      // Allow normal form submission
      return true;
    });

    // Reset form and button state when modal is hidden
    $('.modal').on('hidden.bs.modal', function () {
      console.log('Modal hidden:', this.id);
      const form = $(this).find('form')[0];
      if (form) {
        form.reset();
      }
      $(this).find('button[type="submit"]').removeClass('btn-loading').prop('disabled', false).text('Save Changes');
    });

    // Force input focus when clicked
    $(document).on('click', '.modal input, .modal select', function() {
      console.log('Input clicked:', this.name);
      $(this).focus();
    });

    // Debug: Check if inputs are working
    $(document).on('input change', '.modal input, .modal select', function() {
      console.log('Input changed:', this.name, this.value);
    });

    // Function to manually enable all inputs
    function enableAllInputs() {
      $('.modal input, .modal select').each(function() {
        this.disabled = false;
        this.readOnly = false;
        this.style.setProperty('pointer-events', 'auto', 'important');
        this.style.setProperty('background-color', '#ffffff', 'important');
        this.style.setProperty('cursor', 'text', 'important');
      });
      console.log('All inputs force-enabled');
    }

    // Auto-enable inputs on various events
    setTimeout(enableAllInputs, 500);
    $(window).on('load', enableAllInputs);

    // Test function - call this in browser console: testInputs()
    window.testInputs = function() {
      console.log('Testing modal inputs...');
      $('.modal').each(function() {
        const modalId = this.id;
        console.log('Modal:', modalId);
        
        $(this).find('input, select').each(function() {
          console.log('Input:', this.name, 'Disabled:', this.disabled, 'ReadOnly:', this.readOnly);
          console.log('Styles:', window.getComputedStyle(this).pointerEvents, window.getComputedStyle(this).backgroundColor);
        });
      });
    };

    console.log('Order Management initialized with minimal custom styling! 🎯');
  });
</script>
</body>
</html>