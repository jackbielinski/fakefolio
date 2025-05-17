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
        <h2 class="font-bold">Create Stock</h2>
        <button id="close-modal" class="close" onclick="modalManager.close();">&times;</button>
    </div>
    <div class="modal-body">
        <form id="create-stock-form" method="POST">
            <table class="w-full table-auto">
                <tr>
                    <td>
                        <span>Stock Ticker</span><br>
                        <span class="text-gray-500 text-sm">e.g. STCK, RR<br>Must be less than 4 chars.</span>
                    </td>
                    <td>
                        <input type="text" id="stock_ticker" name="stock_ticker" placeholder="STCK" maxlength="4" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>Stock Name</span><br>
                        <span class="text-gray-500 text-sm">e.g. Stock Name</span>
                    </td>
                    <td>
                        <input type="text" id="stock_name" name="stock_name" placeholder="Stock Name" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>Starting Stock Price</span><br>
                        <span class="text-gray-500 text-sm">e.g. 100.00<br>By default, starting price is $2.00</span>
                    </td>
                    <td>
                        <input type="number" id="stock_price" name="stock_price" placeholder="100.00" step="0.01" value="2.00" required>
                    </td>
                </tr>
            </table>
        </form>
        <span id="form-messages" class="hidden mt-2"></span><br>
        <button id="submit-form" class="btn-sm btn-primary">Submit</button>
        <button id="generate-fields" class="btn-sm btn-secondary">Generate fields</button>
        <button id="reset-form" class="btn-sm btn-secondary">Reset</button>
        <button id="bulk-generate" class="btn-sm btn-secondary float-right">Bulk Generate</button>
        <strong id="form-messages" class="hidden"></strong>
    </div>
</div>