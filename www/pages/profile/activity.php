<?php
    include '../../include/backend/main.php';

    $userParam = $_GET['u'] ?? null;
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 5;

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

    // Total count
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM user_activity WHERE sender_uuid = :user_id OR recipient_uuid = :user_id");
    $countStmt->execute(['user_id' => $profileUserId]);
    $totalItems = (int)$countStmt->fetchColumn();
    $totalPages = max(1, (int)ceil($totalItems / $perPage));

    if ($page > $totalPages) {
        $page = $totalPages;
    }

    $offset = ($page - 1) * $perPage;

    // Fetch paginated activities
    $stmt = $pdo->prepare("SELECT * FROM user_activity WHERE (sender_uuid = :user_id OR recipient_uuid = :user_id) AND transaction_type IN ('unknown','sent_money','buy_stock','sell_stock') ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':user_id', $profileUserId);
    $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($activities)) {
        echo "<p class='text-gray-400'>No recent activity.</p>";
    } else {
        echo "<ul class='space-y-2'>";
        foreach ($activities as $activity) {
            // Get both sender and recipient usernames and avatars
            $stmt = $pdo->prepare("SELECT username, avatar FROM users WHERE id = :uuid");
            $stmt->execute(['uuid' => $activity['sender_uuid']]);
            $sender = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($activity['recipient_uuid']) {
                $stmt = $pdo->prepare("SELECT username, avatar FROM users WHERE id = :uuid");
                $stmt->execute(['uuid' => $activity['recipient_uuid']]);
                $recipient = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $recipient = null;
            }

            // "Translate" activity types into human-readable text
            switch ($activity['transaction_type']) {
                case 'unknown':
                    $activity['transaction_type'] = "performed an action ";
                    break;
                case 'sent_money':
                    $activity['transaction_type'] = "sent money ";
                    break;
                case 'buy_stock':
                    $activity['transaction_type'] = "bought stocks ";
                    break;
                case 'sell_stock':
                    $activity['transaction_type'] = "sold stocks ";
                    break;
            }

            // Format time
            $formattedTime = date("F j, Y, g:i a T", strtotime($activity['created_at']));

        echo "<li class='p-3 border border-gray-300 bg-white'>";
        if ($recipient) {
            if ($activity['recipient_uuid'] === $_SESSION['user_id']) {
                // Activity where profile user is the recipient
                echo "<p class='text-sm'><img id='avatar' src='" . BASE_URL . "/_static/" . htmlspecialchars($sender['avatar']) . "' alt='" . htmlspecialchars($sender['username']) . "' class='inline-block w-6 h-6'> <a href='" . BASE_URL . "/@". urlencode($sender['username']) ."'><strong>" . htmlspecialchars($sender['username']) . "</strong></a> " . htmlspecialchars($activity['transaction_type']) . " to <img id='avatar' src='" . BASE_URL . "/_static/" . htmlspecialchars($recipient['avatar']) . "' alt='" . htmlspecialchars($sender['username']) . "' class='inline-block w-6 h-6'> <strong>" . htmlspecialchars($recipient['username']) . "</strong>.</p>";
            } else {
                // Activity where profile user is the sender
                echo "<p class='text-sm'><img id='avatar' src='" . BASE_URL . "/_static/" . htmlspecialchars($sender['avatar']) . "' alt='" . htmlspecialchars($sender['username']) . "' class='inline-block w-6 h-6'> <a href='" . BASE_URL . "/@" . urlencode($sender['username']) . "'><strong>" . htmlspecialchars($sender['username']) . "</strong></a> " . htmlspecialchars($activity['transaction_type']) . " to <img id='avatar' src='" . BASE_URL . "/_static/" . htmlspecialchars($recipient['avatar']) . "' alt='" . htmlspecialchars($recipient['username']) . "' class='inline-block w-6 h-6'> <a href='" . BASE_URL . "/@" . urlencode($recipient['username']) . "'><strong>" . htmlspecialchars($recipient['username']) . "</strong></a>.</p>";
            }
        }
        echo "</p><p class='text-lg my-2'><span class='balance clean my-2'><img src='" . BASE_URL . "/_static/icon/clean_money.png' alt='" . htmlspecialchars($activity['amount']) . "' class='inline-block align-middle w-4 h-4'>&nbsp;" . number_format($activity['amount'], 2) . "</span></p>";
        echo "<p class='text-xs text-gray-400 mt-2'>" . htmlspecialchars($formattedTime) . "</p>";
        echo "</li>";
    }
        echo "</ul>";
    }
    // Pagination links
    if ($totalPages > 1) {
        echo "<div class='mx-auto'>";
        echo "<div class='pagination flex items-center space-x-2 mt-4'>";

        if ($page > 1) {
            echo "<a href='/@{$userParam}/activity?page=" . ($page - 1) . "' data-page='" . ($page - 1) . "' class='px-3 py-1 border rounded'>Previous</a>";
        }

        echo "<span class='px-3 py-1'>Page {$page} of {$totalPages}</span>";

        if ($page < $totalPages) {
            echo "<a href='/@{$userParam}/activity?page=" . ($page + 1) . "' data-page='" . ($page + 1) . "' class='px-3 py-1 border rounded'>Next</a>";
        }

        echo "</div>";
        echo "</div>";
    }

?>