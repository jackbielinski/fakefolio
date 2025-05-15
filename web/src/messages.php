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
            <div id="messages" class="hidden">
                <div class="message">
                    <div class="message-header">
                        <strong>Sender Name</strong>
                        <span class="text-gray-400">Date</span>
                    </div>
                    <div class="message-body">
                        <p>This is a sample message content. It can be multiple lines long.</p>
                    </div>
                </div>
                <!-- Repeat the message block for more messages -->
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