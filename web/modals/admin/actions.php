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
        <h2 class="font-bold">Admin Actions for </h2>
        <button id="close-modal" class="close" onclick="modalManager.close();">&times;</button>
    </div>
    <div class="modal-body">
        <form id="admin-actions-form" action="../api/admin/actions.php" method="POST" enctype="multipart/form-data">
            <h1 class="font-bold text-3xl">Before you click...</h1>
            <p>Make sure you know what you're doing. This is a powerful tool and having access comes with great responsibility. If you're unsure of what to do, read a list of advice below or contact another admin.</p><br>
            <ul class="list-disc list-inside">
                <li>
                    <strong>Is the severity of the situation to a point where the user needs to be banned? </strong>
                    <span>Then ban the user. If the user did nothing wrong on-site (excluding personal conflict), then leave them alone.</span>
                </li>
                <li>
                    <strong>Don't know what to do? Loophole found in the rules?</strong>
                    <span>Then ban them. Go with your gut and back your decision with evidence if you're asked.</span>
                </li>
                <li>
                    <strong>Is the user being a nuisance? </strong>
                    <span>Maybe just a cooldown is enough. If the user consistently breaks the rules, then ban them.</span>
                </li>
            </ul><br>
            <div class="text-center">
                <strong class="text-xl">Select an action.</strong><br>
                <button id="ban-user" class="btn-sm btn-primary">Ban User</button>
                <button id="warn-user" class="btn-sm btn-primary">Set Cooldown</button>
                <button id="send-message" class="btn-sm btn-primary">Send System Message</button>
            </div>
        </form>
        <strong id="form-messages" class="hidden mt-3"></strong>
    </div>
</div>