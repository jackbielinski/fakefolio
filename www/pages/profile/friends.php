<?php
include dirname(__DIR__, 2) . '/include/backend/main.php';

$userParam = $_GET['u'] ?? null;

if ($userParam) {
    // Lookup user by username
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute(['username' => $userParam]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userData) {
        $profileUserId = $userData['id'];
    } else {
        echo "<p class='text-gray-400'>User not found.</p>";
        exit();
    }
} else {
    $profileUserId = $_SESSION['user_id'];
}

$stmt = $pdo->prepare("SELECT * FROM friends WHERE (sender = :id OR receiver = :id) AND friendship_status = 'friends'");
$stmt->execute(['id' => $profileUserId]);
$friendsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($friendsList) === 0) {
    echo "<p class='text-gray-400'>No friends to display.</p>";
    exit();
}

echo "<div class='grid grid-cols-3 md:grid-cols-12 gap-4 mt-4'>";

$viewerId = $_SESSION['user_id'] ?? null;

// Collect other user IDs from friends
$otherIds = [];
foreach ($friendsList as $friend) {
    $otherUserId = ($friend['sender'] == $profileUserId) ? $friend['receiver'] : $friend['sender'];
    if ($otherUserId == $profileUserId)
        continue;
    $otherIds[$otherUserId] = true;
}

$stmtUser = $pdo->prepare("SELECT id, username, display_name, avatar FROM users WHERE id = :id");

// If viewer is not on their own profile and is friends with the profile user, show viewer first with "(you)"
if ($viewerId && $viewerId != $profileUserId && isset($otherIds[$viewerId])) {
    $stmtUser->execute(['id' => $viewerId]);
    $viewer = $stmtUser->fetch(PDO::FETCH_ASSOC);
    if ($viewer) {
        $avatar = !empty($viewer['avatar']) ? $viewer['avatar'] : 'placeholder.png';
        $displayName = !empty($viewer['display_name']) ? $viewer['display_name'] : $viewer['username'];

        echo "<a href='" . BASE_URL . "/profile?u=" . htmlspecialchars($viewer['username']) . "'>";
        echo "<div class='tooltip-container'>";
        echo "<img src='" . BASE_URL . "/_static/" . htmlspecialchars($avatar) . "' alt='" . htmlspecialchars($displayName) . "' class='w-1/2 sm:w-max mx-auto' id='avatar'/>";
        echo "<p class='tooltip font-bold'>@" . htmlspecialchars($viewer['username']) . " <span class='text-orange-400'>(You)</span></p>";
        echo "</div>";
        echo "</a>";
    }
    unset($otherIds[$viewerId]);
}

// Render remaining friends
foreach (array_keys($otherIds) as $otherId) {
    $stmtUser->execute(['id' => $otherId]);
    $otherUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($otherUser) {
        $avatar = !empty($otherUser['avatar']) ? $otherUser['avatar'] : 'placeholder.png';
        $displayName = !empty($otherUser['display_name']) ? $otherUser['display_name'] : $otherUser['username'];

        echo "<a href='" . BASE_URL . "/profile?u=" . htmlspecialchars($otherUser['username']) . "'>";
        echo "<div class='tooltip-container'>";
        echo "<img src='" . BASE_URL . "/_static/" . htmlspecialchars($avatar) . "' alt='" . htmlspecialchars($displayName) . "' class='w-1/2 sm:w-max mx-auto' id='avatar'/>";
        echo "<p class='tooltip font-bold'>@" . htmlspecialchars($otherUser['username']) . "<span></span></p>";
        echo "</div>";
        echo "</a>";
    }
}

echo "</div>";
?>