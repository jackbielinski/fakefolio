<?php
    require_once "../inc/func.php";

    $userId = $_GET['user_id'];
    $user = getUserById($userId);

    if (!$user) {
        http_response_code(404);
        echo json_encode(["error" => "User not found"]);
        exit;
    } else {
        $credibility = $user['credibility'];
        $risk = $user['risk'];
    }

    $credibility_risk = [
        "credibility" => $credibility,
        "risk" => $risk
    ];

    echo json_encode($credibility_risk);
    http_response_code(200);
?>