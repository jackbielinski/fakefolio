<?php
include dirname(__DIR__, 2) . '/include/backend/main.php';
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
            <?php include dirname(__DIR__, 2) . '/include/page_elements/toolbar-header.php'; ?>
            <div class="text-center">
                <h1 class="text-4xl font-bold">Integrate Fakefolio into <span class="highlight blue">your</span> application.</h1>
                <p>With Fakefolio integration, users can directly manage their portfolios, create trades, and track performance - all from your website.</p>
            </div>
            <div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <div class="step blue">1</div>
                        <strong class="font-bold text-xl">Create your app</strong>
                        <p>Use your existing Fakefolio account to build your application. Customize the branding so OAuth flows match your app's design.</p>
                    </div>
                    <div>
                        <div class="step blue">2</div>
                        <strong class="font-bold text-xl">Apply for approval</strong>
                        <p>Submit your application for review. Our team will ensure it meets security and functionality standards before approval.</p>
                    </div>
                    <div>
                        <div class="step blue">3</div>
                        <strong class="font-bold text-xl">Integrate!</strong>
                        <p>Once approved, use our comprehensive API and OAuth system to integrate Fakefolio features directly into your application.</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-6">
                <h2 class="font-bold text-3xl">Ready to build?</h2>
                <a href="<?= BASE_URL ?>/developers/dashboard" class="btn secondary mt-4">Get Started</a>
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