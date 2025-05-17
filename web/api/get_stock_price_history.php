<?php
    include "../inc/main.php";

    $conn = getDB();

    // Get the stock ID from the request
    $stockId = $_GET['stock_id'] ?? null;
    if (!$stockId) {
        echo json_encode(["error" => "Missing stock_id parameter."]);
        http_response_code(400);
        exit;
    }

    // Fetch the stock price history, limiting to the last 30 days
    $query = "SELECT * FROM stock_prices WHERE stock_id = :stock_id AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) ORDER BY date ASC";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":stock_id", $stockId);
    $stmt->execute();
    $priceHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($priceHistory) {
        $response = [];
        foreach ($priceHistory as $row) {
            $response[] = [
                "date" => $row['date'],
                "price" => $row['price']
            ];
        }
        echo json_encode($response);
        http_response_code(200);
    } else {
        echo json_encode(["error" => "No price history found for this stock."]);
        http_response_code(404);
    }
?>