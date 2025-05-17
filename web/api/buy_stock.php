<?php
    include "../inc/main.php";

    $conn = getDB();

    $user_id = $_SESSION['user_id'];
    $stock_id = $_POST['stock_id'];
    $quantity = $_POST['quantity'];
    $quantity = floatval($quantity);
    $quantity = max($quantity, 1); // Ensure quantity is at least 1

    // Check if the stock is valid
    $stock = getStockById($stock_id);

    if (!$stock) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid stock.']);
        exit;
    }

    // Get the current price of the stock
    $current_price = getStockPrice($stock_id)['price'];

    if ($current_price === false) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve stock price.']);
        exit;
    }

    $current_price = round($current_price, 2);

    // Get the order fee
    $query = "SELECT order_fee FROM stock_exchange_settings";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $order_fee = $result['order_fee'];
    $order_fee = floatval($order_fee);
    $order_fee = ($order_fee / 100);
    $order_fee = round($order_fee, 2);

    // Calculate the total price
    $subtotal = $current_price * $quantity;
    $subtotal = round($subtotal, 2);
    $total_price = $subtotal + ($subtotal * $order_fee);
    $total_price = round($total_price, 2);

    // Check if the user has enough balance
    $balances = getBalances($user_id);
    if ($balances['clean_money'] < $total_price) {
        echo json_encode(['status' => 'error', 'message' => 'Insufficient funds.', 'current_price' => $current_price, 'quantity' => $quantity, 'order_fee' => $order_fee, 'total_price' => $total_price]);
        exit;
    } else {
        // Deduct the total price from the user's balance
        $conn = getDB();
        $query = "UPDATE users SET clean_money = clean_money - :total_price WHERE id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':total_price', $total_price);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        // Add the stock to the user's portfolio
        $query = "INSERT INTO shares (user_id, stock_id, owned_shares) VALUES (:user_id, :stock_id, :owned_shares)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':stock_id', $stock_id);
        $stmt->bindParam(':owned_shares', $quantity);
        $stmt->execute();

        // Manipulate the stock price
        // Depending on how many shares are bought at what price, the stock price will be manipulated by a certain percentage of that using the stock_purchase_power variable.
        $query = "SELECT stock_purchase_power FROM stock_exchange_settings";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stock_purchase_power = $result['stock_purchase_power'];

        // Calculate the stock purchase power
        $stock_purchase_power = floatval($stock_purchase_power);
        $stock_purchase_power = ($stock_purchase_power / 100);

        // Calculate the new stock price using the following formula:
        // new_price = current_price + (quantity * stock_purchase_power)
        $new_price = $current_price + ($current_price * $quantity) * $stock_purchase_power;
        // Add a random percentage to the new price
        $random_percent = 0.01 * rand(1, 7) * $quantity;
        $new_price += ($new_price * $random_percent);
        // Ensure the new price doesn't go negative; set to 0.01 if it does
        if ($new_price < 0.01) {
            $new_price = 0.01;
        }
        $new_price = round($new_price, 2);

        // Insert the new price into the stock_prices table
        $query = "INSERT INTO stock_prices (stock_id, price) VALUES (:stock_id, :price)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':stock_id', $stock_id);
        $stmt->bindParam(':price', $new_price);
        $stmt->execute();

        // Get current credibility
        $query = "SELECT credibility FROM users WHERE id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $credibility = $result['credibility'];

        // Calculate the new credibility
        $randPercent = 0.01 * rand(1, 7) * $quantity;

        $new_credibility = $credibility + $randPercent;
        $new_credibility = round($new_credibility, 0);

        // Update the user's credibility
        $query = "UPDATE users SET credibility = :credibility WHERE id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':credibility', $new_credibility);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        // Return success message
        echo json_encode(['status' => 'success', 'message' => 'Stock purchased successfully.', 'new_price' => $new_price]);
    }
