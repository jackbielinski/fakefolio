<?php
    include "../../inc/main.php";

    $conn = getDB();

    $active_user = getUserInfo($_SESSION['user_id']);
    if (!$active_user) {
        echo "<p>User not found.</p>";
        exit;
    }

    // Check if user is an admin
    if (!isAdmin($active_user['id'])) {
        echo "<p>You do not have permission to access this page.</p>";
        exit;
    }

    $stock_name = $_POST['stock_name'];
    $stock_ticker = $_POST['stock_ticker'];
    $stock_price = $_POST['stock_price'];

    // Validate inputs
    if (empty($stock_name) || empty($stock_ticker) || empty($stock_price)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Make sure stock price is either a number or decimal with 2 decimal places
    if (!is_numeric($stock_price) || $stock_price < 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid stock price.']);
        exit;
    }

    // Check if stock ticker is less than 4 characters
    if (strlen($stock_ticker) > 4) {
        echo json_encode(['status' => 'error', 'message' => 'Stock ticker must be less than 4 characters.']);
        exit;
    }

    // Check if stock ticker or name already exists
    $query = "SELECT * FROM stocks WHERE stock_ticker = :stock_ticker OR stock_name = :stock_name";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':stock_ticker', $stock_ticker);
    $stmt->bindParam(':stock_name', $stock_name);
    $stmt->execute();
    $existing_stock = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing_stock) {
        echo json_encode(['status' => 'error', 'message' => 'Stock ticker or name already exists.']);
        exit;
    }

    // Insert new stock into the database
    $query = "INSERT INTO stocks (stock_name, stock_author, stock_ticker) VALUES (:stock_name, :stock_author, :stock_ticker)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':stock_name', $stock_name);
    $stmt->bindParam(':stock_author', $active_user['id']);
    $stmt->bindParam(':stock_ticker', $stock_ticker);
    $stmt->execute();

    // Insert price into stock_prices table
    $stock_id = $conn->lastInsertId();
    $query = "INSERT INTO stock_prices (stock_id, price) VALUES (:stock_id, :price)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':stock_id', $stock_id);
    $stmt->bindParam(':price', $stock_price);
    $stmt->execute();

    // Return success message
    echo json_encode(['status' => 'success', 'message' => 'Stock created successfully.']);
    exit;
?>