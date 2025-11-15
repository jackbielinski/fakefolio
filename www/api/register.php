<?php
    include "../include/backend/main.php";
    include "../include/page_elements/errors.php";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $terms = isset($_POST['terms']) ? true : false;

        // Input validation
        if (strlen($username) > 20) {
            echo json_encode(["success" => false, "error" => find_error_key("username_length")]);
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["success" => false, "error" => find_error_key("email_invalid")]);
            exit();
        }

        if (strlen($password) < 8) {
            echo json_encode(["success" => false, "error" => find_error_key("password_length")]);
            exit();
        }

        if (!$terms) {
            echo json_encode(["success" => false, "error" => find_error_key("terms_unchecked")]);
            exit();
        }

        // Check for existing username or email
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            // Determine which one is taken
            $stmt = $pdo->prepare("SELECT username, email FROM users WHERE username = :username OR email = :email");
            $stmt->execute(['username' => $username, 'email' => $email]);
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingUser['username'] === $username) {
                echo json_encode(["success" => false, "error" => find_error_key("username_taken")]);
                exit();
            } elseif ($existingUser['email'] === $email) {
                echo json_encode(["success" => false, "error" => find_error_key("email_taken")]);
                exit();
            }
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user into the database (PostgreSQL: use RETURNING to get inserted id)
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, created_at) VALUES (:username, :email, :password_hash, NOW()) RETURNING id");
    try {
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password_hash' => $hashedPassword
        ]);
        // Registration successful, log in the user
        $userId = $stmt->fetchColumn();
        if (!$userId) {
            throw new Exception("Failed to retrieve inserted ID");
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        echo json_encode(["success" => true]);
        exit();
    } catch (Exception $e) {
        error_log("Database insertion error: " . $e->getMessage());
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
        exit();
    }
}