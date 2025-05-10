<?php
    require_once "../inc/func.php";

    session_start();
    $conn = getDB();

    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(["error" => "Unauthorized"]);
        exit;
    } else {
        $userId = $_SESSION['user_id'];
        $user = getUserById($userId);

        // get form data
        $username = $_POST['username'];
        $profilePicture = $_FILES['profile_picture'];
        $email = $_POST['email'];

        $errors = [];
        $success = false;

        if ($username && trim($username) !== '' && $username !== $user['username']) {
            // Only now do we validate and check cooldown

            if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
                $errors[] = "Invalid username format. Only alphanumeric characters and underscores are allowed.";
            } else {
                $existingUser = getUserByUsername($username);
                if ($existingUser && $existingUser['id'] !== $userId) {
                    $errors[] = "Username already taken.";
                } else {
                    // Only check cooldown if username is changing
                    $cooldowns = getCooldowns($userId);
                    $hasCooldown = false;
                    foreach ($cooldowns as $cooldown) {
                        if ($cooldown['type'] === 'username_change') {
                            $hasCooldown = true;
                            $errors[] = "You can only change your username once every year. You have to wait until " . date('Y-m-d H:i:s', strtotime($cooldown['expiration_date'])) . ".";
                            break;
                        }
                    }

                    if (!$hasCooldown) {
                        $conn = getDB();
                        $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
                        $stmt->execute([$username, $userId]);

                        if ($stmt->rowCount() > 0) {
                            $stmt = $conn->prepare("INSERT INTO system_cooldowns (recipient_user_id, type, expiration_date) VALUES (?, 'username_change', DATE_ADD(NOW(), INTERVAL 30 DAY))");
                            $stmt->execute([$userId]);
                            $success = true;
                        }
                    }
                }
            }
        } else {
            // Username didn't change, skip validation and cooldown
            $success = true;
        }

        // Check if email is valid, and if it is changed
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        } else {
            // Check if email is already taken
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->rowCount() > 0) {
                $errors[] = "Email already taken.";
            } else {
                // Check for cooldown for email change
                $cooldowns = getCooldowns($userId);
                $hasEmailCooldown = false;
                foreach ($cooldowns as $cooldown) {
                    if ($cooldown['type'] === 'email_change') {
                        $hasEmailCooldown = true;
                        $errors[] = "You can only change your username once every year. You have to wait until " . date('Y-m-d H:i:s', strtotime($cooldown['expiration_date'])) . ".";
                        break;
                    }
                }

                if (!$hasEmailCooldown) {
                    // Update email in database
                    $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
                    $stmt->execute([$email, $userId]);
                    $success = true;

                    if ($success && $stmt->rowCount() > 0) {
                        // Set 30 day cooldown for email change
                        $stmt = $conn->prepare("INSERT INTO system_cooldowns (recipient_user_id, type, expiration_date) VALUES (?, 'email_change', DATE_ADD(NOW(), INTERVAL 7 DAY))");
                        $stmt->execute([$userId]);
                    }
                }

                // Check if the email is verified
                $stmt = $conn->prepare("SELECT verified FROM verification_codes WHERE requesting_email = ?");
                $stmt->execute([$email]);
                $verification = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$verification) {
                    $stmt = $conn->prepare("UPDATE social_settings SET settings_enabled = 0 WHERE associated_user = ?");
                    $stmt->execute([$userId]);

                    requestEmailVerification($userId, $email);
                } else {
                    turnOnUserSocialSettings($userId);
                }
            }
        }

        $pfpChanged = false;

        if (!empty($profilePicture['name'])) {
            $maxFileSize = 1024 * 1024;
            $allowedTypes = ['image/jpeg', 'image/png'];
            if ($profilePicture['size'] > $maxFileSize) {
                $errors[] = "Profile picture is too large. Max 1MB.";
            } elseif (!in_array($profilePicture['type'], $allowedTypes)) {
                $errors[] = "Invalid file type. Only JPEG and PNG allowed.";
            } else {
                $folder = "pfp/";
                $targetDir = "../_static/" . $folder;
                $fileExtension = pathinfo($profilePicture['name'], PATHINFO_EXTENSION);
                $fileName = $userId . "." . $fileExtension;
                $targetFilePath = $targetDir . $fileName;

                // Delete previous files with same user ID
                $existingFiles = glob($targetDir . $userId . ".*");
                foreach ($existingFiles as $existingFile) {
                    unlink($existingFile);
                }

                if (move_uploaded_file($profilePicture['tmp_name'], $targetFilePath)) {
                    // Resize image to square
                    resizeToSquare($targetFilePath, $profilePicture['type'], 256);

                    $dbFilePath = $folder . $fileName;
                    $stmt = $conn->prepare("UPDATE users SET profile_picture_path = ? WHERE id = ?");
                    $stmt->execute([$dbFilePath, $userId]);
                    $pfpChanged = true;
                } else {
                    $errors[] = "Failed to upload profile picture.";
                }
            }
        }

        // Return response
        if ($success) {
        $response = [];
            if ($success) {
                $response['success'] = "Profile updated successfully.";
            }
            if (!empty($errors)) {
                $response['errors'] = $errors;
            }
            echo json_encode($response);
        } else {
            echo json_encode(["errors" => $errors]);
        }
    }
?>