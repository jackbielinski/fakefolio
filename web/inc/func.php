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
            $stmt = $conn->prepare("INSERT INTO verification_codes (verification_code, requesting_email) VALUES (?, ?)");
            return $stmt->execute([$verificationCode, $email]); // Returns true on success, false on failure
        }
    }
?>