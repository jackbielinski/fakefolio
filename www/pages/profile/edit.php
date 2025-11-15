<?php
include dirname(__DIR__, 2) . '/include/backend/main.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ./login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/ff_dist.css">
    <title>Fakefolio</title>
</head>

<?php
    // Get current user info
    $stmt = $pdo->prepare("SELECT display_name, username, avatar, banner_img FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $displayName = htmlspecialchars($user['display_name']);
    $username = htmlspecialchars($user['username']);
    $avatar = htmlspecialchars($user['avatar']);
    $banner = htmlspecialchars($user['banner_img']);
?>

<body>
    <div id="body">
        <div class="container">
            <?php include dirname(__DIR__, 2) . '/include/page_elements/toolbar-header.php'; ?>
            <div class="flex items-start">
                <img src="<?php echo BASE_URL; ?>/_static/<?= $avatar ?>" alt="Avatar" width="64">
                <div class="inline-block align-top ml-3 w-full">
                    <h1 class="font-bold text-2xl">Edit Profile</h1>
                    <p>Update your public profile appearance here. Looking for your <a href="<?= BASE_URL; ?>/account/settings">account settings</a>?</p>
                </div>
            </div>
            <div class="mt-4">
                <h2 class="font-bold text-xl">Profile Information</h2>
                <form id="edit-profile-form" method="POST" enctype="multipart/form-data" class="grid gap-2 mt-2">
                    <div>
                        <label for="display_name" class="block font-medium mb-1">Display Name</label>
                        <input type="text" id="display_name" name="display_name" value="<?= $displayName ?>">
                    </div>
                    <input type="hidden" name="username" id="username" value="<?= $username ?>">
                    <div>
                        <label for="avatar" class="block font-medium mb-1">Avatar</label>
                        <div class="flex items-start">
                        <img src="<?= BASE_URL; ?>/_static/<?= $avatar ?>" id="avatarPreview" alt="Avatar Preview" class="w-32 h-32">
                            <div id="avatarEdit" class="inline-block ml-3">
                                <!-- choose image button -->
                                <label for="avatar_upload" class="btn primary">Choose Image</label>
                                <input type="file" id="avatar_upload" name="avatar_upload" class="hidden">
                                <p id="fileName" class="text-gray-500">Upload an image to get started.</p>
                                <div id="imageMetadata" class="mt-2 hidden">
                                    <p id="imageSize" class="text-sm text-gray-400"></p>
                                    <p id="imageType" class="text-sm text-gray-400"></p>
                                    <p id="imageDimensionWarning" class="text-sm text-orange-500 mt-3"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                    <label for="banner" class="block font-medium mb-1">Banner Image</label>
                        <div>
                        <img src="<?= BASE_URL; ?>/_static/<?= $banner ?>" id="bannerPreview" alt="Banner Preview">
                        <div id="bannerEdit" class="mt-3">
                            <!-- choose image button -->
                            <label for="banner_upload" class="btn primary">Choose Image</label>
                            <input type="file" id="banner_upload" name="banner_upload" class="hidden">
                            <p id="bannerFileName" class="text-gray-500">Upload an image to get started.</p>
                            <div id="bannerImageMetadata" class="mt-2 hidden">
                                <p id="bannerImageSize" class="text-sm text-gray-400"></p>
                                <p id="bannerImageType" class="text-sm text-gray-400"></p>
                                <p id="bannerImageDimensionWarning" class="text-sm text-orange-500 mt-3"></p>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div id="error-message" class="hidden"></div>
                    <button type="submit" class="btn success mt-2">Save Changes</button>
                </form>
                <script src="<?= BASE_URL; ?>/js/edit_profile.js"></script>
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