<?php
include "../inc/main.php";

if (!isset($_GET['user_id'])) {
    echo json_encode(['error' => 'User ID is required']);
    exit;
} else {
    $social_settings = getUserSocialSettings($_GET['user_id']);

    if ($social_settings) {
        echo json_encode($social_settings);
    } else {
        echo json_encode(['error' => 'User not found or no social settings available']);
    }
}
?>