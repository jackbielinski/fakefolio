<?php
include "../../inc/main.php";

$active_user = getUserInfo($_SESSION['user_id']);

if (!$active_user) {
    echo "<p>User not found.</p>";
    exit;
}
?>
<div class="modal-content">
    <div class="modal-header">
        <h2 class="font-bold">My Holdings</h2>
        <button id="close-modal" class="close" onclick="modalManager.close();">&times;</button>
    </div>
    <div class="modal-body">
        <strong id="form-messages" class="hidden mt-3"></strong>
        <table class="stockstable w-full table-auto">
            <thead>
                <tr>
                    <th>Stock</th>
                    <th>Shares</th>
                    <th>Price</th>
                    <th>Total Value</th>
                </tr>
            </thead>
            <tbody id="holdings-table-body">
                <?php
                $conn = getDB();

                $query = "SELECT * FROM shares WHERE user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$active_user['id']]);
                $holdings = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($holdings) {
                    foreach ($holdings as $holding) {
                        $owned_shares = $holding['owned_shares'];
                        $stock_id = $holding['stock_id'];

                        // Get stock info
                        $query = "SELECT * FROM stocks WHERE stock_id = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->execute([$stock_id]);
                        $stock = $stmt->fetch(PDO::FETCH_ASSOC);
                        if (!$stock) {
                            continue; // Skip if stock not found
                        }
                        
                        // Get latest price
                        $query = "SELECT * FROM stock_prices WHERE stock_id = ? ORDER BY date DESC LIMIT 1";
                        $stmt = $conn->prepare($query);
                        $stmt->execute([$stock_id]);
                        $latest_price = $stmt->fetch(PDO::FETCH_ASSOC);

                        // Calculate total value
                        $total_value = $owned_shares * $latest_price['price'];

                        // Echo
                        echo "<tr>";
                        echo "<td><a href='javascript:void(0)' class='stock-link' data-ticker='{$stock['stock_ticker']}'>{$stock['stock_ticker']}</a></td>";
                        echo "<td>{$owned_shares}</td>";
                        echo "<td>{$latest_price['price']}</td>";
                        echo "<td>{$total_value}</td>";
                        echo "<td><button class='btn-sm btn-primary'>Sell</button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No holdings found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>