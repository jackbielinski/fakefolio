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
        <table class="table-auto w-full">
            
        </table>
    </div>
</div>