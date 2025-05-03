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
            <div id="header">
                <img src="../_static/fakefolio_wordmark_white.png" alt="Fakefolio" width="150">
                <div id="nav">
                    <ul>
                        <li><a href="#">Log In</a></li>
                        <li><a href="#">Register</a></li>
                    </ul>
                </div>
            </div><br>
            <div class="inline-block">
                <img class="inline-block" src="../_static/icons/stocks.png" alt="Stocks" width="40">
                <strong class="text-3xl inline-block align-middle ml-1">Stock Exchange</strong>
            </div>
            <div id='balance-bar' class='inline-block float-right'>
                <div id="clean-money">
                    <span id="clean">$10,000</span><br>
                    <span id="subtitle">CLEAN MONEY</span>
                </div>
            </div><br><br>
            <div id="content">
                <div id="stock-table" class="overflow-x-auto">
                    <table class="table-auto w-full text-left border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border border-gray-300 px-4 py-2">Stock</th>
                                <th class="border border-gray-300 px-4 py-2">Price</th>
                                <th class="border border-gray-300 px-4 py-2">Change</th>
                                <th class="border border-gray-300 px-4 py-2">Volume</th>
                            </tr>
                        </thead>
                        <tbody id="stock-data">
                            <!-- Stock data will be populated here -->
                            <?php
                            $stocks = [
                                ['name' => 'Apple', 'price' => 150.25, 'change' => '+1.5%', 'volume' => '1.2M'],
                                ['name' => 'Google', 'price' => 2800.50, 'change' => '-0.8%', 'volume' => '900K'],
                                ['name' => 'Amazon', 'price' => 3450.75, 'change' => '+2.1%', 'volume' => '1.5M'],
                                ['name' => 'Tesla', 'price' => 720.10, 'change' => '+0.5%', 'volume' => '2.3M']
                            ];

                            foreach ($stocks as $stock) {
                                echo "<tr>";
                                echo "<td class='border border-gray-300 px-4 py-2'>{$stock['name']}</td>";
                                echo "<td class='border border-gray-300 px-4 py-2'>\${$stock['price']}</td>";
                                echo "<td class='border border-gray-300 px-4 py-2'>{$stock['change']}</td>";
                                echo "<td class='border border-gray-300 px-4 py-2'>{$stock['volume']}</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
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