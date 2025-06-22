<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'Student') {
    // Redirect to login page if the user is not logged in or not a student
    header('Location: ../code/login.php');
    exit;
}

@include '../link/db_connect.php'; // Include the database connection

$userID = $_SESSION['userID'];
$query = "SELECT * FROM user WHERE userID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $userID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Check if user exists
if (!$user) {
    echo "User not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contactNum = $_POST['contactNum'];

    // Update user information
    $updateQuery = "UPDATE user SET username = ?, email = ?, contactNum = ? WHERE userID = ?";
    $updateStmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, 'sssi', $username, $email, $contactNum, $userID);

    if (mysqli_stmt_execute($updateStmt)) {
        $_SESSION['success_message'] = "Profile updated successfully!";
        header('Location: userProfile.php');
        exit;
    } else {
        $error_message = "Failed to update profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" type="text/css" href="../link/style.css">
    <link rel="stylesheet" type="text/css" href="dashboardStaff.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
</head>
<body>
    <?php include '../link/sidebarStudent.php'; ?>
    <?php include '../link/headerStudent.php'; ?>

    <div class="main_content">
        <h3>User Profile</h3>
        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php } ?>
        <?php if (isset($_SESSION['success_message'])) { ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php } ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="contactNum" class="form-label">Contact Number</label>
                <input type="text" class="form-control" id="contactNum" name="contactNum" value="<?php echo htmlspecialchars($user['contactNum'] ?? ''); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
</body>
</html>