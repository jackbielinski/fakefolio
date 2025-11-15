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
            <?php include './include/page_elements/toolbar.php'; ?>
            <div class="header">
                <img src="./_static/banner/f_3.png" alt="Fakefolio Banner">
            </div>
            <div>
                <h1 class="font-bold text-4xl">Register</h1>
                <p>Join Fakefolio today and start your journey in the world of virtual crime!</p>
            </div>
            <div class="mt-3">
                <form action="./api/register.php" method="POST">
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
                    <?php
                        include './include/page_elements/errors.php';

                        if (isset($_GET['error'])) {
                            display_error($_GET['error']);
                        }
                    ?>
                    <button type="submit" name="submit" class="btn primary">Register</button>
                </form>
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