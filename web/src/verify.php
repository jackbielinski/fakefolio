<?php
    include "../inc/main.php";

    $verificationCode = $_GET['verificationCode'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fakefolio-dist.css">
    <title>E-Mail Verification</title>
</head>
<body>
    <div id="body">
        <div id="content-container">
            <?php include "../inc/header.php"; ?>
            <div id="content">
                <div class="inline-block">
                    <img class="inline-block" src="../_static/icons/mail.png" alt="Mail" width="40">
                    <span class="font-bold align-middle ml-1 text-3xl">E-Mail Verification</span>
                </div>
                <a id="skipBtn" href="home.php" class="float-right align-bottom text-2xl text-red-600 hover:text-red-800 hover:underline">Skip & risk losing my account</a>
                <div id="checkVerify" class="text-center mt-7">
                    <strong class="text-4xl">Check your inbox for an E-mail from @fakefolio.com.</strong><br><br>
                    <span>We collect your E-mail address to relay important account updates and will never send marketing E-mails to your inbox, ever. Leaving your account unverified may lessen your chance of retreiving your account in the event you get locked out.</span>
                    <br><br><strong>Is the link not working? <a id="verifyWithCode" class="text-red-600 hover:text-red-800 hover:underline">Enter your verification code.</a></strong>
                    <div id="verification-code" class="hidden mt-5">
                        <input type="text" id="code" placeholder="Enter verification code">
                        <button id="verifyBtn" class="btn btn-primary">Verify</button>
                    </div>
                </div>
                <div id="verifySuccess" class="hidden text-center mt-7">
                    <strong id="title" class="text-4xl">E-mail verified successfully!</strong><br><br>
                    <span id="description" class="text-xl">Thank you for verifying your E-mail address. You can now enjoy all the features of Fakefolio.</span>
                    <br><br><a href="home.php" id="continueBtn" class="btn btn-primary">Continue</a><br><br>
                </div>
                <script>
                    window.onload = function() {
                        const skipBtn = document.getElementById("skipBtn");
                        const verificationCode = document.getElementById("verification-code");
                        const verifyBtn = document.getElementById("verifyBtn");
                        const verifyWithCode = document.getElementById("verifyWithCode");

                        verifyWithCode.addEventListener("click", function() {
                            verificationCode.classList.toggle("hidden");
                        });

                        // Check if verification code is provided in the URL
                        if (<?php echo json_encode($verificationCode); ?>) {
                            document.getElementById("checkVerify").classList.add("hidden");
                            document.getElementById("verifySuccess").classList.remove("hidden");
                            skipBtn.classList.add("hidden");
                        }
                    };
                </script>
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