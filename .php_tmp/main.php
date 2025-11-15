<?php
    session_start();

    # Composer
    require_once __DIR__ . '/../../vendor/autoload.php';

    # Dotenv setup
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    # Database connection
    $host = $_ENV['PSQL_HOST'];
    $port = $_ENV['PSQL_PORT'];
    $user = $_ENV['PSQL_USER'];
    $password = $_ENV['PSQL_PASSWORD'];
    $db = $_ENV['PSQL_DB'];

    $dsn = "pgsql:host=$host;port=$port;dbname=$db";
    try {
        $pdo = new PDO($dsn, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        error_log("Database connection error: " . $e->getMessage());
        exit("Database connection error");
    }
?>