<?php
    session_start();
    require_once "../../inc/func.php";

    if (!isset($_GET['user_id'])) {
        // Get username
        $username = $_GET['username'] ?? null;

        if ($username) {
            $user = getUserByUsername($username);
            if ($user) {
                $userId = $user['id'];
            } else {
                echo json_encode(["error" => "User not found."]);
                http_response_code(404);
                exit;
            }
        } else {
            echo json_encode(["error" => "User ID or username is required."]);
            http_response_code(400);
            exit;
        }
    } else {
        $userId = $_GET['user_id'];
    }

    if (!isAdmin($_SESSION["user_id"]) || ($_SESSION["user_id"] !== $userId)) {
        echo json_encode(["error" => "You are not authorized to access this resource."]);
        http_response_code(403);
        exit;
    } else {
        // Select everything from the users table
        $conn = getDB();

        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode($result);
            http_response_code(200);
        } else {
            echo json_encode(["error" => "User not found."]);
            http_response_code(404);
        }
    }
?>