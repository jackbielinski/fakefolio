<?php
include dirname(__DIR__, 2) . '/include/backend/main.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/ff_dist.css">
    <title>Fakefolio</title>
</head>

<body>
    <div id="body">
        <div class="container">
            <?php include dirname(__DIR__, 2) . '/include/page_elements/toolbar-header.php'; ?>
            <div>
                <img src="./_static/wordmark/f_casino.png" class="mx-auto my-6" alt="Fakefolio Casino">
                <p class="text-gray-400 text-xs uppercase text-center mt-2">The Fakefolio Casino is a virtual entertainment feature that uses only in-game fictional currency. All bets, winnings, and losses are simulated and have no real-world monetary value. Fakefolio does not offer or promote real gambling or financial wagering; participation in the casino is solely for entertainment purposes, and all outcomes are digitally generated without real money or prizes involved.</p>
            </div>
            <div id="restricted" class="my-5 text-center">
                <img src="./_static/icon/closed_signage.jpg" alt="Closed Signage" class="mx-auto" width="200">
                <h1 class="mt-3 font-bold text-2xl">We're sorry.</h1>
                <p>To enter the casino, you must have at least <img src="./_static/icon/dirty_money.png" alt="Dirty Money" class="inline" width="16">&nbsp;<span class="balance dirty">$30,000</span> in dirty money (you have <img src="./_static/icon/dirty_money.png" alt="Dirty Money" class="inline" width="16">&nbsp;<span id="funds-available" class="balance dirty">$0</span>).</p>
            </div>
            <div id="casino-content" style="display:none;">
                <h1>Hey you made it</h1>
            </div>
        </div>
        <?php
                // Check if user has at least $30,000 in dirty money
                $stmt = $pdo->prepare("SELECT dirty_money FROM users WHERE id = :id");
                $stmt->execute(['id' => $_SESSION['user_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                echo '<script>
                    document.getElementById("funds-available").innerText = "$" + ' . json_encode(number_format($user['dirty_money'], 2)) . ';
                </script>';

                if ($user && $user['dirty_money'] >= 30000) {
                    // User has enough dirty money, show casino content
                    echo '<script>
                        document.getElementById("restricted").style.display = "none";
                        document.getElementById("casino-content").style.display = "block";
                    </script>';
                } else {
                    // Remove casino content if user doesn't have enough funds
                    echo '<script>
                        var casinoContent = document.getElementById("casino-content");
                        if (casinoContent) {
                            casinoContent.parentNode.removeChild(casinoContent);
                        }
                    </script>';
                }
            ?>
        <div class="footer">
            <p>Fakefolio is a virtual game for entertainment only. All currency, stocks, and trades are fictional and
                have no
                real-world value.</p>
            <p>&copy; 2024 Fakefolio. All rights reserved.</p>
        </div>
    </div>
</body>

</html>