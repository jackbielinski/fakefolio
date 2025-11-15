<?php
include dirname(__DIR__, 2) . '/include/backend/main.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ./login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/ff_dist.css">
    <title>Fakefolio</title>
</head>

<?php
// Get current user info
$stmt = $pdo->prepare("SELECT display_name, username, email, avatar, banner_img FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$displayName = htmlspecialchars($user['display_name']);
$username = htmlspecialchars($user['username']);
$email = htmlspecialchars($user['email']);
$avatar = htmlspecialchars($user['avatar']);
$banner = htmlspecialchars($user['banner_img']);
?>

<body>
    <div id="body">
        <div class="container">
            <?php include dirname(__DIR__, 2) . '/include/page_elements/toolbar-header.php'; ?>
            <div class="flex items-start">
                <img src="<?php echo BASE_URL; ?>/_static/<?= $avatar ?>" alt="Avatar" width="64">
                <div class="inline-block align-top ml-3 w-full">
                    <h1 class="font-bold text-2xl">Account Settings</h1>
                    <p>Update your account settings here. Looking for your <a href="<?= BASE_URL; ?>/profile/edit">profile settings</a>?</p>
                </div>
            </div>
            <div class="mt-4">
                <form id="edit-settings-form" method="post">
                <div class="grid gap-1">
                    <fieldset>
                        <legend>Account details</legend>
                        <div class="grid gap-4">
                            <div>
                                <label for="username" class="block font-medium mb-1">Username</label>
                                <strong><?= $username ?></strong>
                                <p class="text-gray-400 text-sm">Your unique username on the platform. You cannot change this.</p>
                            </div>
                            <div>
                                <label for="email" class="block font-medium mb-1">Email Address</label>
                                <div>
                                    <strong><?= $email ?></strong>
                                    <?php
                                        // Check if email is verified
                                        $stmt = $pdo->prepare("SELECT email FROM verified_emails WHERE id = :user_id AND email = :email");
                                        $stmt->execute(['user_id' => $_SESSION['user_id'], 'email' => $email]);
                                        $isVerified = $stmt->fetchColumn();

                                        if ($isVerified) {
                                            echo "<span class='text-green-800'><img src='" . BASE_URL . "/_static/icon/check_circle.png' alt='Verified' class='inline-block' width='16'> verified</span>";
                                        } else {
                                            echo "<span class='text-red-600'><img src='" . BASE_URL . "/_static/icon/danger.png' alt='Not Verified' class='inline-block' width='16'> not verified</span>";
                                        }
                                    ?>
                                </div>
                                <p class="text-gray-400 text-sm">We only send emails when it pertains to your account. Leaving your account unverified will make it unrecoverable by Fakefolio Support.</p>
                            </div>
                            <div>
                                <label for="avatar" class="block font-medium mb-1">Password</label>
                                <div>
                                    <button onclick="location.href='./change_password'" class="btn blue">Change Password</button>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Select your timezone</legend>
                        <p>Your timezone shows activity timestamps in your local time. Updates on next login.</p>
                        <?php
                        // get timezone from DB instead of session
                        $stmtTZ = $pdo->prepare("SELECT timezone FROM users WHERE id = :id");
                        $stmtTZ->execute(['id' => $_SESSION['user_id']]);
                        $timezone = $stmtTZ->fetchColumn() ?: 'UTC';
                        $timezone = htmlspecialchars($timezone, ENT_QUOTES, 'UTF-8');
                        ?>
                        <select class="mt-2" name="timezone" id="timezone">
                            <option value="UTC" <?= ($timezone === 'UTC') ? 'selected' : '' ?>>UTC</option>
                            <option value="America/New_York" <?= ($timezone === 'America/New_York') ? 'selected' : '' ?>>Eastern Time (US &amp; Canada)</option>
                            <option value="America/Chicago" <?= ($timezone === 'America/Chicago') ? 'selected' : '' ?>>Central Time (US &amp; Canada)</option>
                            <option value="America/Denver" <?= ($timezone === 'America/Denver') ? 'selected' : '' ?>>Mountain Time (US &amp; Canada)</option>
                            <option value="America/Los_Angeles" <?= ($timezone === 'America/Los_Angeles') ? 'selected' : '' ?>>Pacific Time (US &amp; Canada)</option>
                            <option value="Europe/London" <?= ($timezone === 'Europe/London') ? 'selected' : '' ?>>GMT (London, UK)</option>
                            <option value="Europe/Berlin" <?= ($timezone === 'Europe/Berlin') ? 'selected' : '' ?>>CET (Berlin, Germany)</option>
                            <option value="Asia/Kolkata" <?= ($timezone === 'Asia/Kolkata') ? 'selected' : '' ?>>IST (India Standard Time)</option>
                            <option value="Asia/Shanghai" <?= ($timezone === 'Asia/Shanghai') ? 'selected' : '' ?>>CST (China Standard Time)</option>
                            <option value="Asia/Tokyo" <?= ($timezone === 'Asia/Tokyo') ? 'selected' : '' ?>>JST (Japan Standard Time)</option>
                            <option value="Australia/Sydney" <?= ($timezone === 'Australia/Sydney') ? 'selected' : '' ?>>AEDT (Australian Eastern Daylight Time)</option>
                        </select>
                    </fieldset>
                    <fieldset>
                        <legend><img src="<?= BASE_URL; ?>/_static/icon/danger.png" alt="Danger" class="inline-block">&nbsp;<span>Danger zone</span></legend>
                        <p class="text-red-600"><strong class="uppercase">Warning:</strong> The actions in this section are irreversible. Please proceed with caution.</p>
                        <div class="mt-2">
                            <strong>Delete account</strong>
                            <p class="text-gray-400 text-sm">You may permanently delete your account and all of its associated data. If you would like to request your data, please contact support before proceeding.</p>
                            <button id="delete-account-btn" class="btn primary">Delete Account</button>
                        </div>
                    </fieldset>
                    <div>
                        <button id="save-settings-btn" class="btn success w-full sm:w-max">Save Settings</button>
                    </div>
                    <div id="message" class="hidden font-bold my-2"></div>
                </div>
            </div>
            </form>
        </div>
        <script src="<?= BASE_URL; ?>/js/edit_settings.js"></script>
        <div class="footer">
            <p>Fakefolio is a virtual game for entertainment only. All currency, stocks, and trades are fictional and
                have no
                real-world value.</p>
            <p>&copy; 2024 Fakefolio. All rights reserved.</p>
        </div>
    </div>
</body>

</html>