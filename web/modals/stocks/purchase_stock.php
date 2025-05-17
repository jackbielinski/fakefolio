<?php
include "../../inc/main.php";

$conn = getDB();

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
        <h2 class="font-bold">Buy Stock</h2>
        <button id="close-modal" class="close" onclick="modalManager.close();">&times;</button>
    </div>
    <div class="modal-body">
        <table class="w-full">
            <tr>
                <td>
                    <span>Stock</span>
                </td>
                <td>
                    <?php
                        if (isset($stock_id)) {
                            // Get stock info
                            $query = "SELECT * FROM stocks WHERE stock_id = ?";
                            $stmt = $conn->prepare($query);
                            $stmt->execute([$stock_id]);
                            $stock = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($stock) {
                                echo "<span class='text-green-700'>{$stock['stock_name']} (\${$stock['stock_ticker']})</span>";
                            } else {
                                echo "<span class='text-red-700'>Stock not found</span>";
                            }
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span>Share Quantity</span>
                </td>
                <td>
                    <input type="number" id="selectedQuantity" name="selectedQuantity" class="w-full" min="1" value="1">
                    <button id="max" class="btn-sm btn-primary">Set to max</button>
                </td>
            </tr>
        </table>
        <div id="purchaseSummary" class="mt-4">
            <div>
                <span id="subtotal" class="base-stat text-green-700">$0.00</span>&nbsp;<span
                    class="text-sm font-bold text-gray-400">SUBTOTAL</span>
                <div class="float-right align-bottom">
                    <span id="clean">$0.00</span><br>
                    <span class="text-sm font-bold text-gray-400 float-right">CLEAN MONEY</span>
                </div>
            </div>
            <div>
                <span class="fee">0%</span>&nbsp;<span class="text-sm font-bold text-gray-400">ORDER FEE</span>
            </div><br>
            <div>
                <span id="total" class="base-stat text-green-700">$0.00</span>&nbsp;<span
                    class="text-sm font-bold text-gray-400">TOTAL</span>
                <div class="float-right align-bottom">
                    <button type="submit" id="confirm-purchase" class="btn-sm btn-primary">Confirm Purchase</button>
                </div>
            </div>
        </div>
        <span id="buy-form-messages" class="hidden mt-3"></span>
    </div>
</div>