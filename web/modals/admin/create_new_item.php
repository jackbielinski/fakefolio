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
        <h2 class="font-bold">Create New Item</h2>
        <button id="close-modal" class="close" onclick="modalManager.close();">&times;</button>
    </div>
    <div class="modal-body">
            <table class="table-auto w-full">
                <tr>
                    <td style="padding-right: 10px;">
                        <label for="item_id">Item Author</label>
                    </td>
                    <td>
                        <?php
                            // Get the user's info
                            $user = getUserInfo($_SESSION['user_id']);

                            $username = htmlspecialchars($user['username']);
                            $profile_picture_path = htmlspecialchars($user['profile_picture_path']);

                            // Display the user's profile picture and username
                            echo '<img class="inline-block" id="profile_picture_prev" src="../_static/' . (!empty($profile_picture_path) ? $profile_picture_path : 'default_profile.png') . '" alt="Profile Picture" width="45">';
                            echo '<strong class="inline-block ml-2">' . $username . '</strong>';
                        ?>
                        <br><small>For bookkeeping purposes, we log the author of items.<br>Your username will not be displayed on items you create.</small>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="item_name">Item Name</label><br>
                        <small class="font-bold text-gray-400">The name of the item.</small>
                    </td>
                    <td>
                        <input type="text" id="item_name" name="item_name" placeholder="Enter item name..." required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="item_description">Item Description</label><br>
                        <small class="font-bold text-gray-400">A brief description of the item.</small>
                    </td>
                    <td>
                        <input type="text" id="item_description" name="item_description" placeholder="Enter item description..." required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="item_price">Item Type</label><br>
                        <small class="font-bold text-gray-400">The type of item.</small>
                    </td>
                    <td>
                        <select id="item_type" name="item_type" required>
                            <option value="usb">USB</option>
                            <option value="badge">Profile Badge</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="item_price">Item Price</label><br>
                        <small class="font-bold text-gray-400">The price of the item.</small>
                    </td>
                    <td>
                        <input type="number" id="item_price" name="item_price" placeholder="Example: 69420.69" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="item_image">Item Image</label><br>
                        <small class="font-bold text-gray-400">The image of the item.</small>
                    </td>
                    <td>
                        <input type="file" id="item_image" name="item_image" accept="image/*" required>
                        <div id="preview-container" class="hidden"><br><br>
                            <img id="item_image_preview" src="../_static/item_images/default.png" alt="Item Image Preview" width="100" class="hidden">
                            <span id="item_image_error" class="text-red-500 hidden">Invalid image format. Please select a valid image file.</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="item_stock">Item Stock</label><br>
                        <small class="font-bold text-gray-400">Pick how many copies of the item you want to make.<br>You can always make more later.</small>
                    </td>
                    <td>
                        <input type="number" id="item_stock" name="item_stock" placeholder="Enter item stock..." required>
                    </td>
                </tr><br>
                <tr>
                    <td colspan="2" class="text-center">
                        <button id="create-item-btn" class="btn-sm btn-primary">Create Item</button>
                    </td>
                </tr>
            </table>
        <strong id="form-messages" class="hidden mt-3"></strong>
    </div>
</div>