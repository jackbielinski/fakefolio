<?php
    session_start();

    header("Cache-Control: no-cache, must-revalidate");

    // --- Universal include root fix ---
    if (!defined('ROOT_PATH')) {
        // Base URL for browser assets (used in <img>, <link>, <script>)
        define('BASE_URL', '/fakefolio/www');
        
        // Root path for PHP includes (used in include(), glob(), etc.)
        define('ROOT_PATH', realpath($_SERVER['DOCUMENT_ROOT'] . BASE_URL));
    }

    # Composer
    require_once dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php';

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
        echo "Database connection error: " . $e->getMessage();
        error_log("Database connection error: " . $e->getMessage());
        exit("Database connection error");
    }

    if (isset($_SESSION['user_id'])) {
        // Get timezone from database and store in session if not already set
        if (!isset($_SESSION['timezone'])) {
            $stmt = $pdo->prepare("SELECT timezone FROM users WHERE id = :id");
            $stmt->execute(['id' => $_SESSION['user_id']]);
            $timezone = $stmt->fetchColumn();
            $_SESSION['timezone'] = $timezone ? $timezone : 'UTC';
        }
        date_default_timezone_set($_SESSION['timezone']);
        // Update last active timestamp
        $stmt = $pdo->prepare("UPDATE users SET last_active = NOW() WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
    }
?>