<?php
session_start();
@include '../link/db_connect.php'; // DB connection

$success = [];
$error = []; // Initialize error array (you missed declaring this explicitly)

if (isset($_POST['register'])) {
    $username   = mysqli_real_escape_string($conn, $_POST['username']);
    $pass       = md5($_POST['password']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $contactNum = mysqli_real_escape_string($conn, $_POST['contactNum']);
    $role       = mysqli_real_escape_string($conn, $_POST['role']);

    // 1. Check if username already exists
    $query = "SELECT * FROM user WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $error[] = 'User already exists!';
    } else {
        // 2. Insert into `user` table
        $insertUser = "INSERT INTO user (username, password, email, contactNum, role) 
                       VALUES ('$username', '$pass', '$email', '$contactNum', '$role')";

        if (mysqli_query($conn, $insertUser)) {
            $newUserID = mysqli_insert_id($conn);

            // 3. If Student, also insert into `customer` table
            if ($role === 'Student') {
                $studentID = 'STU' . rand(1000, 9999); // Auto-generate
                $insertCust = "INSERT INTO customer (userID, studentID, name, verificationStatus)
                               VALUES ('$newUserID', '$studentID', '$username', 'Pending')";
                mysqli_query($conn, $insertCust);
            }

            $success[] = 'User successfully registered!';
            header('refresh:1;url=userManagement.php');
        } else {
            $error[] = 'Failed to register!';
        }
    }
}
?>
