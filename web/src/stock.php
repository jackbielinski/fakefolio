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
    <script src="https://cdn.plot.ly/plotly-3.0.1.min.js" charset="utf-8"></script>
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

                    echo "<img src='../_static/icons/stocks.png' alt='Stock Icon' class='align-top inline-block mr-2' width='35'>";
                    echo "<div id='stock-info-" . $stockId . "' class='inline-block mb-4'>";
                    echo "<h2 class='text-2xl'>" . htmlspecialchars($stock['stock_name']) . " ($" . htmlspecialchars($stock['stock_ticker']) . ")</h2>";
                    echo "<p class='text-gray-500'>Latest Price: <span id='stock_price' class='text-green-700'>";
                    if (is_numeric($price)) {
                        echo "$" . number_format($price, 2);
                    } else {
                        echo htmlspecialchars($price);
                    }
                    echo "</span> on " . $price_date . "</p>";
                    echo "<p class='text-sm text-gray-500' id='last_updated'></p>";
                    echo "<div id='stock-actions' class='mt-4'>";
                    echo "<button id='buy-stock' class='btn-sm btn-primary'>Buy</button>";

                    $conn = getDB();

                    // Determine if the user has this stock in their portfolio
                    $user_id = $_SESSION['user_id'];
                    $query = "SELECT * FROM shares WHERE user_id = ? AND stock_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$user_id, $stockId]);
                    $holding = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($holding) {
                        $owned_shares = $holding['owned_shares'];
                        echo "<button id='sell-stock' class='btn-sm btn-secondary ml-2'>Sell</button>";
                    }

                    echo "</div>";
                    echo "<div id='form-messages' class='font-bold hidden mt-3'></div>";
                    echo "</div>";
                }
                ?>
                <script>
                    document.getElementById('buy-stock').addEventListener('click', function () {
                        modalManager.load("../modals/stocks/purchase_stock.php?stock_id=<?php echo $stockId; ?>", function () {
                            const quantityInput = document.getElementById("selectedQuantity");
                            const subtotalSpan = document.querySelector("#purchaseSummary #subtotal");
                            const orderFeeSpan = document.querySelector("#purchaseSummary .fee");
                            const totalSpan = document.querySelector("#purchaseSummary #total");
                            let cleanMoney = 0;

                            function formatNumber(num) {
                                return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }

                            // Fetch clean money and set balance
                            fetch(`../api/check_balance.php?user_id=<?php echo $_SESSION['user_id']; ?>`)
                                .then(response => response.json())
                                .then(data => {
                                    const cleanMoneySpan = document.querySelector("#purchaseSummary #clean");
                                    cleanMoney = parseFloat(data.clean_money.replace(/[$,]/g, ''));
                                    cleanMoneySpan.innerText = `$${formatNumber(cleanMoney)}`;
                                });

                            function updateSubtotal() {
                                const stockId = <?php echo json_encode($stockId); ?>;
                                const quantity = parseInt(quantityInput.value);
                                if (stockId && quantity > 0) {
                                    fetch(`../api/get_stock_data.php?stock_id=${stockId}`)
                                        .then(response => response.json())
                                        .then(data => {
                                            const price = parseFloat(data.latest_price);
                                            const subtotal = price * quantity;
                                            // Fetch order fee percentage
                                            fetch(`../api/get_stock_exchange_settings.php`)
                                                .then(response => response.json())
                                                .then(data => {
                                                    const orderFee = data.order_fee;
                                                    const orderFeeAmount = subtotal * (orderFee / 100);
                                                    const total = subtotal + orderFeeAmount;
                                                    subtotalSpan.innerText = `$${formatNumber(subtotal)}`;
                                                    orderFeeSpan.innerText = `${orderFee}%`;
                                                    totalSpan.innerText = `$${formatNumber(total)}`;
                                                    // Check if the total is greater than clean money
                                                    if (total > cleanMoney) {
                                                        totalSpan.classList.add("text-red-600");
                                                        orderFeeSpan.classList.add("text-red-600");
                                                        subtotalSpan.classList.add("text-red-600");
                                                    } else {
                                                        totalSpan.classList.remove("text-red-600");
                                                        orderFeeSpan.classList.remove("text-red-600");
                                                        subtotalSpan.classList.remove("text-red-600");
                                                    }

                                                    // Disable/enable the purchase button based on the total
                                                    const purchaseButton = document.getElementById("confirm-purchase");
                                                    if (total > cleanMoney) {
                                                        purchaseButton.disabled = true;
                                                        purchaseButton.classList.add("disabled");
                                                    } else {
                                                        purchaseButton.disabled = false;
                                                        purchaseButton.classList.remove("disabled");
                                                    }
                                                });
                                        });
                                } else {
                                    subtotalSpan.innerText = "$0.00";
                                    orderFeeSpan.innerText = "0%";
                                    totalSpan.innerText = "$0.00";
                                }
                            }

                            // Set max quantity to the maximum number of shares the user can buy (based on total including fee)
                            const maxButton = document.getElementById("max");
                            maxButton.addEventListener("click", () => {
                                const stockId = <?php echo json_encode($stockId); ?>;
                                if (stockId) {
                                    // Fetch stock price and order fee
                                    Promise.all([
                                        fetch(`../api/get_stock_data.php?stock_id=${stockId}`).then(res => res.json()),
                                        fetch(`../api/get_stock_exchange_settings.php`).then(res => res.json())
                                    ]).then(([stockData, settingsData]) => {
                                        const price = parseFloat(stockData.latest_price);
                                        const orderFee = parseFloat(settingsData.order_fee) / 100;
                                        // maxQuantity = floor(cleanMoney / (price * (1 + orderFee)))
                                        const maxQuantity = Math.floor(cleanMoney / (price * (1 + orderFee)));
                                        quantityInput.value = maxQuantity > 0 ? maxQuantity : 0;
                                        updateSubtotal();
                                    });
                                }
                            });

                            quantityInput.addEventListener("input", updateSubtotal);

                            // Initial update
                            updateSubtotal();

                            // Form submission
                            const confirmPurchaseBtn = document.getElementById("confirm-purchase");
                            confirmPurchaseBtn.addEventListener("click", function () {
                                const quantity = parseInt(quantityInput.value);
                                if (quantity > 0) {
                                    const stockId = <?php echo json_encode($stockId); ?>;
                                    const userId = <?php echo json_encode($_SESSION['user_id']); ?>;
                                    const orderFee = parseFloat(orderFeeSpan.innerText.replace("%", "")) / 100;
                                    const subtotal = parseFloat(subtotalSpan.innerText.replace(/[$,]/g, ''));
                                    const total = parseFloat(totalSpan.innerText.replace(/[$,]/g, ''));

                                    // Send the purchase request
                                    fetch("../api/buy_stock.php", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/x-www-form-urlencoded"
                                        },
                                        body: `stock_id=${encodeURIComponent(stockId)}&quantity=${encodeURIComponent(quantity)}`
                                    })
                                        .then(response => response.json())
                                        .then(data => {
                                            const formmessages = document.getElementById("form-messages");
                                            if (data.status == "success") {
                                                modalManager.close();

                                                formmessages.classList.remove("hidden");
                                                formmessages.classList.add("text-green-700");
                                                formmessages.innerText = `Successfully purchased ${quantity} share${quantity === 1 ? '' : 's'} of <?php echo getStockById($stockId)["stock_name"] ?> for $${formatNumber(total)}.`;
                                                // Wait 3 seconds and then hide the message
                                                setTimeout(() => {
                                                    formmessages.classList.add("hidden");
                                                }, 3000);
                                            } else {
                                                const modalMessages = document.getElementById("buy-form-messages");
                                                modalMessages.classList.remove("hidden");
                                                modalMessages.classList.add("text-red-700");
                                                modalMessages.innerText = `Error: ${data.message}`;
                                                // Wait 3 seconds and then hide the message
                                                setTimeout(() => {
                                                    modalMessages.classList.add("hidden");
                                                }, 3000);
                                            }
                                        });
                                } else {
                                    alert("Please enter a valid quantity.");
                                }
                            });
                        });
                    });

                    document.getElementById('sell-stock').addEventListener('click', function () {
                        modalManager.load("../modals/stocks/sell.php?stock_id=<?php echo $stockId; ?>", function () {
                            const quantityInput = document.getElementById("selectedQuantity");
                            const totalSpan = document.querySelector("#sellSummary #total");
                            const priceSpan = document.getElementById("price");
                            const sellFormMessages = document.getElementById("sell-form-messages");

                            function formatNumber(num) {
                                return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }

                            function updateSubtotal() {
                                const stockId = <?php echo json_encode($stockId); ?>;
                                const quantity = parseInt(quantityInput.value);
                                if (stockId && quantity > 0) {
                                    fetch(`../api/get_stock_data.php?stock_id=${stockId}`)
                                        .then(response => response.json())
                                        .then(data => {
                                            const price = parseFloat(data.latest_price);
                                            const subtotal = price * quantity;

                                            priceSpan.innerText = `$${formatNumber(price)}`;

                                            // Get owned shares
                                            fetch(`../api/get_user_portfolio.php?stock_id=${stockId}`)
                                                .then(response => response.json())
                                                .then(data => {
                                                    const ownedShares = data.reduce((sum, entry) => {
                                                        const shares = parseInt(entry.owned_shares);
                                                        console.log(`Adding ${shares} shares from`, entry);
                                                        return sum + shares;
                                                    }, 0);

                                                    if (ownedShares < quantity) {
                                                        totalSpan.classList.add("text-red-600");
                                                        sellFormMessages.classList.remove("hidden");
                                                        sellFormMessages.classList.add("text-red-700");
                                                        sellFormMessages.innerHTML = `<br>You cannot sell more shares than you own.`;
                                                    } else {
                                                        totalSpan.classList.remove("text-red-600");
                                                        sellFormMessages.classList.add("hidden");
                                                        sellFormMessages.innerHTML = "";
                                                        totalSpan.innerText = `$${formatNumber(subtotal)}`;
                                                    }
                                                });
                                        });
                                } else {
                                    subtotalSpan.innerText = "$0.00";
                                    totalSpan.innerText = "$0.00";
                                }
                            }

                            // Set max quantity to the maximum number of shares the user can sell
                            const maxButton = document.getElementById("max");
                            maxButton.addEventListener("click", () => {
                                // Get owned shares and set max quantity
                                fetch(`../api/get_user_portfolio.php?stock_id=<?php echo $stockId; ?>`)
                                    .then(response => response.json())
                                    .then(data => {
                                        const ownedShares = data.reduce((sum, entry) => {
                                            const shares = parseInt(entry.owned_shares);
                                            console.log(`Adding ${shares} shares from`, entry);
                                            return sum + shares;
                                        }, 0);
                                        quantityInput.value = ownedShares > 0 ? ownedShares : 0;
                                        updateSubtotal();
                                    });
                            });

                            quantityInput.addEventListener("input", updateSubtotal);

                            // Initial update
                            updateSubtotal();

                            // Form submission
                            const confirmSellBtn = document.getElementById("sell-confirm");
                            confirmSellBtn.addEventListener("click", function () {
                                const quantity = parseInt(quantityInput.value);
                                if (quantity > 0) {
                                    const stockId = <?php echo json_encode($stockId); ?>;
                                    // Send the sell request
                                    fetch("../api/sell_stock.php", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/x-www-form-urlencoded"
                                        },
                                        body: `stock_id=${encodeURIComponent(stockId)}&quantity=${encodeURIComponent(quantity)}`
                                    })
                                        .then(response => response.json())
                                        .then(data => {
                                            const formmessages = document.getElementById("form-messages");
                                            if (data.status == "success") {
                                                modalManager.close();

                                                formmessages.classList.remove("hidden");
                                                formmessages.classList.add("text-green-700");
                                                formmessages.innerText = `Successfully sold ${quantity} shares of <?php echo getStockById($stockId)["stock_name"] ?> for $${formatNumber(data.sell_value)}.`;
                                                // Wait 3 seconds and then hide the message
                                                setTimeout(() => {
                                                    formmessages.classList.add("hidden");
                                                }, 3000);
                                            } else {
                                                const modalMessages = document.getElementById("sell-form-messages");
                                                modalMessages.classList.remove("hidden");
                                                modalMessages.classList.add("text-red-700");
                                                modalMessages.innerText = `Error: ${data.message}`;
                                                // Wait 3 seconds and then hide the message
                                                setTimeout(() => {
                                                    modalMessages.classList.add("hidden");
                                                }, 3000);
                                            }
                                        });
                                } else {
                                    alert("Please enter a valid quantity.");
                                }
                            });
                        });
                    });
                </script>
                <div id="stock-history" class="w-full h-127"></div>
                <script>
                    // Get stock history data, limit to 30 days
                    const stockId = <?php echo json_encode($stockId); ?>;

                    const fetchStockHistory = async () => {
                        try {
                            const response = await fetch(`../api/get_stock_price_history.php?stock_id=${stockId}`);
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            const data = await response.json();
                            return data;
                        } catch (error) {
                            console.error('Error fetching stock history:', error);
                        }
                    };

                    const renderStockHistory = async () => {
                        const data = await fetchStockHistory();
                        if (data && data.length > 0) {
                            // Group prices by date (YYYY-MM-DD), take the latest price for each day
                            const grouped = {};
                            data.forEach(item => {
                                const day = item.date.split('T')[0];
                                grouped[day] = item.price;
                            });
                            const dates = Object.keys(grouped);
                            const prices = Object.values(grouped);

                            const trace = {
                                x: dates,
                                y: prices,
                                type: 'line',
                                connectgaps: true,
                                marker: { color: '#b70000' }
                            };

                            const layout = {
                                xaxis: { title: 'Date' },
                                yaxis: { title: 'Price ($)' }
                            };

                            Plotly.newPlot('stock-history', [trace], layout);

                            // Get the latest price
                            const latestPrice = prices[prices.length - 1];
                            const stockPriceElement = document.getElementById('stock_price');
                            if (stockPriceElement) {
                                const priceNum = Number(latestPrice);
                                stockPriceElement.textContent = (typeof priceNum === 'number' && !isNaN(priceNum))
                                    ? `$${priceNum.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
                                    : latestPrice;
                            }

                            // Show "Last updated X seconds ago" since last fetch
                            const lastUpdatedElement = document.getElementById('last_updated');
                            if (lastUpdatedElement) {
                                let secondsAgo = 0;
                                lastUpdatedElement.textContent = `Last updated: just now`;

                                // Clear any previous interval
                                if (window.lastUpdatedInterval) {
                                    clearInterval(window.lastUpdatedInterval);
                                }

                                window.lastUpdatedInterval = setInterval(() => {
                                    secondsAgo++;
                                    lastUpdatedElement.textContent = `Last updated: ${secondsAgo} second${secondsAgo === 1 ? '' : 's'} ago`;
                                }, 1000);
                            }
                        } else {
                            console.error('No stock history data available');
                        }
                    };

                    document.addEventListener('DOMContentLoaded', () => {
                        renderStockHistory();

                        // Update the stock price every 5 seconds
                        setInterval(async () => {
                            renderStockHistory();
                        }, 5000);
                    });
                </script>
            </div>
        </div>
        <div id="modal" class="modal">
            <div id="modal-content">
            </div>
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