<?php
include "../include/backend/main.php";
include "../include/page_elements/errors.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Grab user data and see what changed. Do not count null fields. If null, keep existing value
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "error" => find_error_key("required")]);
        exit();
    } else {
        $userId = $_SESSION['user_id'];
    }

    $stmt = $pdo->prepare("SELECT timezone FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $timezone = trim($_POST['timezone']) ?: $user['timezone'];

    // Update user settings in the database
    $stmt = $pdo->prepare("UPDATE users SET timezone = :timezone WHERE id = :id");
    try {
        $stmt->execute([
            'timezone' => $timezone,
            'id' => $userId
        ]);
        echo json_encode(["success" => true]);
        exit();
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => find_error_key("database_error")]);
        exit();
    }
}
?>