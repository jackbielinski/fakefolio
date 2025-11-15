<?php
header('Content-Type: application/json');

include "../include/backend/main.php";
include "../include/page_elements/errors.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_SESSION['user_id'];

    $activity = trim($_POST['activity']);
    $stmt = $pdo->prepare("INSERT INTO user_activity (user_id, transaction_type) VALUES (:user_id, :activity)");
    $stmt->execute(['user_id' => $user, 'activity' => $activity]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => find_error_key("database_error")]);
        exit();
    }
}
?>