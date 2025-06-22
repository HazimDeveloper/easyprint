<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - EasyPrint</title>
    <link rel="stylesheet" href="login.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <!-- Optional: header -->
    <header class="header">
        <div class="header-container">
            <span class="header-logo material-icons">print</span>
            <div class="header-title">EasyPrint</div>
        </div>
    </header>

    <main class="login-container">
        <!-- Welcome Left Panel -->
        <section class="login-welcome">
            <img src="..\img\printAnime.png" alt="EasyPrint Logo" class="welcome-image">
            <h2>Welcome!</h2>
            <p>Upload your documents, customize your print orders, and schedule pick-up times easily with our secure platform. Designed specifically for students and PETAKOM members.</p>
        </section>

        <!-- Login Form -->
        <section class="login-form-section">
            <form action="loginAction.php" method="POST" class="login-form">
                <div class="form-header">
                    <span class="material-icons">login</span>
                    Login
                </div>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>

                <select name="role" required>
                    <option value="" selected disabled>- Select Role -</option>
                    <option value="Student">Student</option>
                    <option value="Staff">Staff</option>
                </select>

                <button type="submit" name="signIn" class="btn-login">Login</button>

                <div class="register-text">
                Not a member? <a href="register.php?">Register Now!</a>
                </div>
            </form>
        </section>
    </main>

    <!-- Optional: footer -->
    <footer class="footer">
        <div class="footer-container">
            &copy; <?= date("Y") ?> EasyPrint. All rights reserved.
        </div>
    </footer>
</body>
</html>
