<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome to EasyPrint</title>
</head>
<body>
    <h2>Hello, <?php echo $_SESSION['username']; ?>!</h2>
    <p>Welcome to EasyPrint Dashboard</p>
    <a href="logout.php">Logout</a>
</body>
</html>
