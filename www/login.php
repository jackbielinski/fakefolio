<?php
    include './include/backend/main.php';

    // Redirect to dashboard if already logged in
    if (isset($_SESSION['user_id'])) {
        header("Location: ./home");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/ff_dist.css">
    <title>Fakefolio</title>
</head>

<body>
    <div id="body">
        <div class="container">
            <?php include './include/page_elements/toolbar-header.php'; ?>
            <div>
                <h1 class="font-bold text-4xl">Log in</h1>
                <p>Step back into the world of Fakefolio.</p>
                <p class="text-gray-400 text-sm">Don't have an account yet? <a href="register">Register here</a>.</p>
            </div>
            <div class="mt-3">
                <form id="login-form" method="POST">
                    <p>Username or Email</p>
                    <input type="text" id="username_email" name="username_email" maxlength="255" required>
                    <p>Password</p>
                    <input type="password" id="password" name="password" minlength="8" required>
                    <div class="error-message" id="error-message" style="display: none;">
                    </div>
                    <button type="submit" name="submit" class="btn primary">Log in</button>
                </form>
                <script src="./js/login.js"></script>
            </div>
        </div>
        <div class="footer">
            <p>Fakefolio is a virtual game for entertainment only. All currency, stocks, and trades are fictional and
                have no
                real-world value.</p>
            <p>&copy; 2024 Fakefolio. All rights reserved.</p>
        </div>
    </div>
</body>

</html>