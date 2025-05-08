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
            <h1 class="text-5xl font-bold" id="welcome-text">Welcome back.</h1><br>
            <span>Welcome to Fakefolio — the only place where your imagination is your portfolio and the rules are optional. Build shady
            shell companies, move fake cash, and climb the leaderboard of the most corrupt masterminds in the game. No real money.
            No real consequences. Just pure digital fraud fantasy. Create your alias, start your empire, and out-scheme the rest.
            Think you’ve got what it takes to thrive in the grey area? Let’s find out.</span>
            <br><br>
            <div id="info-section" class="flex flex-wrap justify-center gap-8">
                <div class="info-item flex items-center">
                    <img src="../_static/icons/game-controller.png" alt="Virtual Gameplay" width="100" class="mr-4">
                    <div>
                        <h2 class="text-xl font-bold">Virtual Gameplay</h2>
                        <p>Fakefolio is a completely virtual experience. All actions take place in a simulated environment, ensuring no real-world consequences.</p>
                    </div>
                </div>
                <div class="info-item flex items-center">
                    <img src="../_static/icons/customs.png" alt="Legal Fiction" width="100" class="mr-4">
                    <div>
                        <h2 class="text-xl font-bold">Legal Fiction</h2>
                        <p>Everything in Fakefolio is fictional. No real money, no real fraud — just a fun and immersive game for creative minds.</p>
                    </div>
                </div>
                <div class="info-item flex items-center">
                    <img src="../_static/icons/fun.png" alt="Ethical Fun" width="100" class="mr-4">
                    <div>
                        <h2 class="text-xl font-bold">Ethical Fun</h2>
                        <p>Designed for entertainment, Fakefolio ensures that all gameplay stays within the boundaries of ethical virtual fun.</p>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                const welcomeMessages = [
                    "Ready to fake your fortune?",
                    "Join the shadow economy.",
                    "Create your cover identity.",
                    "Time to open your first shell corp.",
                    "One click closer to financial fiction.",
                    "Sign up and stay off the radar.",
                    "Build your empire — no paperwork required.",
                    "No questions asked. Just gains.",
                    "Start your hustle.",
                    "The market needs new manipulators.",
                    "Create your digital disguise.",
                    "Your new Fakefolio awaits.",
                    "Forge your financial future.",
                    "Time to go full sketchy.",
                    "Sign up. Get shady.",
                    "New here? We’ll fix that.",
                    "Be your own fraudster.",
                    "Create a profile. Make it believable.",
                    "Make fake moves that feel real.",
                    "Scammers welcome.",
                    "All aliases accepted.",
                    "Join. Scheme. Repeat.",
                    "Your identity is safe-ish with us.",
                    "The first step to laundering success.",
                    "Setup your black market profile.",
                    "Ready to blur the lines?",
                    "Enter the simulation.",
                    "Start trading lies today.",
                    "We don’t check IDs.",
                    "Create your alternate self.",
                    "Build wealth without limits... or laws.",
                    "One account. Infinite fraud.",
                    "Create your first fictional firm.",
                    "You bring the ambition. We'll bring the loopholes.",
                    "You look like trouble. We like that.",
                    "Start dirty. Get rich.",
                    "Fool the market like a pro.",
                    "The game begins with a name.",
                    "Step into the under-market.",
                    "Secure your seat at the shady table.",
                    "Your Fakefolio is just one signup away.",
                    "Don’t just watch — scam with style.",
                    "Be anyone. Own everything.",
                    "Write your own success scam.",
                    "All fake billionaires start here.",
                    "Make a name. Then fake it.",
                    "This could be the start of something unethical.",
                    "Almost legal. Always profitable.",
                    "Sign up, then disappear.",
                    "Registration never felt this sketchy."
                ];

                const randomMessage = welcomeMessages[Math.floor(Math.random() * welcomeMessages.length)];
                document.getElementById("welcome-text").innerText = randomMessage;
            </script>
            <div id="content">
                <form id="registration" method="post">
                    <strong>Username</strong>
                    <input type="text" name="username" id="username" placeholder="Enter your username" required><br>
                    <strong>Email</strong>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required><br>
                    <strong>Password</strong>
                    <input type="password" name="password" id="password" placeholder="Enter your password"
                        required><br><br>
                    <input type="submit" name="submit" value="Register" class="btn btn-primary"><strong id="form-messages" class="text-red-700 ml-2"></strong><br><br>
                    <span class="text-sm">or, if you don't have an account yet, </span><a
                        class="font-bold underline hover:decoration-transparent" href="login.php">Log In</a><br>
                </form>
            </div>
            <script>
                document.getElementById("registration").addEventListener("submit", function (event) {
                    event.preventDefault(); // Prevent the default form submission

                    const formData = new FormData(this); // Create a FormData object from the form

                    fetch("../api/register.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.text()) // instead of .json()
                    .then(text => {
                        console.log("Raw response:", text); // See what's returned
                        const data = JSON.parse(text); // Now safely parse
                        if (data.error) {
                            document.getElementById("form-messages").innerText = data.error;
                            document.getElementById("form-messages").classList.remove("hidden");
                        } else {
                            window.location.href = "home.php";
                        }
                    })
                });
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