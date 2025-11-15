<?php
header('Content-Type: application/json');

include "../include/backend/main.php";
include "../include/page_elements/errors.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_email = trim($_POST['username_email']);
    $password = $_POST['password'];

    if (filter_var($username_email, FILTER_VALIDATE_EMAIL)) {
        $email = $username_email;
        $username = null;
    } else {
        $username = $username_email;
        $email = null;
    }

    if ($username && strlen($username) > 20) {
        echo json_encode(["success" => false, "error" => find_error_key("username_length")]);
        exit();
    }

    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "error" => find_error_key("email_invalid")]);
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
    $stmt->execute(['username' => $username, 'email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
        $stmt->execute(['id' => $user['id']]);
        echo json_encode(["success" => true, "user" => $user]);
        exit();
    } else {
        echo json_encode(["success" => false, "error" => find_error_key("invalid_credentials")]);
        exit();
    }
}
?>