<?php
include "../inc/main.php";

$conn = getDB();

$active_user = getUserInfo($_SESSION['user_id']);
if (!$active_user) {
    echo "<p>User not found.</p>";
    exit;
}

if (isset($_POST['stock_id'])) {
    // Set the stock_id from the GET parameter
    $stock_id = $_POST['stock_id'];
} else {
    // If no stock_id is provided, redirect or show an error
    echo "<p>No stock ID provided.</p>";
    exit;
}

if (isset($_POST['quantity'])) {
    $quantity = $_POST['quantity'];
} else {
    $quantity = 1; // Default value
}

// Get the stock information
$stock = getStockById($stock_id);
if (!$stock) {
    echo "<p>Stock not found.</p>";
    exit;
}

// Get the current price of the stock
$current_price = getStockPrice($stock_id)['price'];
if ($current_price === false) {
    echo "<p>Failed to retrieve stock price.</p>";
    exit;
}

$current_price = round($current_price, 2);

// See if the user has enough shares to sell
$owned_shares = getUserShares($active_user['id'], $stock_id)["total_shares"];
if ($owned_shares < $quantity) {
    echo json_encode(['status' => 'error', 'message' => 'Insufficient shares.']);
    exit;
}

// Calculate the total price
$subtotal = $current_price * $quantity;
$subtotal = round($subtotal, 2);

// If the user has enough shares, proceed with the sale
// If the user is selling all shares, delete the entry from the shares table
if ($owned_shares == $quantity) {
    $query = "DELETE FROM shares WHERE user_id = :user_id AND stock_id = :stock_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $active_user['id']);
    $stmt->bindParam(':stock_id', $stock_id);
    $stmt->execute();
} else {
    // Otherwise, just update the owned shares
    $query = "UPDATE shares SET owned_shares = owned_shares - :owned_shares WHERE user_id = :user_id AND stock_id = :stock_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':owned_shares', $quantity);
    $stmt->bindParam(':user_id', $active_user['id']);
    $stmt->bindParam(':stock_id', $stock_id);
    $stmt->execute();
}

// Add the total price to the user's balance
$query = "UPDATE users SET clean_money = clean_money + :total_price WHERE id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':total_price', $subtotal);
$stmt->bindParam(':user_id', $active_user['id']);
$stmt->execute();

// Lower the stock price by a small random percentage depending on the quantity sold
$decrease_percent = 0.01 * rand(1, 15) * $quantity;
$lower_price = $current_price - ($current_price * $decrease_percent);

// Ensure the price doesn't go negative; set to 0.01 if it does
if ($lower_price < 0.01) {
    $lower_price = 0.01;
}

$query = "INSERT INTO stock_prices (stock_id, price) VALUES (:stock_id, :price)";
$stmt = $conn->prepare($query);
$stmt->bindParam(':stock_id', $stock_id);
$stmt->bindParam(':price', $lower_price);
$stmt->execute();

// Return success message
echo json_encode(['status' => 'success', 'message' => 'Stock sold successfully.', 'sell_value' => $subtotal]);
?>