<?php
    include "../inc/main.php";

    $conn = getDB();

    $query = "SELECT order_fee FROM stock_exchange_settings";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($settings) {
        $order_fee = $settings['order_fee'];
        echo json_encode(["order_fee" => $order_fee]);
        http_response_code(200);
    } else {
        echo json_encode(["error" => "Settings not found."]);
        http_response_code(404);
    }
?>