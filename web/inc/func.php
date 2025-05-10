<?php
    function getDB() {
        static $conn = null;

        if ($conn === null) {
            $host = 'localhost';
            $db = 'fakefolio';
            $user = 'root';
            $pass = 'root';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];

            try {
                $conn = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                die("DB Error: " . $e->getMessage());
            }
        }

        return $conn;
    }

    function getUserById($userId) {
        $conn = getDB();

        $stmt = $conn->prepare("SELECT id, profile_picture_path, username, account_created, dirty_money, clean_money, credibility, risk FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            return $user; // Returns array of user data
        } else {
            return null; // User not found
        }
    }

    function getUserByUsername($username) {
        $conn = getDB();

        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return getUserById($user['id']); // Returns array of user data
        } else {
            return null; // User not found
        }
    }

    function getBalances($userId) {
        $conn = getDB();

        $stmt = $conn->prepare("SELECT dirty_money, clean_money FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Returns array of balances
    }

    function getUserInfo($userId) {
        $conn = getDB();

        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            return $user; // Returns array of user data
        } else {
            return null; // User not found
        }
    }

    // Retrieve cooldowns
    function getCooldowns($userId) {
        $conn = getDB();

        $stmt = $conn->prepare("SELECT * FROM system_cooldowns WHERE recipient_user_id = ?");
        $stmt->execute([$userId]);
        $cooldowns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($cooldowns) {
            return $cooldowns; // Returns array of cooldowns
        } else {
            return null; // No cooldowns found
        }
    }

    // Is Admin
    function isAdmin($userId) {
        $conn = getDB();

        $stmt = $conn->prepare("SELECT admin_rights FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user && $user['admin_rights'] == 1; // Returns true if user is admin, false otherwise
    }

    // Request email verification
    function requestEmailVerification($userId, $email) {
        $conn = getDB();

        // Check if email is already verified
        $stmt = $conn->prepare("SELECT verified FROM verification_codes WHERE requesting_email = ?");
        $stmt->execute([$email]);
        $verification = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($verification) {
            return false; // Email already verified
        } else {
            // Generate verification code
            $verificationCode = bin2hex(random_bytes(8));
            $stmt = $conn->prepare("INSERT INTO verification_codes (verification_code, requesting_email, associated_user) VALUES (?, ?, ?)");
            return $stmt->execute([$verificationCode, $email, $userId]); // Returns true on success, false on failure
        }
    }

    // Check if active email is verified
    function isEmailVerified($userId) {
        $conn = getDB();

        // Get email from user id
        $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return false; // User not found
        } else {
            $email = $user['email'];
        }

        $stmt = $conn->prepare("SELECT verified FROM verification_codes WHERE requesting_email = ?");
        $stmt->execute([$email]);
        $verification = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($verification) {
            return $verification['verified'] == 1; // Returns true if email is verified, false otherwise
        } else {
            return false;
        }
    }

    // Get users verified email(s)
    function getUserVerifiedEmails($userId) {
        $conn = getDB();

        $stmt = $conn->prepare("SELECT requesting_email FROM verification_codes WHERE associated_user = ? AND verified = 1");
        $stmt->execute([$userId]);
        $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $emails ? array_map(fn($email) => ['requesting_email' => $email], $emails) : []; // Returns array of associative arrays
    }

    // Image resize
    function resizeToSquare($filePath, $mimeType, $size)
    {
        switch ($mimeType) {
            case 'image/jpeg':
                $src = imagecreatefromjpeg($filePath);
                break;
            case 'image/png':
                $src = imagecreatefrompng($filePath);
                break;
            default:
                return false;
        }

        $width = imagesx($src);
        $height = imagesy($src);
        $min = min($width, $height);

        // Crop to square
        $srcCrop = imagecrop($src, [
            'x' => ($width - $min) / 2,
            'y' => ($height - $min) / 2,
            'width' => $min,
            'height' => $min
        ]);

        $dst = imagecreatetruecolor($size, $size);
        imagecopyresampled($dst, $srcCrop, 0, 0, 0, 0, $size, $size, $min, $min);

        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($dst, $filePath, 90);
                break;
            case 'image/png':
                imagepng($dst, $filePath);
                break;
        }

        imagedestroy($src);
        imagedestroy($srcCrop);
        imagedestroy($dst);
        return true;
    }

    // Update user profile
    function updateUserProfile($userId, $username, $email, $profilePicture) {
        $conn = getDB();

        // Check if username is already taken
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$username, $userId]);
        if ($stmt->rowCount() > 0) {
            return false; // Username already taken
        }

        // Update username and email in database
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        if ($stmt->execute([$username, $email, $userId])) {
            if (!empty($profilePicture['name'])) {
                // Handle profile picture upload
                $targetDir = "../_static/profile_pictures/";
                $targetFile = $targetDir . basename($profilePicture["name"]);
                move_uploaded_file($profilePicture["tmp_name"], $targetFile);
                resizeToSquare($targetFile, mime_content_type($targetFile), 200);
                // Update profile picture path in database
                $stmt = $conn->prepare("UPDATE users SET profile_picture_path = ? WHERE id = ?");
                return $stmt->execute([$targetFile, $userId]); // Returns true on success, false on failure
            }
            return true; // Profile updated successfully
        } else {
            return false; // Failed to update profile
        }
    }

    // Get user's social settings and return as array
    function getUserSocialSettings($userId) {
        $conn = getDB();

        $stmt = $conn->prepare("SELECT * FROM social_settings WHERE associated_user = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Returns associative array of social settings
    }

    // Reset user's social settings
    function resetUserSocialSettings($userId) {
        $conn = getDB();

        $stmt = $conn->prepare("UPDATE social_settings SET allow_messages = 0, allow_friend_requests = 0, allow_profile_wall_comments = 0 WHERE associated_user = ?");
        return $stmt->execute([$userId]); // Returns true on success, false on failure
    }

    // Turn on user's social settings
    function turnOnUserSocialSettings($userId) {
        $conn = getDB();

        $stmt = $conn->prepare("UPDATE social_settings SET allow_messages = 1, allow_friend_requests = 1, allow_profile_wall_comments = 1 WHERE associated_user = ?");
        return $stmt->execute([$userId]); // Returns true on success, false on failure
    }
?>