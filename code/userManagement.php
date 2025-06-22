<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'Staff') {
    header('Location: login.php');
    exit;
}

@include '../link/db_connect.php';

$query = "SELECT * FROM user";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching user data: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - User Management</title>
    <link rel="stylesheet" type="text/css" href="../link/style.css">
    <link rel="stylesheet" type="text/css" href="userManagement.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
</head>

<body>
    <?php include '../link/sidebarStaff.php'; ?>
    <?php include '../link/headerStaff.php'; ?>

    <div class="main_content">
        <?php if (isset($_SESSION['status'])): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <span class="material-symbols-outlined me-2">check_circle</span>
                <?php echo $_SESSION['status']; unset($_SESSION['status']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                <span class="material-symbols-outlined me-2">error</span>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="text">
            <span>User Management</span>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table id="myTable" class="table table-hover pt-3 pb-3">
                    <thead class="thead-color">
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Username</th>
                            <th scope="col">Email</th>
                            <th scope="col">Contact No</th>
                            <th scope="col">Role</th>
                            <th scope="col">Delete</th>
                            <th scope="col">Edit</th>
                        </tr>
                    </thead>
                    <tbody class="tbody-color">
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?= $row['userID']; ?></td>
                                <td><?= $row['username']; ?></td>
                                <td><?= $row['email']; ?></td>
                                <td><?= $row['contactNum']; ?></td>
                                <td><?= $row['role']; ?></td>
                                <td>
                                    <a href="deleteUser.php?userID=<?= $row['userID']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">
                                        <i class="material-symbols-outlined">delete</i>
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['userID']; ?>">
                                        <i class="material-symbols-outlined">edit</i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?= $row['userID']; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editUserModalLabel<?= $row['userID']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="editUser.php" method="POST">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="editUserModalLabel<?= $row['userID']; ?>">Edit User</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="userID" value="<?= $row['userID']; ?>">

                                                <div class="mb-3">
                                                    <label for="username<?= $row['userID']; ?>" class="form-label">Username</label>
                                                    <input type="text" class="form-control" id="username<?= $row['userID']; ?>" name="username" value="<?= $row['username']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="email<?= $row['userID']; ?>" class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="email<?= $row['userID']; ?>" name="email" value="<?= $row['email']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="contactNum<?= $row['userID']; ?>" class="form-label">Contact Number</label>
                                                    <input type="text" class="form-control" id="contactNum<?= $row['userID']; ?>" name="contactNum" value="<?= $row['contactNum']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="role<?= $row['userID']; ?>" class="form-label">Role</label>
                                                    <select class="form-select" id="role<?= $row['userID']; ?>" name="role" required>
                                                        <option value="Student" <?= $row['role'] == 'Student' ? 'selected' : ''; ?>>Student</option>
                                                        <option value="Staff" <?= $row['role'] == 'Staff' ? 'selected' : ''; ?>>Staff</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                                                <button type="submit" class="btn btn-primary" name="updateUser">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- End Edit Modal -->
                        <?php } ?>
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
            $('#myTable').DataTable({
                "pageLength": 6,
                "lengthMenu": [6, 12, 18]
            });
        });
    </script>
</body>
</html>
