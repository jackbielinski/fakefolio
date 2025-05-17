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
                    <select id="selectedStock" name="stock" class="w-full">
                        <option value="">Select a stock</option>
                        <?php
                        $stocks = getStocks();

                        foreach ($stocks as $stock) {
                            $price = getStockPrice($stock['stock_id'])['price'];
                            $price = is_numeric($price) ? number_format($price, 2) : "N/A";

                            echo "<option value='{$stock['stock_id']}'>{$stock['stock_name']} (\${$price}/share)</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <span>Share Quantity</span>
                </td>
                <td>
                    <input type="number" id="selectedQuantity" name="selectedQuantity" class="w-full" min="1" value="1">
                </td>
            </tr>
        </table>
        <div id="purchaseSummary" class="mt-4">
            <div>
                <span id="subtotal" class="base-stat text-green-700">$0.00</span>&nbsp;<span
                    class="text-sm font-bold text-gray-400">SUBTOTAL</span>
                <div class="float-right align-bottom">
                    <span id="clean">$1,000.00</span><br>
                    <span class="text-sm font-bold text-gray-400 float-right">CLEAN MONEY</span>
                </div>
            </div>
            <div>
                <span class="fee">0%</span>&nbsp;<span class="text-sm font-bold text-gray-400">ORDER FEE</span>
            </div><br>
            <div>
                <span id="total" class="base-stat text-green-700">$0.00</span>&nbsp;<span class="text-sm font-bold text-gray-400">TOTAL</span>
                <div class="float-right align-bottom">
                    <button class="btn-sm btn-primary">Confirm Purchase</button>
                </div>
            </div>
        </div>
    </div>
</div>