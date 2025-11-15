<?php
include dirname(__DIR__, 2) . '/include/backend/main.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/ff_dist.css">
    <title>Fakefolio</title>
</head>

<body>
    <div id="body">
        <div class="container">
            <?php include dirname(__DIR__, 2) . '/include/page_elements/toolbar-header.php'; ?>
            <div>
                <h1 class="font-bold text-xl md:text-3xl inline-block">My applications (0)</h1>
                <a href="<?= BASE_URL ?>/developers/applications/create" class="btn success float-right align-top"><img src="<?= BASE_URL ?>/_static/icon/plus.png" alt="Plus" class="inline-block align-middle" width="16"> Create Application</a>
            </div>
            <div>
                <p class="text-center m-16 text-gray-400">Nothing here.</p>
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