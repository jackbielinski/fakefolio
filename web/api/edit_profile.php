<?php
    require_once "../inc/func.php";

    session_start();

    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(["error" => "Unauthorized"]);
        exit;
    } else {
        $userId = $_SESSION['user_id'];
        $user = getUserById($userId);

        // get form data
        $username = $_POST['username'] ?? null;
        $profilePicture = $_FILES['profile_picture'] ?? null;
        $email = $_POST['email'] ?? null;

        $errors = [];
        $success = false;

        // Check if username is valid
        if ($username && !preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            $errors[] = "Invalid username format. Only alphanumeric characters and underscores are allowed.";
        } else {
            // Check if username is already taken
            $existingUser = getUserByUsername($username);
            if ($existingUser && $existingUser['id'] !== $userId) {
                $errors[] = "Username already taken.";
            } else {
                // Check if username is changed
                if ($user['username'] !== $username) {
                    // Check if username change is allowed (once a year)
                    $cooldowns = getCooldowns($userId);
                    if ($cooldowns) {
                        foreach ($cooldowns as $cooldown) {
                            if ($cooldown['type'] === 'username_change') {
                                $errors[] = "You can only change your username once every year. You have to wait until " . date('Y-m-d H:i:s', strtotime($cooldown['expiration_date'])) . ".";
                                break;
                            }
                        }
                    } else {
                        // Update username in database
                        $conn = getDB();
                        $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
                        $stmt->execute([$username, $userId]);

                        $success = true;

                        if ($success && $stmt->rowCount() > 0) {
                            // Set cooldown for username change
                            $stmt = $conn->prepare("INSERT INTO system_cooldowns (recipient_user_id, type, expiration_date) VALUES (?, 'username_change', DATE_ADD(NOW(), INTERVAL 1 YEAR))");
                            $stmt->execute([$userId]);
                        }
                    }
                }
            }
        }

        // Check if email is valid, and if it is changed
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        } else {
            $conn = getDB();
            // Check if email is already taken
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->rowCount() > 0) {
                $errors[] = "Email already taken.";
            } else {
                // Update email in database
                $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
                $stmt->execute([$email, $userId]);
                $success = true;
            }
        }

        // Check if profile picture is valid and not too large
        if ($profilePicture) {
            $maxFileSize = 1024 * 1024; // 1MB
            $allowedTypes = ['image/jpeg', 'image/png'];

            if ($profilePicture['size'] > $maxFileSize) {
            $errors[] = "Profile picture is too large. Maximum size is 1MB.";
            } elseif (!in_array($profilePicture['type'], $allowedTypes)) {
            $errors[] = "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
            } else {
            // Move uploaded file to the desired directory
            $folder = "pfp/";
            $targetDir = "../_static/" . $folder;
            // Rename the file to User ID only + file extension
            $fileExtension = pathinfo($profilePicture['name'], PATHINFO_EXTENSION);
            $fileName = $userId . "." . $fileExtension;
            $targetFilePath = $targetDir . $fileName;

            // Remove any existing files with the same user ID but different extensions
            $existingFiles = glob($targetDir . $userId . ".*");
            foreach ($existingFiles as $existingFile) {
                if ($existingFile !== $targetFilePath) {
                    unlink($existingFile);
                }
            }

            $dbFilePath = $folder . $fileName; // Store the path in the database
            if (move_uploaded_file($profilePicture['tmp_name'], $targetFilePath)) {
                // Update profile picture path in database
                $stmt = $conn->prepare("UPDATE users SET profile_picture_path = ? WHERE id = ?");
                $stmt->execute([$dbFilePath, $userId]);
                $success = true;
            } else {
                $errors[] = "Failed to upload profile picture.";
            }
            }
        }

        // Return response
        if ($success) {
            echo json_encode(["success" => "Profile updated successfully."]);
        } else {
            echo json_encode(["errors" => $errors]);
        }
    }
?>