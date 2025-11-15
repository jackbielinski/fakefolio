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

        // Delete the pending friend request
        $deleteRequest = $pdo->prepare("
            DELETE FROM friends
            WHERE sender = :current_user AND receiver = :friend_user AND friendship_status = 'pending'
        ");
        $deleteRequest->execute([
            'current_user' => $_SESSION['user_id'],
            'friend_user' => $user['id']
        ]);

        echo json_encode(['success' => true, 'user_unadded' => $username]);
        exit();
    } else {
        echo json_encode(['success' => false, 'error' => 'Username is required.']);
        exit();
    }
}
?>