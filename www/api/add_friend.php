<?php
include '../include/backend/main.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';

    if ($username) {
        // Check if user exists
        $userCheck = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $userCheck->execute(['username' => $username]);
        $user = $userCheck->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            echo json_encode(['success' => false, 'error' => 'User does not exist.']);
            exit();
        }

        // Prevent adding oneself as a friend
        if ($username === $_SESSION['username']) {
            echo json_encode(['success' => false, 'error' => 'You cannot add yourself as a friend.']);
            exit();
        }

        // Check if user is already friends
        $friendCheck = $pdo->prepare("
    SELECT COUNT(*) FROM friends
    WHERE ((sender = :current_user AND receiver = :friend_user)
        OR (sender = :friend_user AND receiver = :current_user))
    AND friendship_status = 'friends'
");
        $friendCheck->execute([
            'current_user' => $_SESSION['user_id'],
            'friend_user' => $user['id']
        ]);
        $isFriends = $friendCheck->fetchColumn();

        if ($isFriends) {
            echo json_encode(['success' => false, 'error' => 'You are already friends with this user.']);
            exit();
        }

        // Check if there is already a pending friend request
        $requestCheck = $pdo->prepare("
    SELECT COUNT(*) FROM friends
    WHERE ((sender = :current_user AND receiver = :friend_user)
        OR (sender = :friend_user AND receiver = :current_user))
    AND friendship_status = 'pending'
");
        $requestCheck->execute([
            'current_user' => $_SESSION['user_id'],
            'friend_user' => $user['id']
        ]);
        $hasPendingRequest = $requestCheck->fetchColumn();

        if ($hasPendingRequest) {
            echo json_encode(['success' => false, 'error' => 'A friend request is already pending with this user.']);
            exit();
        }


        // Insert friend request into the database
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user) {
            // User exists, proceed with adding friend
            $stmt = $pdo->prepare("INSERT INTO friends (sender, receiver, friendship_status, friendship_created) VALUES (:sender, :receiver, :status, NOW())");
            $stmt->execute(['sender' => $_SESSION['user_id'], 'receiver' => $user['id'], 'status' => 'pending']);
            echo json_encode(['success' => true, 'user_added' => $username]);
        } else {
            echo json_encode(['success' => false, 'error' => 'User not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid username.']);
    }
}
?>