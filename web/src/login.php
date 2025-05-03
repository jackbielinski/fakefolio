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
            <div id="display-container" class="red-people">
                <h1 id="welcome-text">Welcome back.</h1>
                <form action="../api/login.php" method="post">
                    <strong>Username</strong>
                    <input type="text" name="username" id="username" placeholder="Enter your username" required><br>
                    <strong>Password</strong>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required><br><br>
                    <input type="submit" name="submit" value="Log In" class="btn btn-primary"><br><br>
                    <span class="text-sm">or, if you don't have an account yet, </span><a class="font-bold underline hover:decoration-transparent" href="#"> Register</a><br>
                </form>
            </div>
            <script type="text/javascript">
                const welcomeMessages = [
                        "Welcome back, legend.",
                        "Good to see you again.",
                        "The market missed you.",
                        "Back for more chaos?",
                        "Hey there, boss.",
                        "Fake it till you make it.",
                        "Back in the game, I see.",
                        "Let's cook again.",
                        "Time to make fake stacks.",
                        "You again? Let's roll.",
                        "Just when we got quiet...",
                        "Reconnected. Ready to scheme.",
                        "Your portfolio awaits.",
                        "Welcome back, mastermind.",
                        "Return of the trader.",
                        "Dirty money never sleeps.",
                        "Nice to see your shady face.",
                        "Vault unlocked.",
                        "The system is watching again.",
                        "Let's hustle.",
                        "The boardroom missed you.",
                        "Ready to manipulate some markets?",
                        "Always a pleasure, boss.",
                        "That fake cash won't stack itself.",
                        "Hope you brought your burner.",
                        "Time to wreck the economy again.",
                        "Laundering time.",
                        "Greed mode activated.",
                        "Power suit on? Let's go.",
                        "Faux Street's finest returns.",
                        "Spreadsheets trembling.",
                        "Markets are shaking already.",
                        "Back to the digital grind.",
                        "Resuming your financial fantasies.",
                        "Whispers say you're back.",
                        "One more deal won't hurt, right?",
                        "Check your offshore account.",
                        "Welcome back, big brain.",
                        "Let the Fakefolio flow.",
                        "You’ve got insider vibes today.",
                        "The bots salute you.",
                        "Still not banned? Impressive.",
                        "Risk is just a number.",
                        "They thought you quit.",
                        "What scandal today?",
                        "Your holdings are... unstable.",
                        "Your shell corps missed you.",
                        "Ready for another cover-up?",
                        "All eyes on your next move.",
                        "Welcome to the simulation."
                    ];

                const randomMessage = welcomeMessages[Math.floor(Math.random() * welcomeMessages.length)];
                document.getElementById("welcome-text").innerText = randomMessage;
            </script>
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