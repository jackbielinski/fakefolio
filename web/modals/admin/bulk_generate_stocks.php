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
        <h2 class="font-bold">Bulk Generate Stocks</h2>
        <button id="close-modal" class="close" onclick="modalManager.close();">&times;</button>
    </div>
    <div class="modal-body">
        <form id="bulk-generate-form" method="POST">
            <table class="w-full table-auto">
                <tr>
                    <td>
                        <span>Number of Stocks</span><br>
                        <span class="text-gray-500 text-sm">Maximum of 20 stocks can be made at a time.</span>
                    </td>
                    <td>
                        <input type="number" id="num_stocks" name="num_stocks" placeholder="100" min="1" required>
                    </td>
                </tr>
                
            </table>
        </form>
        <button id="submit-form" class="btn-sm btn-primary">Submit</button>
        <strong id="form-messages" class="hidden mt-2"></strong><br>
    </div>
</div>