<?php include "../inc/main.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fakefolio-dist.css">
    <title>Fakefolio</title>
</head>
<body>
    <div id="body">
        <div id="content-container">
            <?php include "../inc/header.php"; ?>
            <div id="content">
                <div class="inline-block">
                    <img class="inline-block" src="../_static/icons/shop.png" alt="Shop" width="40">
                    <span class="font-bold align-middle ml-1 text-3xl">Marketplace <small class="text-orange-500"><sup>BETA</sup></small></span>
                </div>
                <a href="#" class="btn-sm btn-primary float-right">Your shop</a>
                <div id="marketplace" class="mt-5 mb-5">
                    <div id="featured">
                        <strong>Featured Shops</strong>
                    </div>
                </div>
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