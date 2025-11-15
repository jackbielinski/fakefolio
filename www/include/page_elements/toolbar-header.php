<?php
    // Get username from session if logged in
    $user_id = $_SESSION['user_id'] ?? null;

    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $username = $user['username'];
    }
?>

<div class="toolbar my-6">
    <a href="<?= BASE_URL ?>/">
        <img src="<?= BASE_URL ?>/_static/wordmark/f_wordmark_flat_white.png" alt="Fakefolio" width="90">
    </a>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="links">
            <a href="<?= BASE_URL ?>/login">Log in</a>
            <span>or</span>
            <a href="<?= BASE_URL ?>/register">Register</a>
        </div>
    <?php else: ?>
        <div class="links">
            <strong>Welcome, <?= htmlspecialchars($username) ?></strong>
        </div>
        <div class="links">
            <a href="<?= BASE_URL ?>/@<?= htmlspecialchars($username) ?>">Profile</a>
            <span>|</span>
            <a href="<?= BASE_URL ?>/logout">Log out</a>
        </div>
    <?php endif; ?>
</div>

<?php if (isset($_SESSION['user_id'])): ?>
    <div class="toolbar linknav">
        <a href="<?= BASE_URL ?>/home">Home</a>
        <a href="<?= BASE_URL ?>/stocks">Stocks</a>
        <a href="<?= BASE_URL ?>/casino">Casino</a>
        <a href="<?= BASE_URL ?>/friends">Friends</a>
        <?php
            $stmt = $pdo->prepare("SELECT * FROM webmasters WHERE id = :id");
            $stmt->execute(['id' => $_SESSION['user_id']]);
            $webmaster = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($webmaster) {
                echo '<a href="' . BASE_URL . '/developers">Admin</a>';
            }
        ?>
        <a href="<?= BASE_URL ?>/account/settings">Settings</a>
    </div>
<?php endif; ?>

<?php
// Banner handling
$bannerDir = ROOT_PATH . '/_static/banner/';
$files = glob($bannerDir . 'f_*.png');
$bannerCount = count($files);

if ($bannerCount > 0) {
    $randomBanner = basename($files[array_rand($files)]);
    $bannerSrc = BASE_URL . '/_static/banner/' . $randomBanner;
} else {
    $bannerSrc = BASE_URL . '/_static/banner/f.png';
}

$currentPath = $_SERVER['REQUEST_URI'];
if (strpos($currentPath, '/casino') !== false) {
    $bannerSrc = BASE_URL . '/_static/banner/casino.png';
} elseif (strpos($currentPath, '/stocks') !== false) {
    $bannerSrc = BASE_URL . '/_static/banner/stocks.png';
} elseif (strpos($currentPath, '/developers') !== false) {
    $bannerSrc = BASE_URL . '/_static/banner/developers.png';
} elseif (preg_match('#/@([^/]+)#', $currentPath, $m)) {
    // Path contains an @username, safely use the captured username
    $usernameInPath = $m[1] ?? null;

    if ($usernameInPath) {
        $stmt = $pdo->prepare("SELECT banner_img FROM users WHERE username = :username");
        $stmt->execute(['username' => $usernameInPath]);
        $userBanner = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userBanner && !empty($userBanner['banner_img'])) {
            $bannerSrc = BASE_URL . '/_static/' . ltrim($userBanner['banner_img'], '/');
        } else {
            $bannerSrc = BASE_URL . '/_static/banner/placeholder.png';
        }
    } else {
        $bannerSrc = BASE_URL . '/_static/banner/placeholder.png';
    }
} elseif (strpos($currentPath, '/profile') !== false) {
    // /profile (no @username) — use the logged-in user's banner if available
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT banner_img FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $userBanner = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userBanner && !empty($userBanner['banner_img'])) {
            $bannerSrc = BASE_URL . '/_static/' . ltrim($userBanner['banner_img'], '/');
        } else {
            $bannerSrc = BASE_URL . '/_static/banner/placeholder.png';
        }
    } else {
        $bannerSrc = BASE_URL . '/_static/banner/placeholder.png';
    }
}
?>

<div class="header">
    <img class="banner" src="<?= htmlspecialchars($bannerSrc) ?>" alt="Fakefolio Banner">
    <span style="display:none;"><?= $bannerCount ?></span>
</div>
