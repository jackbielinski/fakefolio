<?php
    require_once "../inc/func.php";

    $user_id = $_GET['user_id'];
    $user = getUserById($user_id);

    if ($user) {
        $response = [
            'id' => $user_id,
            'username' => $user['username'],
            'profile_picture_path' => $user['profile_picture_path'],
            'account_created' => $user['account_created'],
            'dirty_money' => $user['dirty_money'],
            'clean_money' => $user['clean_money'],
            'credibility' => $user['credibility'],
            'risk' => $user['risk']
        ];
        echo json_encode($response);
        http_response_code(200);
    } else {
        echo json_encode(['error' => 'User not found']);
        http_response_code(404);
    }
?>