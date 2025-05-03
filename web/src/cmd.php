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
                    <img class="inline-block" src="../_static/icons/computer.png" alt="Computer" width="40">
                    <span class="font-bold align-middle ml-1 text-3xl">Command <small class="text-orange-500"><sup>BETA</sup></small></span>
                </div>
                <!-- Make an iframe and put an image overlaying it -->
                <iframe src="../modals/cmd.php" id="command-frame" class="mx-auto h-60" scrolling="no" frameborder="0"></iframe>
                <div id="frame">
                    <img src="../_static/tv_frame.png" class="w-full" alt="TV Frame">
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