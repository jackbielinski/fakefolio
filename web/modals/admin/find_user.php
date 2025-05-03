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
        <h2 class="font-bold">Find User</h2>
        <button id="close-modal" class="close" onclick="modalManager.close();">&times;</button>
    </div>
    <div class="modal-body">
        <form id="find-user-form" action="../api/admin/query_user.php" method="POST" enctype="multipart/form-data">
            <strong>Username</strong>
            <input type="text" id="search_username" name="search_username" placeholder="Enter username..." required>
        </form>
        <div id="search-results">
            <div id="searched-user-info" class="profile-card hidden">
                <h3 class="font-bold text-2xl">Search Results</h3><br>
                <img id="pfp" src="../_static/pfp/default.png" alt="Default" width="100" class="inline-block">
                <div id="queryResults" class="profile-card-body ml-2 inline-block align-top">
                    <strong id="username" class="text-2xl">username</strong><br>
                    <div id="data">
                        <strong id="user_id" class="text-sm text-gray-400">user id</strong><span id="user_id-value" class="ml-2"></span><br>
                        <strong id="email" class="text-sm text-gray-400">email</strong><span id="email-value" class="ml-2"></span><br>
                        <strong id="account_created" class="text-sm text-gray-400">account created</strong><span id="account_created-value" class="ml-2"></span><br>
                        <strong id="credibility" class="text-sm text-green-700">credibility</strong><span id="credibility-value" class="ml-2"></span><br>
                        <strong id="risk" class="text-sm text-red-700">risk</strong><span id="risk-value" class="ml-2"></span><br>
                        <strong id="clean_money" class="text-sm text-gray-400">clean money</strong><span id="clean-value" class="ml-2"></span><br>
                        <strong id="dirty_money" class="text-sm text-gray-400">dirty money</strong><span id="dirty-value" class="ml-2"></span><br>
                        <button id="action-view-btn" class="btn-sm btn-primary">View actions</button><br><br>
                    </div>
                </div><br><br>
                <p class="font-bold text-gray-400">Can't find what you're looking for? Try <a class="text-blue-500 underline hover:cursor-pointer hover:decoration-transparent hover:text-red-500" id="browse-db-button">browsing users</a> instead.</p>
            </div>
        </div><br>
        <button id="submit-form" class="btn-sm btn-primary">Submit</button><br>
        <strong id="form-messages" class="hidden mt-3"></strong>
    </div>
</div>