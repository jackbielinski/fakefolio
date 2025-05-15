<?php
    $ticker = $_GET['ticker'] ?? null;
    include "../inc/main.php";

    if (!$ticker) {
        echo json_encode(["error" => "Ticker symbol is required."]);
        http_response_code(400);
        exit;
    }

    $conn = getDB();

    $stmt = $conn->prepare("SELECT * FROM stocks WHERE stock_ticker = :ticker");
    $stmt->bindParam(":ticker", $ticker);
    $stmt->execute();
    $stock = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($stock) {
        $stockId = $stock['stock_id'];

        // Get the latest price
        $stmt = $conn->prepare("SELECT * FROM stock_prices WHERE stock_id = :stock_id ORDER BY date DESC LIMIT 1");
        $stmt->bindParam(":stock_id", $stockId);
        $stmt->execute();
        $price = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($price) {
            echo json_encode([
                "ticker" => $stock['stock_ticker'],
                "name" => $stock['stock_name'],
                "latest_price" => $price['price'],
                "date" => $price['date']
            ]);
            http_response_code(200);
        } else {
            echo json_encode(["error" => "No price data found for this stock."]);
            http_response_code(404);
        }
    } else {
        echo json_encode(["error" => "Stock not found."]);
        http_response_code(404);
    }
?>