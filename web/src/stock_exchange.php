<?php
include "../inc/main.php";

$active_user = getUserInfo($_SESSION['user_id']);
if (!$active_user) {
    header("Location: /login");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fakefolio-dist.css">
    <!-- JS -->
    <script src="../js/modalManager.js"></script>
    <script src="../js/formHandler.js"></script>
    <title>Fakefolio</title>
</head>
<body>
    <div id="body">
        <div id="content-container">
            <?php include "../inc/header.php"; ?>
            <div class="inline-block">
                <img class="inline-block" src="../_static/fsx_wordmark_red.png" alt="FSX Logo" width="40">
                <strong class="text-3xl inline-block align-middle ml-1">Stock Exchange</strong>
            </div>
            <div id='balance-bar' class='inline-block float-right'>
                <div id="balance-bar-flex" class="sm:my-2 md:my-0 flex justify-end gap-3 items-center">
                    <div id="portfolio-value">
                        <span class="rank">$0</span>
                        <span class="text-sm font-bold text-gray-400">VALUE</span>
                    </div>
                    <div id="credibility">
                        <span class="credibility">0</span>
                        <span class="text-sm font-bold text-gray-400">CREDIBILITY</span>
                    </div>
                    <div id="risk">
                        <span class="risk">0</span>
                        <span class="text-sm font-bold text-gray-400">RISK</span>
                    </div>
                </div>
            </div><br><br>
            <script>
                function fetchValCredRisk() {
                    fetch(`../api/get_credibility_risk.php?user_id=${<?php echo $_SESSION['user_id']; ?>}`)
                        .then(response => response.json())
                        .then(data => {
                            document.querySelector(".credibility").innerText = Number(data.credibility).toLocaleString();
                            document.querySelector(".risk").innerText = Number(data.risk).toLocaleString();
                        });

                    fetch(`../api/get_user_portfolio.php?user_id=${<?php echo $_SESSION['user_id']; ?>}`)
                        .then(response => response.json())
                        .then(data => {
                            // Get JSON and for each stock, get the price and multiply by quantity
                            let totalValue = 0;
                            data.forEach(stock => {
                                const price = parseFloat(stock.price);
                                const quantity = parseInt(stock.quantity);
                                if (!isNaN(price) && !isNaN(quantity)) {
                                    totalValue += price * quantity;
                                }
                            });
                            // Format the total value as currency
                            const formattedValue = `$${totalValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                            document.querySelector("#portfolio-value .rank").innerText = formattedValue;
                        });
                }

                // Call the function to fetch and display credibility and risk
                fetchValCredRisk();

                // Update the values every 5 seconds
                setInterval(fetchValCredRisk, 2500);
            </script>
            <div id="content">
                <div id="buttons">
                    <button id="purchase" class="btn-sm btn-primary">Buy Stock</button>
                    <button id="holdings" class="btn-sm btn-primary">My Holdings</button>
                </div><br>
                <script>
                    const holdingsButton = document.getElementById("holdings");
                    const purchaseButton = document.getElementById("purchase");

                    holdingsButton.addEventListener("click", () => {
                        modalManager.load('../modals/stocks/holdings.php', function () {

                        });
                    });

                    purchaseButton.addEventListener("click", () => {
                        modalManager.load('../modals/stocks/purchase.php', function () {

                        });
                    });
                </script>
                <div id="trending">
                    <h1 class="font-bold text-2xl">Trending</h1>
                    <!-- make a table with a column for rank, name, ticker, users holding, change, and price -->
                    <table class="table-auto w-full stockstable mt-2">
                        <thead>
                            <tr>
                                <th class="text-left">Rank</th>
                                <th class="text-left">Name</th>
                                <th class="text-left">Ticker</th>
                                <th class="text-left">Users Holding</th>
                                <th class="text-left">Change</th>
                                <th class="text-left">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $conn = getDB();

                                // Get the most popular stocks by number of users holding them
                                $query = "SELECT stock_id, COUNT(user_id) as user_count FROM shares GROUP BY stock_id ORDER BY user_count DESC";
                                $stmt = $conn->prepare($query);
                                $stmt->execute();
                                $stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                $rank = 1;
                                $seenTickers = [];

                                foreach ($stocks as $stock) {
                                    $stock_id = $stock['stock_id'];
                                    $query = "SELECT * FROM stocks WHERE stock_id = :id";
                                    $stmt = $conn->prepare($query);
                                    $stmt->bindParam(':id', $stock_id);
                                    $stmt->execute();
                                    $stock_data = $stmt->fetch(PDO::FETCH_ASSOC);

                                    // Skip duplicate tickers
                                    if ($stock_data && !in_array($stock_data['stock_ticker'], $seenTickers)) {
                                        $seenTickers[] = $stock_data['stock_ticker'];
                                        echo "<tr>";
                                        echo "<td>{$rank}</td>";
                                        echo "<td>{$stock_data['stock_name']}</td>";
                                        echo "<td>{$stock_data['stock_ticker']}</td>";

                                        // Count users holding the stock
                                        $query = "SELECT COUNT(*) FROM shares WHERE stock_id = :id";
                                        $stmt = $conn->prepare($query);
                                        $stmt->bindParam(':id', $stock_id);
                                        $stmt->execute();
                                        $user_count = $stmt->fetchColumn();
                                        echo "<td>{$user_count}</td>";
                                        // Echo change as 0.00%
                                        echo "<td class='font-bold text-black'>0.00%</td>";
                                        // Get the latest price
                                        $query = "SELECT price FROM stock_prices WHERE stock_id = :id ORDER BY date DESC LIMIT 1";
                                        $stmt = $conn->prepare($query);
                                        $stmt->bindParam(':id', $stock_id);
                                        $stmt->execute();
                                        $price_data = $stmt->fetch(PDO::FETCH_ASSOC);
                                        if ($price_data) {
                                            $price = $price_data['price'];
                                            // Format price as $X.XX
                                            echo "<td>\$" . number_format($price, 2) . "</td>";
                                        } else {
                                            echo "<td>N/A</td>";
                                        }

                                        echo "</tr>";
                                        $rank++;
                                    }
                                }
                            ?>
                            <script>
                                // When the row is clicked, get the ticker from that stock and redirect to the stock page
                                document.querySelectorAll('.stockstable tr').forEach(row => {
                                    row.addEventListener('click', () => {
                                        const ticker = row.cells[2].innerText;
                                        window.location.href = `stock?ticker=${ticker}`;
                                    });
                                });

                                // Constantly update every stock
                                // Get the new date and price, calculate change, apply color, and set values
                                // Store initial prices for each stock row (skip header row)
                                const stockRows = Array.from(document.querySelectorAll('.stockstable tbody tr'));
                                // Store previous prices for each ticker
                                const previousPrices = {};
                                // Store initial prices for change calculation
                                const initialPrices = {};
                                stockRows.forEach(row => {
                                    const ticker = row.cells[2].innerText;
                                    const price = parseFloat(row.cells[5].innerText.replace('$', ''));
                                    if (!isNaN(price) && isFinite(price)) {
                                        initialPrices[ticker] = price;
                                        previousPrices[ticker] = price;
                                    }
                                });

                                // Remove duplicate stocks by ticker
                                const uniqueRows = [];
                                const seenTickers = new Set();
                                stockRows.forEach(row => {
                                    const ticker = row.cells[2].innerText;
                                    if (!seenTickers.has(ticker)) {
                                        uniqueRows.push(row);
                                        seenTickers.add(ticker);
                                    }
                                });

                                setInterval(() => {
                                    uniqueRows.forEach(row => {
                                        const ticker = row.cells[2].innerText;
                                        fetch(`../api/get_stock_data.php?ticker=${ticker}`)
                                            .then(response => response.json())
                                            .then(data => {
                                                const latestPrice = parseFloat(data.latest_price);
                                                const initialPrice = initialPrices[ticker];
                                                if (!isNaN(latestPrice) && isFinite(latestPrice) && previousPrices[ticker] && previousPrices[ticker] !== 0) {
                                                    // Calculate change based on previous price
                                                    const change = ((latestPrice - previousPrices[ticker]) / previousPrices[ticker]) * 100;
                                                    // Format numbers
                                                    const sign = change > 0 ? '+' : (change < 0 ? '-' : '');
                                                    const formattedPrice = `$${latestPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                                                    const formattedChange = `${change.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}%`;
                                                    // Update the price and change
                                                    row.cells[5].innerText = formattedPrice;
                                                    row.cells[4].innerText = sign + formattedChange;
                                                    // Update the color
                                                    const tailwindColor = change > 0 ? 'text-green-600' : (change < 0 ? 'text-red-600' : 'text-black');
                                                    row.cells[4].className = `font-bold ${tailwindColor}`;
                                                    // Update previous price for next interval
                                                    previousPrices[ticker] = latestPrice;
                                                } else if (!isNaN(latestPrice) && isFinite(latestPrice)) {
                                                    // If previous price is invalid, just set the price and 0.00% change
                                                    row.cells[5].innerText = `$${latestPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                                                    row.cells[4].innerText = '0.00%';
                                                    row.cells[4].className = 'font-bold text-black';
                                                    previousPrices[ticker] = latestPrice;
                                                }
                                            });
                                    });
                                }, 2500);
                            </script>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="modal" class="modal">
            <div id="modal-content"></div>
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