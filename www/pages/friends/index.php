<?php
include dirname(__DIR__, 2) . '/include/backend/main.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/ff_dist.css">
    <title>Fakefolio</title>
</head>
<?php
// Fetch friends list

$stmt = $pdo->prepare("SELECT * FROM friends WHERE (sender = :id OR receiver = :id) AND friendship_status = 'friends'");
$stmt->execute(['id' => $_SESSION['user_id']]);
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<body>
    <div id="body">
        <div class="container">
            <?php include dirname(__DIR__, 2) . '/include/page_elements/toolbar-header.php'; ?>
            <div>
                <a href="<?= BASE_URL ?>/friends/add" class="btn success float-right">Add Friend</a>
                <h1 class="font-bold text-3xl">Friends (<?= count($friends) ?>)</h1>
            </div>
            <div id="message" class="my-3"></div>
            <div class="mb-8">
                <?php if (count($friends) > 0):
                    echo "<div class='grid sm:grid-cols-2 md:grid-cols-3 gap-4 mt-4'>";
                    // Display friends list
                    foreach ($friends as $friend) {
                        // get user info for other user
                        $otherUserId = ($friend['sender'] == $_SESSION['user_id']) ? $friend['receiver'] : $friend['sender'];
                        $stmt = $pdo->prepare("SELECT id, username, display_name, avatar FROM users WHERE id = :id");
                        $stmt->execute(['id' => $otherUserId]);

                        $otherUser = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($otherUser) {
                            $avatar = !empty($otherUser['avatar']) ? $otherUser['avatar'] : 'placeholder.png';
                            $displayName = !empty($otherUser['display_name']) ? $otherUser['display_name'] : $otherUser['username'];

                            echo "<a href='" . BASE_URL . "/profile?u=" . htmlspecialchars($otherUser['username']) . "'>";
                            echo "<div class='border border-gray-400 bg-gray-100 p-2'>";
                            echo "<img src='" . BASE_URL . "/_static/" . htmlspecialchars($avatar) . "' alt='Avatar' width='40' id='avatar' class='inline-block align-baseline'/>";
                            echo "<div class='inline-block ml-2'>";
                            $maxDisplay = 13;
                            $maxUsername = 15;

                            // Truncate display name safely (multibyte-aware) and then escape
                            $displayRaw = $displayName;
                            if (mb_strlen($displayRaw) > $maxDisplay) {
                                $displayShort = htmlspecialchars(mb_substr($displayRaw, 0, $maxDisplay - 1) . '…');
                            } else {
                                $displayShort = htmlspecialchars($displayRaw);
                            }

                            // Truncate username safely and then escape
                            $usernameRaw = $otherUser['username'];
                            if (mb_strlen($usernameRaw) > $maxUsername) {
                                $usernameShort = htmlspecialchars(mb_substr($usernameRaw, 0, $maxUsername - 1) . '…');
                            } else {
                                $usernameShort = htmlspecialchars($usernameRaw);
                            }

                            echo "<h2 class='font-bold text-xl'>" . $displayShort . "</h2>";
                            echo "<p class='text-gray-500'>@" . $usernameShort . "</p>";
                            echo "</div>";
                            echo "</div>";
                            echo "</a>";
                        }
                    }
                    echo "</div>";
                else: ?>
                    <div>
                        <p class="text-center m-16 text-gray-400">You have no friends added. <a
                                href="<?= BASE_URL ?>/friends/add">Make some!</a></p>
                    </div>
                <?php endif; ?>
            </div>
            <div>
                <h1 class="font-bold text-3xl">Pending Friend Requests</h1>
                <div>
                    <?php
                    // Fetch pending friend requests
                    $stmt = $pdo->prepare("SELECT * FROM friends WHERE receiver = :id AND friendship_status = 'pending'");
                    $stmt->execute(['id' => $_SESSION['user_id']]);
                    $pendingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $maxDisplay = 13;
                    $maxUsername = 15;

                    if (count($pendingRequests) > 0) {
                        echo "<div class='grid sm:grid-cols-2 md:grid-cols-3 gap-4 mt-4'>";
                        foreach ($pendingRequests as $request) {
                            // get user info for receiver
                            $sender = $request['sender'];
                            $stmt = $pdo->prepare("SELECT id, username, display_name, avatar FROM users WHERE id = :id");
                            $stmt->execute(['id' => $sender]);

                            $senderUser = $stmt->fetch(PDO::FETCH_ASSOC);

                            // Trim display name and username
                            $displayRaw = $senderUser['display_name'];
                            if (mb_strlen($displayRaw) > $maxDisplay) {
                                $displayShort = htmlspecialchars(mb_substr($displayRaw, 0, $maxDisplay - 1) . '…');
                            } else {
                                $displayShort = htmlspecialchars($displayRaw);
                            }

                            $usernameRaw = $senderUser['username'];
                            if (mb_strlen($usernameRaw) > $maxUsername) {
                                $usernameShort = htmlspecialchars(mb_substr($usernameRaw, 0, $maxUsername - 1) . '…');
                            } else {
                                $usernameShort = htmlspecialchars($usernameRaw);
                            }

                            if ($senderUser) {
                                $avatar = !empty($senderUser['avatar']) ? $senderUser['avatar'] : 'placeholder.png';
                                $displayName = !empty($senderUser['display_name']) ? $senderUser['display_name'] : $senderUser['username'];

                                echo "<a href='" . BASE_URL . "/profile?u=" . htmlspecialchars($senderUser['username']) . "'>";
                                echo "<div class='border border-gray-400 bg-gray-100 p-2'>";
                                echo "<img src='" . BASE_URL . "/_static/" . htmlspecialchars($avatar) . "' alt='Avatar' width='32' id='avatar' class='inline-block align-top'/>";
                                echo "<div class='inline-block ml-2'>";
                                echo "<h2 class='font-bold text-xl'>" . htmlspecialchars($displayName) . "</h2>";
                                echo "<p class='text-gray-500'>@" . htmlspecialchars($senderUser['username']) . "</p>";
                                echo "</div>";
                                // accept/decline buttons (equal width)
                                echo "<div class='mt-2' style='display:flex;gap:0.5rem;'>";
                                echo "<button id='accept-request?" . htmlspecialchars($senderUser['username']) . "' class='btn-sm success' style='flex:1;'>Accept</button>";
                                echo "<button id='decline-request?" . htmlspecialchars($senderUser['username']) . "' class='btn-sm danger' style='flex:1;'>Decline</button>";
                                echo "</div>";
                                echo "</div>";
                                echo "</a>";
                            }
                        }
                        echo "</div>";
                    } else {
                        echo "<div><p class='text-gray-400 text-center my-16'>No pending friend requests.</p></div>";
                    }
                    ?>
                </div>
            </div>
            <div class="my-8">
                <h1 class="font-bold text-3xl">Outgoing Friend Requests</h1>
                <?php
                // Fetch outgoing friend requests
                $stmt = $pdo->prepare("SELECT * FROM friends WHERE sender = :id AND friendship_status = 'pending'");
                $stmt->execute(['id' => $_SESSION['user_id']]);
                $outgoingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $maxDisplay = 13;
                $maxUsername = 15;

                if (count($outgoingRequests) > 0) {
                    echo "<div class='grid sm:grid-cols-2 md:grid-cols-3 gap-4 mt-4'>";
                    foreach ($outgoingRequests as $request) {
                        // get user info for receiver
                        $receiverId = $request['receiver'];
                        $stmt = $pdo->prepare("SELECT id, username, display_name, avatar FROM users WHERE id = :id");
                        $stmt->execute(['id' => $receiverId]);

                        $receiverUser = $stmt->fetch(PDO::FETCH_ASSOC);

                        // Trim display name and username
                        $displayRaw = $receiverUser['display_name'];
                        if (mb_strlen($displayRaw) > $maxDisplay) {
                            $displayShort = htmlspecialchars(mb_substr($displayRaw, 0, $maxDisplay - 1) . '…');
                        } else {
                            $displayShort = htmlspecialchars($displayRaw);
                        }

                        $usernameRaw = $receiverUser['username'];
                        if (mb_strlen($usernameRaw) > $maxUsername) {
                            $usernameShort = htmlspecialchars(mb_substr($usernameRaw, 0, $maxUsername - 1) . '…');
                        } else {
                            $usernameShort = htmlspecialchars($usernameRaw);
                        }

                        if ($receiverUser) {
                            $avatar = !empty($receiverUser['avatar']) ? $receiverUser['avatar'] : 'placeholder.png';
                            $displayName = !empty($receiverUser['display_name']) ? $receiverUser['display_name'] : $receiverUser['username'];

                            echo "<div class='border border-gray-400 bg-gray-100 p-2'>";
                            echo "<img src='" . BASE_URL . "/_static/" . htmlspecialchars($avatar) . "' alt='Avatar' width='32' id='avatar' class='inline-block align-top'/>";
                            echo "<a href='" . BASE_URL . "/profile?u=" . htmlspecialchars($receiverUser['username']) . "'>";
                            echo "<div class='inline-block ml-2'>";
                            echo "<h2 class='font-bold text-xl'>" . htmlspecialchars($displayName) . "</h2>";
                            echo "<p class='text-gray-500'>@" . htmlspecialchars($receiverUser['username']) . "</p>";
                            echo "</div>";
                            echo "</a>";
                            echo "<div><button id='cancel-request?" . $receiverUser['username'] . "' class='btn-sm warning'>Cancel Request</button></div>";
                            echo "</div>";
                        }
                    }
                    echo "</div>";
                } else {
                    echo "<div><p class='text-gray-400 text-center my-16'>No outgoing friend requests.</p></div>";
                }
                ?>
            </div>
        </div>
        <script src="./js/friends_index.js"></script>
        <div class="footer">
            <p>Fakefolio is a virtual game for entertainment only. All currency, stocks, and trades are fictional and
                have no
                real-world value.</p>
            <p>&copy; 2024 Fakefolio. All rights reserved.</p>
        </div>
    </div>
</body>

</html>