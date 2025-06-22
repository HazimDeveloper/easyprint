<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'Staff') {
    header('Location: login.php');
    exit;
}

@include '../link/db_connect.php';

$query = "SELECT packageID, packageName, colorOption, price, availabilityStatus, availabilityDate FROM package";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching package data: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../link/style.css">
    <link rel="stylesheet" type="text/css" href="packageManagement.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
    <style>
        .status-available {
            color: green;
            font-weight: bold;
        }
        .status-unavailable {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include '../link/sidebarStaff.php'; ?>
    <?php include '../link/headerStaff.php'; ?>

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

        <div class="text">
            <span>Package Management</span>
        </div>

        <div class="card">
            <div class="d-flex align-items-center pb-3">
                <button type="button" class="btn btn-primary d-flex align-items-center ms-auto" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="material-symbols-outlined me-1">add</i>Add Package
                </button>
            </div>

            <div class="table-responsive">
                <table id="myTable" class="table table-hover pt-3 pb-3">
                    <thead class="thead-color">
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Color Options</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Available Date</th>
                            <th>Delete</th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                    <tbody class="tbody-color">
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $row['packageID']; ?></td>
                                <td><?= $row['packageName']; ?></td>
                                <td><?= $row['colorOption']; ?></td>
                                <td><?= $row['price']; ?></td>
                                <td>
                                    <span class="<?= $row['availabilityStatus'] === 'Available' ? 'status-available' : 'status-unavailable'; ?>">
                                        <?= $row['availabilityStatus']; ?>
                                    </span>
                                </td>
                                <td><?= $row['availabilityDate']; ?></td>
                                <td>
                                    <a href="Delete_PackageManagement.php?packageID=<?= $row['packageID']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this package?');">
                                        <i class="material-symbols-outlined">delete</i>
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-secondary editbtn" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['packageID']; ?>">
                                        <i class="material-symbols-outlined">edit</i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?= $row['packageID']; ?>" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Update Package</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="Edit_PackageManagement.php" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="packageID" value="<?= $row['packageID']; ?>">
                                                <div class="mb-3">
                                                    <label for="packageName" class="form-label">Name</label>
                                                    <input type="text" class="form-control" name="packageName" value="<?= $row['packageName']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="colorOption" class="form-label">Color Options</label>
                                                    <select class="form-select" name="colorOption" required>
                                                        <option value="<?= $row['colorOption']; ?>" selected><?= $row['colorOption']; ?></option>
                                                        <option value="<?= $row['colorOption'] === 'Black and White' ? 'Color' : 'Black and White'; ?>">
                                                            <?= $row['colorOption'] === 'Black and White' ? 'Color' : 'Black and White'; ?>
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="price" class="form-label">Price</label>
                                                    <input type="text" class="form-control" name="price" value="<?= $row['price']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Status</label>
                                                    <select class="form-select" name="status" required>
                                                        <option value="<?= $row['availabilityStatus']; ?>" selected><?= $row['availabilityStatus']; ?></option>
                                                        <option value="<?= $row['availabilityStatus'] === 'Available' ? 'Unavailable' : 'Available'; ?>">
                                                            <?= $row['availabilityStatus'] === 'Available' ? 'Unavailable' : 'Available'; ?>
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="availabilityDate" class="form-label">Availability Date</label>
                                                    <input type="date" class="form-control" name="availabilityDate" value="<?= $row['availabilityDate']; ?>">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                                                <button type="submit" class="btn btn-primary" name="updatePackage">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Add Modal -->
            <div class="modal fade" id="addModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Package</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="Add_PackageManagement.php" method="POST">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="packageName" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Color Options</label>
                                    <select class="form-select" name="colorOption" required>
                                        <option value="Black and White">Black and White</option>
                                        <option value="Color">Color</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Price</label>
                                    <input type="text" class="form-control" name="price" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="availabilityStatus" required>
                                        <option value="Available" selected>Available</option>
                                        <option value="Unavailable">Unavailable</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Availability Date</label>
                                    <input type="date" class="form-control" name="availabilityDate">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                                <button type="submit" class="btn btn-primary" name="addPackage">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../link/footer.php'; ?>

    <script>
        let btn = document.querySelector("#btn");
        let sideBar = document.querySelector(".sideBar");
        btn.onclick = function () {
            sideBar.classList.toggle("close");
        }

        $(document).ready(function () {
            $('#myTable').DataTable({"pageLength": 6,"lengthMenu": [6, 12, 18]});
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
</body>

</html>
