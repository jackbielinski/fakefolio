<?php
    include "../inc/main.php";

    // Find out if the verification code is valid
    $verificationCode = $_POST['verificationCode'] ?? null;
    $userId = $_POST['userId'] ?? null;

    if ($verificationCode && $userId) {
        $user = getUserInfo($userId);
        if ($user) {
            $email = $user['email'];

            $db = getDB();

            $stmt = $db->prepare("SELECT * FROM verification_codes WHERE requesting_email = :email");
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                $code = $result[0]['verification_code'];

                // Check if the verification code matches
                if ($verificationCode === $code) {
                    // Update verified to 1
                    $stmt = $db->prepare("UPDATE verification_codes SET verified = 1 WHERE requesting_email = :email");
                    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                    $stmt->execute();

                    // Unrestrict the user from set social settings
                    $stmt = $db->prepare("UPDATE social_settings SET allow_messages = 1, allow_friend_requests = 1, allow_profile_wall_comments = 1 WHERE associated_user = :userId");
                    $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
                    $stmt->execute();

                    echo json_encode(['success' => true, 'message' => 'E-mail verified successfully!']);
                    // 200 OK
                    http_response_code(200);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid verification code.']);
                    // 400 Bad Request
                    http_response_code(400);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No verification code found for this email.']);
                http_response_code(400);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
            http_response_code(400);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        http_response_code(400);
    }
?>