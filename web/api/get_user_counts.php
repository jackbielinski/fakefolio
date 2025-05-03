<?php
    require_once "../inc/func.php";

    // Count all users
    $conn = getDB();
    $stmt = $conn->prepare("SELECT COUNT(*) as total_users FROM users");
    $stmt->execute();
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];
    // Count all admins
    $stmt = $conn->prepare("SELECT COUNT(*) as total_admins FROM users WHERE admin_rights = 1");
    $stmt->execute();
    $totalAdmins = $stmt->fetch(PDO::FETCH_ASSOC)['total_admins'];

    // Return the counts as a JSON response
    echo json_encode([
        "users" => $totalUsers,
        "admins" => $totalAdmins
    ]);
    http_response_code(200);
?>