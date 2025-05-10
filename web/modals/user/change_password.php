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
        <h2 class="font-bold">Change Password</h2>
        <button id="close-modal" class="close" onclick="modalManager.close();">&times;</button>
    </div>
    <div class="modal-body">
        <form method="post">
            <table class="table-auto w-full">
                <tr>
                    <td style="padding-right: 10px;">
                        <label for="current-password">Current Password</label>
                    </td>
                    <td>
                        <input type="password" id="current-password" name="current-password" required>
                    </td>
                </tr>
                <tr>
                    <td style="padding-right: 10px;">
                        <label for="new-password">New Password</label>
                    </td>
                    <td>
                        <input type="password" id="new-password" name="new-password" required>
                    </td>
                </tr>
                <tr>
                    <td style="padding-right: 10px;">
                        <label for="confirm-new-password">Confirm New Password</label>
                    </td>
                    <td>
                        <input type="password" id="confirm-new-password" name="confirm-new-password" required>
                    </td>
                </tr>
            </table>
        </form>
        <br>
        <button id="change-password-submit" type="submit" class="btn-sm btn-primary">Change Password</button>
        <strong id="change-password-form-messages" class="hidden font-bold ml-1"></strong>
    </div>
</div>