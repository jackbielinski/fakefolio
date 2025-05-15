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
        <h2 class="font-bold">Delete Account</h2>
        <button id="close-modal" class="close" onclick="modalManager.close();">&times;</button>
    </div>
    <div class="modal-body">
        <strong>Are you sure you want to delete your account?</strong>
        <span>This action cannot be undone and you will lose all of your data, friends, and account settings. You can always create a new account for a fresh start or if you change your mind. <strong>Support will not be able to retrieve your account and data after you delete it.</strong></span><br><br>
        <form id="delete-account-form" method="POST">
            <table class="table-auto w-full">
                <tr>
                    <td style="padding-right: 10px;">
                        <strong>Enter your password</strong>
                    </td>
                    <td>
                        <input type="password" id="current-password" name="current-password" required>
                    </td>
                </tr>
            </table><br>
            <button id="delete-account-submit" type="submit" class="btn-sm btn-primary">Delete Account</button>
            <span id="delete-form-msgs" class="ml-2 font-bold text-red-600 hidden"></span>
        </form>
    </div>
</div>