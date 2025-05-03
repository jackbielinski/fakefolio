<?php
    include "../inc/main.php";

    // Check if user is admin
    if (!isAdmin($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fakefolio-dist.css">
    <script src="../js/modalManager.js"></script>
    <script src="../js/formHandler.js"></script>
    <title>Fakefolio</title>
</head>
<body>
    <div id="body">
        <div id="content-container">
            <?php include "../inc/header.php"; ?>
            <div id="content">
                <div class="inline-block">
                    <img class="inline-block" src="../_static/icons/stocks.png" alt="Stock" width="40">
                    <strong class="text-3xl inline-block align-middle ml-1"><span class="text-green-600">$TICK </span>Stock Name</strong>
                </div>
                <!-- make a price change chart -->
                <div id="chart" class="w-full h-64 bg-gray-100 mt-5 mb-5"></div>
            </div>
        </div>
        <div id="footer" class="text-center">
            <p>Fakefolio is a game. All characters and events in this game - even those based on real people - are entirely
            fictional. Any resemblance to actual persons, living or dead, or actual events is purely coincidental.</p>
            <br><small>&copy; 2025 Fakefolio</small>
        </div>
    </div>
</body>
</html>