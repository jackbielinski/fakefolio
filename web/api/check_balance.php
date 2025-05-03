<?php
    require_once "../inc/func.php";

    $userId = $_GET['user_id'];
    $balances = getBalances($userId);

    echo json_encode($balances);
    http_response_code(200);
?>