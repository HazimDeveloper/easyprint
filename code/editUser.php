<?php
session_start();
@include '../link/db_connect.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// UPDATE user data when form is submitted
if (isset($_POST['updateUser'])) {
    $userID     = mysqli_real_escape_string($conn, $_POST['userID']);
    $username   = mysqli_real_escape_string($conn, $_POST['username']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $contactNum = mysqli_real_escape_string($conn, $_POST['contactNum']);
    $role       = mysqli_real_escape_string($conn, $_POST['role']);

    $updateUser = "UPDATE user 
                   SET username = '$username', email = '$email', contactNum = '$contactNum', role = '$role' 
                   WHERE userID = '$userID'";

    $result = mysqli_query($conn, $updateUser);

    if ($result) {
        $_SESSION['status'] = "User updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating user: " . mysqli_error($conn);
    }

    header('Location: userManagement.php');
    exit;
}

// FETCH user data to populate form
if (isset($_GET['userID'])) {
    $userID = mysqli_real_escape_string($conn, $_GET['userID']);

    $query = "SELECT * FROM user WHERE userID = '$userID'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['error'] = "User not found.";
        header('Location: userManagement.php');
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid access.";
    header('Location: userManagement.php');
    exit;
}
?>

