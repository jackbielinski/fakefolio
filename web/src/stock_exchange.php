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
                            let totalValue = 0;
                            data.forEach(stock => {
                                const price = parseFloat(stock.price);
                                const quantity = parseInt(stock.quantity);
                                if (!isNaN(price) && !isNaN(quantity)) {
                                    totalValue += price * quantity;
                                }
                            });
                            const formattedValue = `$${totalValue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                            document.querySelector("#portfolio-value .rank").innerText = formattedValue;

                            setInterval(() => {
                                fetch(`../api/get_user_portfolio.php?user_id=${<?php echo $_SESSION['user_id']; ?>}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        let totalValue = 0;
                                        data.forEach(stock => {
                                            const price = parseFloat(stock.price);
                                            const quantity = parseInt(stock.quantity);
                                            if (!isNaN(price) && !isNaN(quantity)) {
                                                totalValue += price * quantity;
                                            }
                                        });
                                        const formattedValue = `$${totalValue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                                        document.querySelector("#portfolio-value .rank").innerText = formattedValue;
                                    });
                            }, 2500);
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
                            const formatNumber = (num) => parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            document.querySelectorAll(".shares").forEach(shares => {
                                const value = parseInt(shares.innerText);
                            });
                            document.querySelectorAll(".price").forEach(price => {
                                const value = parseFloat(price.innerText.replace(/[$,]/g, ''));
                                price.innerText = `$${formatNumber(value)}`;
                            });
                            document.querySelectorAll(".value").forEach(change => {
                                const value = parseFloat(change.innerText.replace(/[%]/g, ''));
                                change.innerText = `$${formatNumber(value)}`;
                            });
                        });
                    });

                    purchaseButton.addEventListener("click", () => {
                        modalManager.load('../modals/stocks/purchase.php', function () {
                            // use query querySelector
                            const stockSelect = document.getElementById("selectedStock");
                            const quantityInput = document.getElementById("selectedQuantity");

                            // Define cleanMoney in the outer scope
                            let cleanMoney = 0;

                            // Fetch clean money and set balance
                            fetch(`../api/check_balance.php?user_id=${<?php echo $_SESSION['user_id']; ?>}`)
                                .then(response => response.json())
                                .then(data => {
                                    const cleanMoneySpan = document.querySelector("#purchaseSummary #clean");
                                    cleanMoneySpan.innerText = `$${data.clean_money}`;

                                    cleanMoney = parseFloat(data.clean_money.replace(/[$,]/g, ''));
                                });

                            function formatNumber(num) {
                                return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }

                            function updateSubtotal() {
                                const stockId = stockSelect.value;
                                const quantity = parseInt(quantityInput.value);
                                const subtotalSpan = document.querySelector("#purchaseSummary #subtotal");
                                const orderFeeSpan = document.querySelector("#purchaseSummary .fee");
                                const totalSpan = document.querySelector("#purchaseSummary #total");

                                if (!stockId || isNaN(quantity) || quantity <= 0) {
                                    subtotalSpan.innerText = "$0.00";
                                    orderFeeSpan.innerText = "0.0%";
                                    totalSpan.innerText = "$0.00";
                                    return;
                                } else {
                                    // Fetch stock price from get_stock_data.php
                                    fetch(`../api/get_stock_data.php?stock_id=${stockId}`)
                                        .then(response => response.json())
                                        .then(data => {
                                            const stockPrice = parseFloat(data.latest_price);
                                            if (isNaN(stockPrice)) {
                                                subtotalSpan.innerText = "$0.00";
                                                orderFeeSpan.innerText = "0.0%";
                                                totalSpan.innerText = "$0.00";
                                                return;
                                            }
                                            const subtotal = stockPrice * quantity;

                                            // Calculate subtotal
                                            subtotalSpan.innerText = `$${formatNumber(subtotal)}`;
                                        })
                                        .catch(error => {
                                            console.error('Error fetching stock data:', error);
                                        });

                                    // Fetch order fee from get_stock_exchange_settings.php
                                    fetch(`../api/get_stock_exchange_settings.php`)
                                        .then(response => response.json())
                                        .then(data => {
                                            const orderFee = subtotal * (data.order_fee / 100); // Convert percentage to decimal
                                            const total = subtotal + orderFee;

                                            subtotalSpan.innerText = `$${formatNumber(subtotal)}`;
                                            if (subtotal > 0) {
                                                orderFeeSpan.innerText = `${formatNumber(orderFee / subtotal * 100)}%`;
                                            } else {
                                                orderFeeSpan.innerText = "0.0%";
                                            }
                                            totalSpan.innerText = `$${formatNumber(total)}`;

                                            // Check if the total is greater than clean money
                                            if (total > cleanMoney) {
                                                totalSpan.classList.add("text-red-600");
                                                totalSpan.classList.remove("text-green-600");
                                                document.querySelector("#purchaseSummary button").classList.add("disabled");
                                            } else {
                                                totalSpan.classList.add("text-green-600");
                                                totalSpan.classList.remove("text-red-600");
                                                document.querySelector("#purchaseSummary button").classList.remove("disabled");
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error fetching order fee:', error);
                                        });
                                }
                            }

                            stockSelect.addEventListener("change", updateSubtotal);
                            quantityInput.addEventListener("input", updateSubtotal);
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
                            $query = "SELECT stock_id, COUNT(user_id) as user_count FROM shares GROUP BY stock_id ORDER BY user_count DESC LIMIT 5";
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
                                    $latest_price = $stmt->fetchColumn();
                                    if ($latest_price !== false) {
                                        // Format the price as currency
                                        $formatted_price = "$" . number_format($latest_price, 2);
                                        echo "<td>{$formatted_price}</td>";
                                    } else {
                                        echo "<td>$0.00</td>";
                                    }
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
                                const stockRows = Array.from(document.querySelectorAll('.stockstable tbody tr'));

                                // Remove duplicate stocks by ticker
                                const uniqueRows = [];
                                const seenTickers = new Set();
                                stockRows.forEach(row => {
                                    const ticker = row.cells[2].innerText;
                                    if (!seenTickers.has(ticker)) {
                                        uniqueRows.push(row);
                                        seenTickers.add(ticker);
                                    }

                                    // Rank the stocks based on the number of users holding them
                                    const userCount = parseInt(row.cells[3].innerText);
                                    const otherRows = uniqueRows.filter(r => r !== row);
                                    const higherRank = otherRows.filter(r => parseInt(r.cells[3].innerText) > userCount);
                                    const rank = higherRank.length + 1;
                                    row.cells[0].innerText = rank;
                                });

                                setInterval(() => {
                                    uniqueRows.forEach(row => {
                                        const ticker = row.cells[2].innerText;
                                        fetch(`../api/get_stock_data.php?ticker=${ticker}`)
                                            .then(response => response.json())
                                            .then(data => {
                                                const latestPrice = parseFloat(data.latest_price);
                                                const prevPrice = row.cells[5];

                                                function formatNumber(num) {
                                                    return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                                }

                                                // Set the new price and format with commas
                                                const formattedPrice = `$${formatNumber(latestPrice)}`;
                                                prevPrice.innerText = formattedPrice;
                                                // Update the change percentage and color
                                                const changeCell = row.cells[4];
                                                // Store previous price in a data attribute
                                                const prevStoredPrice = parseFloat(row.getAttribute('data-prev-price')) || latestPrice;
                                                const change = ((latestPrice - prevStoredPrice) / prevStoredPrice) * 100;
                                                // Add a sign before the change percentage
                                                const sign = change > 0 ? '+' : '';
                                                // Format the change percentage with commas
                                                const formattedChange = `${sign}${formatNumber(change)}%`;

                                                changeCell.innerText = formattedChange;
                                                // Depending on the change, set the color
                                                if (change > 0) {
                                                    changeCell.classList.add('text-green-600');
                                                    changeCell.classList.remove('text-red-600');
                                                } else if (change < 0) {
                                                    changeCell.classList.add('text-red-600');
                                                    changeCell.classList.remove('text-green-600');
                                                } else {
                                                    changeCell.classList.remove('text-green-600', 'text-red-600');
                                                }
                                                // Update the stored previous price for next interval
                                                row.setAttribute('data-prev-price', latestPrice);

                                                // Calculate the rank based on the number of users holding the stock
                                                const userCount = parseInt(row.cells[3].innerText);
                                                // Compare the user count with the other values
                                                const otherRows = uniqueRows.filter(r => r !== row);
                                                const higherRank = otherRows.filter(r => parseInt(r.cells[3].innerText) > userCount);
                                                const rank = higherRank.length + 1;

                                                // Update the rank cell
                                                row.cells[0].innerText = rank;
                                            })
                                            .catch(error => {
                                                console.error('Error fetching stock data:', error);
                                            });
                                    });
                                }, 2500);
                            </script>
                        </tbody>
                    </table><br>
                    <h1 class="font-bold text-2xl">Listed Stocks</h1>
                    <table id="listed" class="table-auto w-full stockstable mt-2">
                        <thead>
                            <tr>
                                <th class="text-left">Name</th>
                                <th class="text-left">Ticker</th>
                                <th class="text-left">Change</th>
                                <th class="text-left">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Get all stocks
                            $query = "SELECT * FROM stocks";
                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($stocks as $stock) {
                                echo "<tr>";
                                echo "<td>{$stock['stock_name']}</td>";
                                echo "<td>{$stock['stock_ticker']}</td>";
                                // Echo change as 0.00%
                                echo "<td class='font-bold text-black'>0.00%</td>";
                                // Get the latest price
                                $query = "SELECT price FROM stock_prices WHERE stock_id = :id ORDER BY date DESC LIMIT 1";
                                $stmt = $conn->prepare($query);
                                $stmt->bindParam(':id', $stock['stock_id']);
                                $stmt->execute();
                                $latest_price = $stmt->fetchColumn();
                                if ($latest_price !== false) {
                                    // Format the price as currency
                                    $formatted_price = "$" . number_format($latest_price, 2);
                                    echo "<td>{$formatted_price}</td>";
                                } else {
                                    echo "<td>$0.00</td>";
                                }
                                echo "</tr>";
                            }
                            ?>
                            <script>
                                // When the row is clicked, get the ticker from that stock and redirect to the stock page
                                document.querySelectorAll('#listed tbody tr').forEach(row => {
                                    row.addEventListener('click', () => {
                                        const ticker = row.cells[1].innerText;
                                        window.location.href = `stock?ticker=${ticker}`;
                                    });
                                });

                                // Update each listed stock's price and change every 2.5 seconds
                                setInterval(() => {
                                    document.querySelectorAll('#listed tbody tr').forEach(row => {
                                        const ticker = row.cells[1].innerText;
                                        fetch(`../api/get_stock_data.php?ticker=${ticker}`)
                                            .then(response => response.json())
                                            .then(data => {
                                                const latestPrice = parseFloat(data.latest_price);
                                                const priceCell = row.cells[3];
                                                const changeCell = row.cells[2];

                                                function formatNumber(num) {
                                                    return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                                }

                                                // Get previous price from data attribute, or use latestPrice if not set
                                                const prevStoredPrice = parseFloat(row.getAttribute('data-prev-price')) || latestPrice;
                                                // Calculate change percentage
                                                const change = prevStoredPrice === 0 ? 0 : ((latestPrice - prevStoredPrice) / prevStoredPrice) * 100;
                                                const sign = change > 0 ? '+' : '';
                                                const formattedChange = `${sign}${formatNumber(change)}%`;

                                                // Triangle icon logic
                                                let triangleIcon = '';
                                                if (change > 0) {
                                                    triangleIcon = `<img src="../_static/icons/triangle-up.png" alt="Up" class="inline-block align-middle mr-1" style="width:12px;height:12px;">`;
                                                } else if (change < 0) {
                                                    triangleIcon = `<img src="../_static/icons/triangle-down.png" alt="Down" class="inline-block align-middle mr-1" style="width:12px;height:12px;">`;
                                                }

                                                // Update price and change cells
                                                priceCell.innerText = `$${formatNumber(latestPrice)}`;
                                                changeCell.innerHTML = triangleIcon + formattedChange;

                                                // Set color based on change
                                                changeCell.classList.remove('text-green-600', 'text-red-600');
                                                if (change > 0) {
                                                    changeCell.classList.add('text-green-600');
                                                } else if (change < 0) {
                                                    changeCell.classList.add('text-red-600');
                                                }

                                                // Store latest price for next interval
                                                row.setAttribute('data-prev-price', latestPrice);
                                            })
                                            .catch(error => {
                                                console.error('Error fetching stock data:', error);
                                            });
                                    });
                                }, 2500);

                                // Add pagination
                                const rowsPerPage = 10;
                                const totalRows = document.querySelectorAll('#listed .stockstable tbody tr').length;
                                const totalPages = Math.ceil(totalRows / rowsPerPage);
                                const pagination = document.createElement('div');
                                pagination.classList.add('pagination');
                                for (let i = 1; i <= totalPages; i++) {
                                    const pageLink = document.createElement('a');
                                    pageLink.innerText = i;
                                    pageLink.href = '#';
                                    pageLink.classList.add('page-link');
                                    pageLink.addEventListener('click', (e) => {
                                        e.preventDefault();
                                        const startRow = (i - 1) * rowsPerPage;
                                        const endRow = startRow + rowsPerPage;
                                        document.querySelectorAll('.stockstable tbody tr').forEach((row, index) => {
                                            row.style.display = (index >= startRow && index < endRow) ? '' : 'none';
                                        });
                                    });
                                    pagination.appendChild(pageLink);
                                }
                                document.querySelector('#listed').appendChild(pagination);
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