<?php
include "../../inc/main.php";

$active_user = getUserInfo($_SESSION['user_id']);

if (!$active_user) {
    echo "<p>User not found.</p>";
    exit;
}

if (isset($_GET['stock_id'])) {
    // Set the stock_id from the GET parameter
    $stock_id = $_GET['stock_id'];
} else {
    // If no stock_id is provided, redirect or show an error
    echo "<p>No stock ID provided.</p>";
    exit;
}
?>
<div class="modal-content">
    <div class="modal-header">
        <?php
        $stock = getStockById($stock_id);

        if ($stock) {
            echo "<h2 class='font-bold'>Sell " . htmlspecialchars($stock["stock_name"]) . " ($" . htmlspecialchars($stock["stock_ticker"]) . ")</h2>";
        } else {
            echo "<h2 class='font-bold'>Stock not found</h2>";
        }
        ?>
        <button id="close-modal" class="close" onclick="modalManager.close();">&times;</button>
    </div>
    <div class="modal-body">
        <table class="w-full">
            <tr>
                <td>
                    <span>Current Price</span>
                </td>
                <td>
                    <?php
                    $current_price = getStockPrice($stock_id)['price'];
                    if ($current_price !== false) {
                        echo "<span id='price' class='text-green-700 font-bold'>\$$current_price</span>";
                    } else {
                        echo "<span class='text-red-700 font-bold'>Price not available</span>";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span>Share Quantity</span><br>
                    <span class="text-gray-500 text-sm">You have: <?php echo getUserShares($active_user['id'], $stock_id)["total_shares"]; ?></span>
                </td>
                <td>
                    <input type="number" id="selectedQuantity" name="selectedQuantity" class="w-full" min="1" value="1">
                    <button id="max" class="btn-sm btn-primary">Set to max</button>
                </td>
            </tr>
        </table>
        <div id="sellSummary" class="text-lg mt-4">
            <span>You'll receive </span>
            <span id="total" class="text-green-700 font-bold">$0.00</span>
        </div><br>
        <button id="sell-confirm" class="btn-sm btn-secondary">Sell</button><br>
        <strong id="sell-form-messages" class="hidden mt-3"></strong>
    </div>
</div>