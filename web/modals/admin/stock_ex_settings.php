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
        <h2 class="font-bold">Change Stock Exchange Settings</h2>
        <button id="close-modal" class="close" onclick="modalManager.close();">&times;</button>
    </div>
    <div class="modal-body">
        <form id="stock-ex-settings-form" method="POST">
            <table class="w-full table-auto">
                <tr>
                    <td>
                        <span>Order Fee</span>
                    </td>
                    <td>
                        <input type="number" id="order_fee" name="order_fee" placeholder="0.0" step="0.1" value="0.0" required>
                    </td>
                </tr>
            </table>
        </form>
        <button id="submit-form" class="btn-sm btn-primary">Submit</button>
        <strong id="form-messages" class="hidden mt-2"></strong><br>
    </div>
</div>