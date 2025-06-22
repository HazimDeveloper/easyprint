<?php
session_start();
include '../link/db_connect.php';

if (isset($_POST['signIn'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']); // MD5 hashed to match registration
    $role     = mysqli_real_escape_string($conn, $_POST['role']);

    $query = "SELECT * FROM user WHERE username = '$username' AND password = '$password' AND role = '$role'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        $_SESSION['userID'] = $user['userID'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;

        // ✅ If student, check/create customer record
        if ($user['role'] === 'Student') {
            $userID = $user['userID'];
            $studentID = $user['studentID'] ?? ''; // Make sure this exists in user table
            $name = $user['username']; // Or use $user['name'] if you have it

            // Check if a customer record already exists
            $checkQuery = "SELECT * FROM customer WHERE userID = '$userID'";
            $checkResult = mysqli_query($conn, $checkQuery);

            if (mysqli_num_rows($checkResult) === 0) {
                // Insert new customer record
                $insertQuery = "INSERT INTO customer (userID, studentID, name, verificationStatus) 
                                VALUES ('$userID', '$studentID', '$name', 'Pending')";
                mysqli_query($conn, $insertQuery);
            }

            header("Location: dashboardStudent.php");
        } else {
            header("Location: dashboardStaff.php");
        }
        exit;
    } else {
        $_SESSION['error'] = "Invalid username, password or role!";
        header("Location: login.php");
        exit;
    }
}
?>
