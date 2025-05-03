<?php
    require_once "../inc/func.php";

    $userId = $_GET['user_id'];
    $user = getUserById($userId);

    if (!$user) {
        http_response_code(404);
        echo json_encode(["error" => "User not found"]);
        exit;
    } else {
        // Get the user's credibility and risk
        $credibility = $user['credibility'];
        $risk = $user['risk'];

        // COUNT statement
        $conn = getDB();

        $stmt = $conn->prepare("SELECT COUNT(*) as `rank` FROM users WHERE credibility > :credibility OR (credibility = :credibility AND risk < :risk)");
        $stmt->bindParam(":credibility", $credibility);
        $stmt->bindParam(":risk", $risk);
        $stmt->execute();

        $rank = $stmt->fetch(PDO::FETCH_ASSOC)['rank'] + 1;

        echo json_encode([
            "rank" => $rank
        ]);
        http_response_code(200);
    }
?>