<?php
include './include/backend/main.php';

// Redirect to dashboard if already logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ./login?error=required");
    exit();
}

$stmt = $pdo->prepare("SELECT username, avatar FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$username = $user['username'] ?? null;
$avatar = $user['avatar'] ?? 'placeholder.png';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/ff_dist.css">
    <title>Fakefolio</title>
</head>

<body>
    <div id="body">
        <div class="container">
            <?php include './include/page_elements/toolbar-header.php'; ?>
            <div class="flex items-start">
                <img src="./_static/<?= htmlspecialchars($avatar) ?>" id="avatar" alt="Avatar" width="100">
                <div class="inline-block align-top ml-4">
                    <h1 class="font-bold text-2xl md:text-4xl">Welcome back, <?php echo htmlspecialchars($username); ?>!</h1>
                    <?php
                    $stmt = $pdo->prepare("SELECT dirty_money, clean_money FROM users WHERE id = :id");
                    $stmt->execute(['id' => $_SESSION['user_id']]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    $dirtyMoney = number_format($user['dirty_money'], 2);
                    $cleanMoney = number_format($user['clean_money'], 2);

                    echo "<p class='mt-2 inline-block'><img class='inline-block' src='./_static/icon/clean_money.png' alt='Clean Money'/>&nbsp;<span class='balance clean'>$" . $cleanMoney . "</span>&nbsp;<span class='text-gray-400'>clean money</span></p>&nbsp;&nbsp;";
                    echo "<p class='mt-1 inline-block'><img class='inline-block' src='./_static/icon/dirty_money.png' alt='Dirty Money'/>&nbsp;<span class='balance dirty'>$" . $dirtyMoney . "</span>&nbsp;<span class='text-gray-400'>dirty money</span></p>";
                    ?>
                </div>
            </div>
        </div>
        <div class="footer">
            <p>Fakefolio is a virtual game for entertainment only. All currency, stocks, and trades are fictional and
                have no
                real-world value.</p>
            <p>&copy; 2024 Fakefolio. All rights reserved.</p>
        </div>
    </div>
</body>

</html>