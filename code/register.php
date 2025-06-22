<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - EasyPrint</title>
    <link rel="stylesheet" href="login.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <header class="header">
        <div class="header-container">
            <span class="header-logo material-icons">how_to_reg</span>
            <div class="header-title">EasyPrint Registration</div>
        </div>
    </header>

    <?php
if (!empty($error)) {
    foreach ($error as $e) {
        echo "<div class='error-message'>$e</div>";
    }
}

if (!empty($success)) {
    foreach ($success as $s) {
        echo "<div class='success-message' style='color: #065f46; background: #d1fae5; padding: 10px; border-radius: 8px;'>$s</div>";
    }
}
?>


    <main class="login-container">
        <section class="login-welcome">
            <img src="../img/logo.png" alt="EasyPrint Logo" class="welcome-image">
            <h2>Join EasyPrint Now!</h2>
            <p>Register now to manage your printing, payments, and orders online with ease.</p>
        </section>

        <section class="login-form-section">
            <form action="registerAction.php" method="POST" class="login-form">
                <div class="form-header">
                    <span class="material-icons">person_add</span>
                    Create Account
                </div>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="text" name="contactNum" placeholder="Phone Number" required>

                <select name="role" required>
                    <option value="" selected disabled>- Select Role -</option>
                    <option value="Student">Student</option>
                    <option value="Staff">Staff</option>
                </select>

                <button type="submit" name="register" class="btn-login">Register</button>

                <div class="register-text">
                    Already have an account? <a href="login.php">Login here</a>
                </div>
            </form>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-container">
            &copy; <?= date("Y") ?> EasyPrint. All rights reserved.
        </div>
    </footer>
</body>
</html>
