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
                <?php
                    // Display balance bar
                    if (isset($_SESSION['user_id'])) {
                        $user_id = $_SESSION['user_id'];

                    echo "<div id='balance-bar' class='float-right'>";
                    echo "    <div id='dirty-money' class='inline-block'>";
                    echo "        <span id='dirty'>$10,000</span><br>";
                    echo "        <span id='subtitle'>DIRTY MONEY</span>";
                    echo "    </div>";
                    echo "    <div id='clean-money' class='ml-2 inline-block'>";
                    echo "        <span id='clean'>$10,000</span><br>";
                    echo "        <span id='subtitle'>CLEAN MONEY</span>";
                    echo "    </div>";
                    echo "</div>";

                    $profile_picture_path = getUserById($user_id)["profile_picture_path"];
                    echo "<img src='../_static/" . $profile_picture_path . "' alt='Profile Picture' width='100' class='inline-block mr-3'>";
                    }
                ?>
                <div id="playerstats" class="inline-block align-top">
                    <?php
                        $username = getUserById($_SESSION["user_id"])["username"];

                        // Find rank based on credibility

                        echo "<strong class='text-2xl'>Welcome back, " . htmlspecialchars($username) . "</strong><br>";
                        echo "<div class='stats'>";
                        echo "<span class='credibility'>0</span> <strong class='text-sm text-gray-400'>CREDIBILITY</strong><br>";
                        echo "<span class='risk'>0</span> <strong class='text-sm text-gray-400'>RISK</strong><br>";
                        echo "<span class='rank'>#</span> <strong class='text-sm text-gray-400'>RANK</strong><br>";
                        echo "</div>";
                    ?>
                </div>
                <script>
                    // Get user ID from PHP session
                    const userId = <?php echo $_SESSION['user_id'] ?? 'null'; ?>;

                    function fetchBalance() {
                        fetch(`../api/check_balance.php?user_id=${userId}`)
                        .then(response => response.json())
                        .then(data => {
                            const formatNumber = (num) => parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            document.getElementById("dirty").innerText = `$${formatNumber(data.dirty_money)}`;
                            document.getElementById("clean").innerText = `$${formatNumber(data.clean_money)}`;
                        });
                    }

                    function fetchCreditbilityRisk() {
                        fetch(`../api/get_credibility_risk.php?user_id=${userId}`)
                        .then(response => response.json())
                        .then(data => {
                            const formatNumber = (num) => parseInt(num).toLocaleString('en-US');
                            document.querySelector(".credibility").innerText = formatNumber(data.credibility);
                            document.querySelector(".risk").innerText = formatNumber(data.risk);
                        });
                    }

                    function fetchRank() {
                        fetch(`../api/get_user_rank.php?user_id=${userId}`)
                        .then(response => response.json())
                        .then(data => {
                            document.querySelector(".rank").innerText = `#${data.rank}`;
                        });
                    }

                    function fetchProfilePicture() {
                        fetch(`../api/get_profile_data.php?user_id=${userId}`)
                        .then(response => response.json())
                        .then(data => {
                            const profilePicturePath = data.profile_picture_path;
                            const profilePicture = document.getElementById("pfp");
                            if (profilePicturePath) {
                                profilePicture.src = `../_static/${profilePicturePath}`;
                            } else {
                                profilePicture.src = "../_static/pfp/default.png";
                            }
                        });
                    }

                    fetchBalance();
                    fetchCreditbilityRisk();
                    fetchRank();

                    setInterval(() => {
                        fetchBalance();
                        fetchCreditbilityRisk();
                        fetchProfilePicture();
                        fetchRank();
                    }, 5000); // Update every 5 seconds
                </script>
                <div id="main-content" class="mt-5">
                    <div id="content-header">
                        <img src="../_static/icons/objective.png" alt="Objective" width="30" class="inline-block">
                        <h2 class="font-bold uppercase text-2xl align-middle ml-1 inline-block">Objectives</h2>
                        <a href="../objectives.php" class="float-right text-sm text-gray-400">View All</a>
                        <div id="objectives-cleared" class="text-center text-gray-400 mt-2">
                            <strong>You have no new objectives.</strong>
                        </div>
                    </div><br>
                    <?php
                        $message_settings = getUserSocialSettings($_SESSION['user_id'])["allow_messages"];
                        if ($message_settings == 1) {
                            echo "<div id='content-header'>";
                            echo "<img src='../_static/icons/mail.png' alt='Messages' width='30' class='inline-block'>";
                            echo "<h2 class='font-bold uppercase text-2xl align-middle ml-2 inline-block'>Messages</h2>";
                            echo "<a href='../messages.php' class='float-right text-sm text-gray-400'>View All</a>";
                            echo "<div id='messages-cleared' class='text-center text-gray-400 mt-2'>";
                            echo "<strong>You have no new messages.</strong>";
                            echo "</div>";
                            echo "</div><br>";
                        }
                    ?>
                    <div id="content-header">
                        <img src="../_static/icons/news.png" alt="Newspaper" width="30" class="inline-block">
                        <h2 class="font-bold uppercase text-2xl align-middle ml-1 inline-block">News</h2>
                        <a href="../objectives.php" class="float-right text-sm text-gray-400">View All</a><br>
                        <div id="news-cleared" class="text-center text-gray-400 mt-2">
                            <strong>There are no recent news posts.</strong>
                        </div>
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