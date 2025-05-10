<?php
include "../inc/main.php";

if (!isset($_POST['user_id'])) {
    echo json_encode(['error' => 'User ID is required']);
    exit;
} else {
    $userId = $_POST['user_id'];

    // Require password
    $password = $_POST['password'] ?? null;
    
    // Match password with the one in the database
    $active_user = getUserInfo($userId);
    if (!$active_user) {
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    if (!password_verify($password, $active_user['password'])) {
        echo json_encode(['error' => 'Password is incorrect']);
        exit;
    }

    // Check if the user is an admin
    if (isAdmin($userId)) {
        echo json_encode(['error' => 'Admin accounts cannot be deleted until they are demoted']);
        exit;
    }

    // Delete account
    $conn = getDB();
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt && $stmt->execute([$userId])) {
        // Delete associated data
        $sql = "DELETE FROM social_settings WHERE associated_user = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userId]);

        // Delete associated cooldowns
        $sql = "DELETE FROM system_cooldowns WHERE recipient_user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userId]);

        // Delete associated verification codes
        $sql = "DELETE FROM verification_codes WHERE associated_user = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userId]);

        echo json_encode(['success' => 'Account deleted successfully']);
    } else {
        echo json_encode(['error' => 'Failed to delete account']);
    }
}
?>