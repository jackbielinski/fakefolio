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
        <h2 class="font-bold">Send System Message</h2>
        <button id="close-modal" class="close" onclick="modalManager.close();">&times;</button>
    </div>
    <div class="modal-body">
        <form id="send-message-form" action="../api/admin/send_system_message.php" method="POST" enctype="multipart/form-data">
            <strong>Recipient</strong>
            <input type="text" id="target_user_id" name="target_user_id" placeholder="Enter username..." required><br>
            <strong>Title</strong>
            <input type="text" id="message_title" name="message_title" placeholder="Enter message title..." required><br>
            <strong>Message</strong>
            <textarea id="message_content" name="message_content" rows="4" placeholder="Enter message content..." required></textarea><br>
        </form>
        <div id="msg-preview" class="hidden">
            <h3 class="font-bold text-2xl">Message Preview</h3><br>
            <div id="msg-preview-content" class="profile-card-body">
                <strong id="msg-preview-title" class="text-2xl">Title</strong><br>
                <strong id="msg-preview-user" class="text-sm text-gray-400">User</strong><span id="msg-preview-user-value" class="ml-2"></span><br>
                <strong id="msg-preview-content-text" class="text-sm text-gray-400">Content</strong><span id="msg-preview-content-value" class="ml-2"></span><br>
            </div>
        </div>
    </div>
</div>