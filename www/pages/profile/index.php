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
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/ff_dist.css">
    <title>Fakefolio</title>
</head>

<body>
    <div id="body">
        <div class="container">
            <?php include dirname(__DIR__, 2) . '/include/page_elements/toolbar-header.php'; ?>
            <div class="flex items-start">
                <img src="<?= BASE_URL ?>/_static/icon/unknown.jpg" alt="Avatar" id="avatar" width="100">
                <div class="inline-block align-top ml-4 w-full">
                    <?php
                    $userParam = $_GET['u'] ?? null;

                    if ($userParam) {
                        // Lookup user by username
                        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
                        $stmt->execute(['username' => $userParam]);
                        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($userData) {
                            $profileUserId = $userData['id']; // <- use a separate variable
                        } else {
                            echo "<h1 class='font-bold text-2xl md:text-4xl'>User not found</h1>";
                            echo "<p class='text-gray-400 text-sm'>@" . htmlspecialchars($userParam) . "</p>";
                            echo "<p class='mt-2'>The user you are looking for does not exist.</p>";
                            echo "<p class='mt-4'><a href='./home' class='btn primary w-full sm:w-max'>Go to Home</a></p>";
                            exit();
                        }
                    } else {
                        $profileUserId = $_SESSION['user_id']; // viewing own profile
                    }

                    $stmt = $pdo->prepare("SELECT id, avatar, username, display_name, dirty_money, clean_money, last_login FROM users WHERE id = :id");
                    $stmt->execute(['id' => $profileUserId]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    $avatar = !empty($user['avatar']) ? $user['avatar'] : 'placeholder.png';
                    echo "<script>document.getElementById('avatar').src = '" . BASE_URL . "/_static/" . htmlspecialchars($avatar) . "';</script>";

                    $displayName = !empty($user['display_name']) ? $user['display_name'] : $user['username'];

                    if ($user['id'] === $_SESSION['user_id']) {
                        echo "<a href='" . BASE_URL . "/profile/edit' class='btn warning w-full flex items-center text-3xl sm:w-max sm:float-right'><span class='inline-block text-xl sm:text-sm'><img class='inline-block mr-2' src='" . BASE_URL . "/_static/icon/pencil_white.png' alt='Pencil'/>Edit Profile</span></a>";
                    } else {
                        // Check if already friends
                        $stmt = $pdo->prepare("SELECT * FROM friends WHERE ((sender = :user1 AND receiver = :user2) OR (sender = :user2 AND receiver = :user1)) AND friendship_status = 'friends'");
                        $stmt->execute(['user1' => $_SESSION['user_id'], 'user2' => $profileUserId]);
                        $isFriend = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($isFriend) {
                            echo "<button onclick='removeFriend(\"" . htmlspecialchars($user['username']) . "\")' class='btn primary w-full flex items-center text-3xl sm:w-max sm:float-right'><span class='inline-block text-xl sm:text-sm'><img class='inline-block mr-2' src='" . BASE_URL . "/_static/icon/user_delete.png' alt='Delete Friend'/>Unfriend</span></button>";
                            echo "<p class='text-orange-500 text-sm font-bold'><img class='inline-block align-middle mr-1' src='" . BASE_URL . "/_static/icon/friends.png' alt='Friends' width='16' height='16'/>Friends</p>";
                        } else {
                            // Check if a friend request is pending
                            $stmt = $pdo->prepare("SELECT * FROM friends WHERE ((sender = :user1 AND receiver = :user2) OR (sender = :user2 AND receiver = :user1)) AND friendship_status = 'pending'");
                            $stmt->execute(['user1' => $_SESSION['user_id'], 'user2' => $profileUserId]);
                            $isPending = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($isPending) {
                                echo "<button onclick='cancelPendingRequest(\"" . htmlspecialchars($user['username']) . "\")' class='btn warning w-full flex items-center text-3xl sm:w-max sm:float-right'><span class='inline-block text-xl sm:text-sm'><img class='inline-block mr-2' src='" . BASE_URL . "/_static/icon/user_delete.png' alt='Cancel Request'/>Cancel Request</span></button>";
                            } else {
                                echo "<button onclick='addFriend(\"" . htmlspecialchars($user['username']) . "\")' class='btn success w-full flex items-center text-3xl sm:w-max sm:float-right'><span class='inline-block text-xl sm:text-sm'><img class='inline-block mr-2' src='" . BASE_URL . "/_static/icon/user_add.png' alt='Add Friend'/>Add Friend</span></button>";
                            }
                        }
                    }

                    // if last activity was less than 5 minutes ago, set $online to true
                    $onlineStmt = $pdo->prepare("SELECT last_active FROM users WHERE id = :id");
                    $onlineStmt->execute(['id' => $profileUserId]);
                    $last_activity = $onlineStmt->fetchColumn();

                    // Check if last activity was within the last 5 minutes (300 seconds)
                    $last_activity_timestamp = strtotime($last_activity);
                    if ($last_activity_timestamp === false) {
                        $last_activity_timestamp = 0;
                    }
                    $current_time = time();
                    $online = ($current_time - $last_activity_timestamp) <= 300;

                    $last_seen = date("M d, Y g:i a T", $last_activity_timestamp);

                    if (!$online) {
                        $online_indicator = "<div class='tooltip-container'><img class='inline-block align-middle' src='" . BASE_URL . "/_static/icon/offline.png' width='14' height='14'/><div class='tooltip'><span>Last seen: " . $last_seen . "</span></div></div>";
                    } else {
                        $online_indicator = "<div class='tooltip-container'><img class='inline-block align-middle' src='" . BASE_URL . "/_static/icon/online.png' width='14' height='14'/><div class='tooltip'><span>Last seen: " . $last_seen . "</span></div></div>";
                    }

                    echo "<h1 class='font-bold text-xl md:text-4xl break-all md:break-normal'>" . htmlspecialchars($displayName) . "&nbsp;" . $online_indicator . "</h1>";
                    echo "<p class='text-gray-400 text-sm'>@" . htmlspecialchars($user['username']) . "</p>";

                    $dirtyMoney = number_format($user['dirty_money'], 2);
                    $cleanMoney = number_format($user['clean_money'], 2);

                    echo "<p class='mt-2 inline-block'><img class='inline-block' src='" . BASE_URL . "/_static/icon/clean_money.png' alt='Clean Money'/>&nbsp;<span class='balance clean'>$" . $cleanMoney . "</span>&nbsp;<span class='text-gray-400'>clean money</span></p>&nbsp;&nbsp;";
                    echo "<p class='mt-1 inline-block'><img class='inline-block' src='" . BASE_URL . "/_static/icon/dirty_money.png' alt='Dirty Money'/>&nbsp;<span class='balance dirty'>$" . $dirtyMoney . "</span>&nbsp;<span class='text-gray-400'>dirty money</span></p>";
                    ?>
                </div>
            </div>
            <?php
            // Include profile tabs
            include dirname(__DIR__, 2) . '/include/page_elements/profile_tabs.php';
            ?>
        </div>
        <script src="<?= BASE_URL ?>/js/friends.js"></script>
        <div class="footer">
            <p>Fakefolio is a virtual game for entertainment only. All currency, stocks, and trades are fictional and
                have no
                real-world value.</p>
            <p>&copy; 2024 Fakefolio. All rights reserved.</p>
        </div>
    </div>
</body>

</html>