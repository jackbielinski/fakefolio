<?php
include "../inc/main.php";

$newPassword = $_POST['new-password'] ?? null;
$currentPassword = $_POST['current-password'] ?? null;

if (!$newPassword || !$currentPassword) {
    echo json_encode(['error' => 'Both current and new passwords are required']);
    exit;
} else if (strlen($newPassword) < 8) {
    echo json_encode(['error' => 'New password must be at least 8 characters long']);
    exit;
} else if ($newPassword !== $_POST['confirm-new-password']) {
    echo json_encode(['error' => 'New passwords do not match']);
    exit;
} else if (!is_numeric($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Invalid user ID']);
    exit;
}

// Insert the new password into the database
$active_user = getUserInfo($_SESSION['user_id']);

if (!$active_user) {
    echo json_encode(['error' => 'User not found']);
    exit;
}

if (password_verify($currentPassword, $active_user['password'])) {
    $conn = getDB();
    $sql = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt && $stmt->execute([password_hash($newPassword, PASSWORD_BCRYPT), $_SESSION['user_id']])) {
        echo json_encode(['success' => 'Password changed successfully']);
    } else {
        echo json_encode(['error' => 'Failed to change password']);
    }
} else {
    echo json_encode(['error' => 'Current password is incorrect']);
}

?>