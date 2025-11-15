<?php
include dirname(__DIR__, 2) . '/include/backend/main.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/ff_dist.css">
    <title>Fakefolio</title>
</head>
<?php
// Fetch friends list

$stmt = $pdo->prepare("SELECT * FROM friends WHERE sender = :id OR receiver = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<body>
    <div id="body">
        <div class="container">
            <?php include dirname(__DIR__, 2) . '/include/page_elements/toolbar-header.php'; ?>
            <div>
                <h1 class="font-bold text-3xl">Add Friends</h1>
            </div>
            <div>
                <form id="add-friend-form" method="POST">
                    <div class="my-4 grid grid-cols-1 sm:grid-cols-2 grid-templates-rows-1 gap-3">
                        <input type="text" name="username" id="username" placeholder="Enter username" required>
                        <button type="submit" class="btn success">Add Friend</button>
                    </div>
                    <div id="message" class="hidden font-bold my-2"></div>
                </form>
            </div>
            <div class="mt-8">
                <!-- based on existing friends, show friends theyre friends with -->
                <h2 class="font-bold text-2xl">Friends You May Know</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-4">
                    <?php
                    // Fetch friends of friends
                    $stmt = $pdo->prepare("SELECT * FROM friends WHERE (sender = :id OR receiver = :id) AND (sender != :id AND receiver != :id)");
                    $stmt->execute(['id' => $_SESSION['user_id']]);
                    $friendsOfFriends = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($friendsOfFriends) > 0) {
                        foreach ($friendsOfFriends as $friend) {
                            $otherUserId = ($friend['sender'] == $_SESSION['user_id']) ? $friend['receiver'] : $friend['sender'];
                            $stmt = $pdo->prepare("SELECT id, username, display_name, avatar FROM users WHERE id = :id");
                            $stmt->execute(['id' => $otherUserId]);
                            $otherUser = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($otherUser) {
                                $avatar = !empty($otherUser['avatar']) ? $otherUser['avatar'] : 'placeholder.png';
                                $displayName = !empty($otherUser['display_name']) ? $otherUser['display_name'] : $otherUser['username'];

                                echo "<div class='border border-gray-400 bg-gray-100 p-2'>";
                                echo "<a href='" . BASE_URL . "/profile?u=" . htmlspecialchars($otherUser['username']) . "'>";
                                echo "<img src='" . BASE_URL . "/_static/" . htmlspecialchars($avatar) . "' alt='Avatar' width='40' id='avatar' class='inline-block align-baseline'/>";
                                echo "<div class='inline-block ml-2'>";
                                echo "<h2 class='font-bold text-xl'>" . htmlspecialchars($displayName) . "</h2>";
                                echo "<p class='text-gray-500'>@" . htmlspecialchars($otherUser['username']) . "</p>";
                                echo "</div>";
                                echo "</a>";
                                echo "</div>";
                            }
                        }
                    } else {
                        echo "<p class='text-gray-400'>No friends you may know found.</p>";
                    }
                    ?>
                </div>
            </div>
            <div>
                <h2 class="font-bold text-2xl mt-8">Featured</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-4">
                    <?php
                    // Fetch random users who are not friends with the current user
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE id != :id AND id NOT IN (SELECT receiver FROM friends WHERE sender = :id) AND id NOT IN (SELECT sender FROM friends WHERE receiver = :id) LIMIT 6");
                    $stmt->execute(['id' => $_SESSION['user_id']]);
                    $randomUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($randomUsers) > 0) {
                        foreach ($randomUsers as $user) {
                            $avatar = !empty($user['avatar']) ? $user['avatar'] : 'placeholder.png';
                            $displayName = !empty($user['display_name']) ? $user['display_name'] : $user['username'];

                            // limit display name to 13 chars and append ellipsis if truncated (multibyte-safe)
                            $max = 11;
                            if (mb_strlen($displayName, 'UTF-8') > $max) {
                                $displayName = mb_substr($displayName, 0, $max, 'UTF-8') . '…';
                            }

                            echo "<div class='border border-gray-400 bg-gray-100 p-2'>";
                            echo "<a href='" . BASE_URL . "/@" . htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') . "'>";
                            echo "<img src='" . BASE_URL . "/_static/" . htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') . "' alt='Avatar' width='40' id='avatar' class='inline-block align-baseline'/>";
                            echo "<div class='inline-block ml-2'>";
                            echo "<h2 class='font-bold text-xl'>" . htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') . "</h2>";
                            echo "<p class='text-gray-500'>@" . htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') . "</p>";
                            echo "</div>";
                            echo "</a>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p class='text-gray-400'>No featured users found.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
        <script src="<?= BASE_URL ?>/js/add_friends.js"></script>
        <div class="footer">
            <p>Fakefolio is a virtual game for entertainment only. All currency, stocks, and trades are fictional and
                have no
                real-world value.</p>
            <p>&copy; 2024 Fakefolio. All rights reserved.</p>
        </div>
    </div>
</body>

</html>