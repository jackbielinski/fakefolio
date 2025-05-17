<?php
    include "../inc/main.php";

    $_GET['ticker'] = $_GET['ticker'] ?? null;

    if (!isset($_GET['ticker']) || empty($_GET['ticker'])) {
        header("Location: stock_exchange");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fakefolio-dist.css">
    <script src="../js/modalManager.js"></script>
    <script src="../js/formHandler.js"></script>
    <title>Fakefolio</title>
</head>
<body>
    <div id="body">
        <div id="content-container">
            <?php include "../inc/header.php"; ?>
            <div id="content">
                <?php
                    $stock = getStockByTicker($_GET['ticker']);

                    if (!$stock) {
                        echo "<div class='text-center'>";
                        echo "<h2 class='text-2xl'>Stock not found</h2>";
                        echo "<p class='text-gray-500'>The stock you are looking for does not exist.</p><br>";
                        echo "<a href='stock_exchange' class='btn btn-primary'>Go back to Stock Exchange</a>";
                        exit;
                    } else {
                        $stockId = $stock['stock_id'];
                        $latest_price = getStockPrice($stockId);
                        if (is_array($latest_price) && isset($latest_price['price'], $latest_price['date'])) {
                            $price = $latest_price['price'];
                            $price_date = date("F j, Y", strtotime($latest_price['date']));
                        } else {
                            $price = "N/A";
                            $price_date = "N/A";
                        }

                        echo "<h2 class='text-2xl'>" . htmlspecialchars($stock['stock_name']) . " ($" . htmlspecialchars($stock['stock_ticker']) . ")</h2>";
                        echo "<p class='text-gray-500'>Latest Price: <span class='text-green-700'>";
                        if (is_numeric($price)) {
                            echo "$" . number_format($price, 2);
                        } else {
                            echo htmlspecialchars($price);
                        }
                        echo "</span> on " . $price_date . "</p>";
                        echo "<div class='mt-4'>";
                        echo "<button class='btn btn-primary' id='buy-stock' data-stock-id='" . htmlspecialchars($stockId) . "'>Buy</button>";
                        echo "<button class='btn btn-secondary' id='sell-stock' data-stock-id='" . htmlspecialchars($stockId) . "'>Sell</button>";
                        echo "<button class='btn btn-secondary' id='add-watchlist' data-stock-id='" . htmlspecialchars($stockId) . "'>Add to Watchlist</button>";
                        echo "</div>";
                    }
                ?>
                <script>
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