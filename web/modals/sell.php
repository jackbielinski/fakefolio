<?php
    include "../inc/main.php";

    $active_user = getUserInfo($_SESSION['user_id']);

    if (!$active_user) {
        echo "<p>User not found.</p>";
        exit;
    }
?>
<div class="modal-content">
    <div class="modal-header">
        <h2 class="font-bold">Sell Product</h2>
        <button id="close-modal" class="close" onclick="modalManager.close();">&times;</button>
    </div>
    <div class="modal-body">
        <stron class="text-xl font-bold">Available from your inventory</strong>
        <strong id="form-messages" class="hidden mt-3"></strong>
    </div>
</div>