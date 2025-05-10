<?php
include "../inc/main.php";

if (!isset($_GET['user_id'])) {
    echo json_encode(['error' => 'User ID is required']);
    exit;
} else {
    $userId = $_GET['user_id'];

    $settings_enabled = getUserSocialSettings($userId)["settings_enabled"] ?? null;
    if ($settings_enabled == 0) {
        echo json_encode(['error' => 'User settings are disabled']);
        exit;
    }

    if (is_numeric($userId) && ($userId == $_SESSION['user_id'] || isAdmin($_SESSION['user_id']))) {
        $settingName = $_GET['setting_name'] ?? null;
        $settingValue = $_GET['setting_value'] ?? null;
    
        if (isset($settingName) && isset($settingValue)) {
            $allowedSettings = ['allow_messages', 'allow_friend_requests', 'allow_profile_wall_comments'];
            if (!in_array($settingName, $allowedSettings)) {
                echo json_encode(['error' => 'Invalid setting name']);
                exit;
            }

            $conn = getDB();
            $sql = "UPDATE social_settings SET `$settingName` = ? WHERE associated_user = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt && $stmt->execute([$settingValue, $userId])) {
                if ($stmt->rowCount() > 0) {
                    echo json_encode(['success' => 'User settings updated successfully']);
                } else {
                    echo json_encode(['error' => 'No changes made or user not found']);
                }
            } else {
                $errorInfo = $stmt ? $stmt->errorInfo() : $conn->errorInfo();
                echo json_encode(['error' => 'Failed to update user settings', 'details' => $errorInfo[2] ?? 'Unknown error']);
            }
        } else {
            echo json_encode(['error' => 'Setting name and value are required']);
        }
    } else {
        echo json_encode(['error' => 'Invalid user ID or insufficient permissions']);
    }
}
?>