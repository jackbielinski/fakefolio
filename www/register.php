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
                <h1 class="font-bold text-4xl">Register</h1>
                <p>Join Fakefolio today and start your journey in the world of virtual crime!</p>
                <p class="text-gray-400 text-sm">Already have an existing account? <a href="login">Log in here</a>.</p>
            </div>
            <div class="mt-3">
                <form id="register-form" method="POST">
                    <p>Username <span class="text-gray-400">(20 characters max)</span></p>
                    <input type="text" id="username" name="username" maxlength="20" required>
                    <p>Email <span class="text-gray-400">(valid email address)</span></p>
                    <input type="email" id="email" name="email" required>
                    <p>Password <span class="text-gray-400">(at least 8 characters)</span></p>
                    <input type="password" id="password" name="password" minlength="8" required>
                    <div class="my-3">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</label>
                    </div>
                    <button type="submit" name="submit" class="btn primary">Register</button>
                    <div id="message" class="hidden font-bold my-2"></div>
                </form>
                <script src="./js/register.js"></script>
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