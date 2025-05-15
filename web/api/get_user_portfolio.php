<?php
    include "../inc/main.php";

    $active_user = getUserInfo($_SESSION['user_id']);

    // Get all shares owned by the user
    $conn = getDB();
    $stmt = $conn->prepare("SELECT * FROM shares WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();

    // Get stock IDs
    $shares = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$shares) {
        echo "<p>No shares found for user.</p>";
        exit;
    } else {
        $shares = array_map(function($share) {
            return [
                'stock_id' => $share['stock_id'],
                'quantity' => $share['owned_shares']
            ];
        }, $shares);
    }

    // Get stock prices
    $stock_prices = [];
    foreach ($shares as $share) {
        $stmt = $conn->prepare("SELECT price FROM stock_prices WHERE stock_id = :stock_id");
        $stmt->bindParam(':stock_id', $share['stock_id']);
        $stmt->execute();
        $price = $stmt->fetchColumn();
        if ($price) {
            $stock_prices[$share['stock_id']] = $price;
        } else {
            echo "<p>Price not found for stock ID: {$share['stock_id']}</p>";
            exit;
        }
    }

    // Prepare the response
    $response = [];
    foreach ($shares as $share) {
        $stock_id = $share['stock_id'];
        $quantity = $share['quantity'];
        $price = $stock_prices[$stock_id];
        $response[] = [
            'stock_id' => $stock_id,
            'quantity' => $quantity,
            'price' => $price
        ];
    }

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    http_response_code(200);

    // Close the database connection
    $conn = null;
?>