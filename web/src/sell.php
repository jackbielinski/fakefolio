<?php include "../inc/main.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fakefolio-dist.css">
    <script src="../js/modalManager.js"></script>
    <title>Fakefolio</title>
</head>
<body>
    <div id="body">
        <div id="content-container">
            <?php include "../inc/header.php"; ?>
            <div id="content">
                <div>
                    <img class="inline-block" src="../_static/icons/package.png" alt="Package" width="40">
                    <span class="font-bold align-middle ml-1 text-3xl">Merchant Portal <small class="text-orange-500"><sup>BETA</sup></small></span>
                    <div class="float-right align-middle">
                        <a href="#" id="sell">
                            <img src="../_static/icons/add.png" alt="Add" width="30" class="inline-block">
                            <strong class="text-green-700 text-xl align-middle">Sell a product</strong>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div id="modal" class="modal">
            <div id="modal-content" class="hidden"></div>
        </div>
        <script>
            document.getElementById("sell").addEventListener("click", function(event) {
                event.preventDefault(); // Prevent the default action of the link

                // Load the modal content from the PHP file
                modalManager.load("../modals/sell.php", function() {
                    console.log("Modal content loaded successfully.");
                });
            });
        </script>
        <div id="footer" class="text-center">
            <p>Fakefolio is a game. All characters and events in this game - even those based on real people - are entirely
            fictional. Any resemblance to actual persons, living or dead, or actual events is purely coincidental.</p>
            <br><small>&copy; 2025 Fakefolio</small>
        </div>
    </div>
</body>
</html>