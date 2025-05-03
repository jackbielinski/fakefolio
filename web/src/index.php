<?php
    include "../inc/main.php";

    if (isset($_SESSION['user_id'])) {
        header("Location: ./home.php");
        exit;
    }
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
            <div id="display-container" class="red-world">
                <h1>Money doesn't grow on trees.</h1>
                <h2>It grows on the web.</h2>
                <p>Fakefolio is a virtual online game where players can gather money through owned businesses and trade on the stock exchange - but without fraud, you'll never make it to the top in time.</p>
            </div><br><br>
            <div id="content">
                <div id="display" class="text-center">
                    <strong class="text-3xl">Two balances. Big difference.</strong><br>
                    <img src="../_static/display-images/dirty-clean-comparison.png" class="mx-auto" alt="Dirty vs. Clean Money" width="600">
                    <span>Similar to the real crooks out there, keep your money dirty to purchase essential items from dealers and process your money through a business to make essential purchases for your operations.</span>
                </div><br>
                <div id="display" class="text-center">
                    <strong class="text-3xl">Fakefolio is a game.</strong><br>
                    <img src="../_static/display-images/people.png" class="mx-auto p-5" alt="Community" width="200">
                    <span>So aim for the top spot. You're fighting against a community of fraudsters that are ruthless. Play your cards right, and you'll be fine.</span>
                </div><br>
                <div id="display" class="text-center">
                    <strong class="text-3xl">Don't get caught.</strong><br>
                    <img src="../_static/display-images/alert.png" class="mx-auto p-5" alt="Alert" width="150">
                    <span>Be on the lookout for private investigators. Your profile has a Credibility and Risk score. The more the feds are intrigued by your movement, the less chance you'll get meeting with dealers and being able to run your business.</span>
                </div><br>
            </div><br>
            <div id="display" class="text-white bg-gradient-to-br from-slate-800 to-black -m-5 p-5 text-center">
                <strong class="text-3xl">Intrigued? Want to be a virtual fraudster?</strong><br>
                <span>We have just the idea for you. Join the online community of Fakefolio and begin capitalizing on the face of Faux Street.</span>
                <br><br>
                <a href="./register.php" class="btn btn-primary">Join the game</a><br><br>
                <span class="text-sm">or, if you already have an existing account, </span><a class="font-bold underline hover:decoration-transparent" href="./login.php"> Log In</a>
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