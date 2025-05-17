<?php
    include "../inc/main.php";

    $active_user = getUserInfo($_SESSION['user_id']);
    $conn = getDB();

    // If there is a stock_id in the GET request, set it
    if (isset($_GET['stock_id'])) {
        $stock_id = $_GET['stock_id'];

        // Get owned shares, price, and stock id
        $stmt = $conn->prepare("SELECT owned_shares FROM shares WHERE user_id = :user_id AND stock_id = :stock_id");
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':stock_id', $stock_id);
        $stmt->execute();

        $holdings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response = [];

        if ($holdings && count($holdings) > 0) {
            foreach ($holdings as $holding) {
            $owned_shares = $holding['owned_shares'];

            // Get stock info
            $query = "SELECT * FROM stocks WHERE stock_id = ?";
            $stmt_stock = $conn->prepare($query);
            $stmt_stock->execute([$stock_id]);
            $stock = $stmt_stock->fetch(PDO::FETCH_ASSOC);

            if ($stock) {
                // Get latest price
                $query = "SELECT * FROM stock_prices WHERE stock_id = ? ORDER BY date DESC LIMIT 1";
                $stmt_price = $conn->prepare($query);
                $stmt_price->execute([$stock_id]);
                $price_data = $stmt_price->fetch(PDO::FETCH_ASSOC);

                if ($price_data) {
                // Calculate total value
                $total_value = round($price_data['price'] * $owned_shares, 2);
                $response[] = [
                    'stock_name' => $stock['stock_name'],
                    'stock_ticker' => $stock['stock_ticker'],
                    'owned_shares' => $owned_shares,
                    'current_price' => round($price_data['price'], 2),
                    'total_value' => round($total_value, 2)
                ];
                }
            }
            }
        } else {
            // No shares found for this stock
            http_response_code(404);
            echo json_encode(['error' => 'No shares found for this stock.']);
            exit;
        }
    } else {
        $stmt = $conn->prepare("SELECT * FROM shares WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();

        // Get owned_shares, price, and stock id
        $holdings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response = [];

        foreach ($holdings as $holding) {
            $stock_id = $holding['stock_id'];
            $owned_shares = $holding['owned_shares'];

            // Get stock info
            $query = "SELECT * FROM stocks WHERE stock_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$stock_id]);
            $stock = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($stock) {
                // Get latest price
                $query = "SELECT * FROM stock_prices WHERE stock_id = ? ORDER BY date DESC LIMIT 1";
                $stmt = $conn->prepare($query);
                $stmt->execute([$stock_id]);
                $price_data = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($price_data) {
                    // Calculate total value
                    $total_value = round($price_data['price'] * $owned_shares, 2);
                    $response[] = [
                        'stock_name' => $stock['stock_name'],
                        'stock_ticker' => $stock['stock_ticker'],
                        'owned_shares' => $owned_shares,
                        'current_price' => round($price_data['price'], 2),
                        'total_value' => round($total_value, 2)
                    ];
                }
            }
        }
    }

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    http_response_code(200);

    // Close the database connection
    $conn = null;
?>