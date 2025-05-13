<?php
include "../inc/main.php";

$conn = getDB();

// Register a new user
$username = $_POST["username"];
$password = $_POST["password"];
$email = $_POST["email"];

// Validate form
if (empty($username) || empty($password) || empty($email)) {
    echo json_encode(["error" => "Please fill in all fields."]);
    http_response_code(400);
    return;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => "Invalid email format."]);
    http_response_code(400);
    return;
}
if (strlen($password) < 8) {
    echo json_encode(["error" => "Password must be at least 8 characters long."]);
    http_response_code(400);
    return;
}
if (strlen($username) > 20) {
    echo json_encode(["error" => "Username must be 20 characters long."]);
    http_response_code(400);
    return;
}

// Validate username
if (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
    echo json_encode(["error" => "Username can only contain letters, numbers, and underscores."]);
    http_response_code(400);
    return;
}

// Check if username or email already exists using prepared statements
$stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
$stmt->bindValue(":username", $username);
$stmt->bindValue(":email", $email);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($result) > 0) {
    echo json_encode(["error" => "Username or E-mail already exists."]);
    http_response_code(400);
    return;
} else {
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password", $hashed_password);
    // Removed invalid bind_param call
    if ($stmt->execute()) {
        // Get the last inserted user ID
        $userId = $conn->lastInsertId();
        // Insert into social_settings
        $stmt = $conn->prepare("INSERT INTO social_settings (associated_user) VALUES (:user_id)");
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        // Set session variables
        $_SESSION["user_id"] = $userId;

        // Request email verification
        if (requestEmailVerification($userId, $email)) {
            echo json_encode(["success" => "User registered successfully."]);
            http_response_code(200);

            // redirect to the verification page
            header("Location: ../verify");
        }
    } else {
        echo json_encode(["error" => "Error registering user."]);
        http_response_code(500);
        exit;
    }

    $stmt = null;
}
?>