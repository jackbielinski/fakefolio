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
        <h2 class="font-bold">Edit Profile</h2>
        <button id="close-modal" class="close" onclick="modalManager.close();">&times;</button>
    </div>
    <div class="modal-body">
        <form id="edit-profile-form" method="POST" enctype="multipart/form-data">
            <table class="table-auto w-full">
                <tr>
                    <td style="padding-right: 10px;">
                        <label for="username">Username (20 chars)</label><br>
                        <small id="username_change_msg" class="font-bold text-gray-400">You can only change your username once every year.</small>
                    </td>
                    <td>
                        <?php
                            // Disable the input if the user has a cooldown for username change
                            $cooldowns = getCooldowns($active_user['id']);
                            $is_username_change_disabled = false;
                            if ($cooldowns) {
                                foreach ($cooldowns as $cooldown) {
                                    if ($cooldown['type'] === 'username_change') {
                                        $is_username_change_disabled = true;
                                        $expiration_date = date('F d, Y \a\t g:i a T', strtotime($cooldown['expiration_date']));
                                        break;
                                    }
                                }
                            }
                            $username_value = htmlspecialchars($active_user['username']);

                            echo '<input type="text" id="username" name="username" value="' . $username_value . '" ' . ($is_username_change_disabled ? 'readonly' : '') . ' required>';
                            if ($is_username_change_disabled) {
                                echo '<span id="username_change_msg" class="text-sm font-bold text-gray-400">You have already changed your username. You may change it again on ' . $expiration_date . '</span>';
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding-right: 10px;"><label for="email">E-mail</label></td>
                    <?php
                        $cooldowns = getCooldowns($active_user['id']);
                        $is_email_change_disabled = false;
                        if ($cooldowns) {
                            foreach ($cooldowns as $cooldown) {
                                if ($cooldown['type'] === 'email_change') {
                                    $is_email_change_disabled = true;
                                    $expiration_date = date('F d, Y \a\t g:i a T', strtotime($cooldown['expiration_date']));
                                    break;
                                }
                            }
                        }
                        $email_value = htmlspecialchars($active_user['email']);
                        echo '<td><input type="email" id="email" name="email" value="' . $email_value . '" ' . ($is_email_change_disabled ? 'readonly' : '') . ' required>';
                        if ($is_email_change_disabled) {
                            echo '<span id="email_change_msg" class="text-sm font-bold text-gray-400">You have already changed your email. You may change it again on ' . $expiration_date . '</span>';
                        }
                    ?>
                </tr>
                <tr>
                    <td style="padding-right: 10px;"><label for="profile_picture">Profile Picture</label><br><small class="font-bold text-gray-400">Your profile picture cannot be any larger than 1MB.</small></td>
                    <td>
                        <img class="inline-block" id="profile_picture_prev" src="../_static/<?php echo !empty($active_user['profile_picture_path']) ? htmlspecialchars($active_user['profile_picture_path']) : 'default_profile.png'; ?>?v=<?php echo time(); ?>" alt="Profile Picture" width="75">
                        <input class="inline-block" type="file" id="profile_picture" name="profile_picture">
                    </td>
                </tr>
            </table>
        </form>
        <br>
        <button id="save-changes" type="submit" class="btn-sm btn-primary">Save Changes</button><br>
        <strong id="form-messages" class="hidden mt-3"></strong>
    </div>
</div>