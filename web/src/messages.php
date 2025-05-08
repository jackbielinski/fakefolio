<?php
include "../inc/main.php";
?>
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
            <div>
                <img src="../_static/icons/mail.png" alt="Mail" width="40" class="inline-block">
                <span class="font-bold align-middle ml-1 text-3xl">Messages</span>
            </div><br>
            <div id="no-messages" class="text-center text-gray-400">
                <strong class="text-3xl">No messages yet.</strong><br>
                <span>Check back later for updates.</span>
            </div>
            <!-- message -->
            <div class="message">
                <div id="sender">
                    <img src="../_static/pfp/default.png" alt="Sender" width="30" class="inline-block">
                    <strong>Message Sender</strong>
                </div>
                <strong class="text-xl">Message Subject</strong><br>
                <span class="text-sm text-opacity-85">Date: 2025-01-01</span><br><br>
                <span id="message-preview">Message preview...</span>
            </div>
        </div>
        <div id="footer" class="text-center">
            <p>Fakefolio is a game. All characters and events in this game - even those based on real people - are
                entirely
                fictional. Any resemblance to actual persons, living or dead, or actual events is purely coincidental.
            </p>
            <br><small>&copy; 2025 Fakefolio</small>
        </div>
    </div>
</body>

</html>