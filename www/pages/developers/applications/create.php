<?php
include dirname(__DIR__, 3) . '/include/backend/main.php';
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
            <?php include dirname(__DIR__, 3) . '/include/page_elements/toolbar-header.php'; ?>
            <div>
                <h1 class="font-bold text-3xl">Create an application</h1>
            </div>
            <div>
                <form>
                    <div class="grid grid-cols-1 gap-3 mt-4">
                        <div>
                            <label for="app_name" class="font-bold">Application Name <span class="required"></span></label>
                            <input type="text" id="app_name" name="app_name" class="input w-full" placeholder="My Fakefolio App" required>
                        </div>
                        <div>
                            <label for="app_description" class="font-bold">Application Description <span class="required"></span></label>
                            <textarea id="app_description" name="app_description" class="input w-full" placeholder="Describe your application..." required></textarea>
                        </div>
                        <div>
                            <label for="redirect_uri" class="font-bold">Redirect URI</label>
                            <input type="text" id="redirect_uri" name="redirect_uri" class="input w-full" placeholder="https://myapp.com/oauth/callback">
                        </div>
                        <div>
                            <button type="submit" class="btn success">Create Application</button>
                        </div>
                    </div>
                </form>
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