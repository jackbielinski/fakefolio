<?php
    include "../inc/main.php";

    // Log in a user
    if (isset($_POST["submit"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];

        // Validate form
        if (empty($username) || empty($password)) {
            echo json_encode(["error" => "Please fill in all fields."]);
            http_response_code(400);
            return;
        }

        // Check if username exists
        $conn = getDB();

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(["error" => "Invalid username or password."]);
            http_response_code(401);
            return;
        } else {
            // Verify password
            if (password_verify($password, $user["password"])) {
                // Set session variables
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["logged_in"] = true;

                // Return success response
                echo json_encode(["success" => "Login successful."]);
                http_response_code(200);

                header("Location: ../src/home.php");
            } else {
                echo json_encode(["error" => "Invalid username or password."]);
                http_response_code(401);
            }
        }
    }
?>