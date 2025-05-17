<?php
    include "../inc/main.php";
    $conn = getDB();

    // Determine which GET parameter to use
    $ticker = $_GET['ticker'] ?? null;
    $stockId = $_GET['stock_id'] ?? null;

    if ($ticker) {
        // Lookup by ticker
        $stmt = $conn->prepare("SELECT * FROM stocks WHERE stock_ticker = :ticker");
        $stmt->bindParam(":ticker", $ticker);
        $stmt->execute();
        $stock = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif ($stockId) {
        // Lookup by stock_id
        $stmt = $conn->prepare("SELECT * FROM stocks WHERE stock_id = :stock_id");
        $stmt->bindParam(":stock_id", $stockId);
        $stmt->execute();
        $stock = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo json_encode(["error" => "Missing ticker or stock_id parameter."]);
        http_response_code(400);
        exit;
    }

    if ($stock) {
        $stockId = $stock['stock_id'];
        // Get the latest price
        $stmt = $conn->prepare("SELECT * FROM stock_prices WHERE stock_id = :stock_id ORDER BY date DESC LIMIT 1");
        $stmt->bindParam(":stock_id", $stockId);
        $stmt->execute();
        $price = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($price) {
            echo json_encode([
                "id" => $stock['stock_id'],
                "ticker" => $stock['stock_ticker'],
                "latest_price" => $price['price']
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