<?php
include "../inc/main.php";

// Check if user is admin
if (!isAdmin($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fakefolio-dist.css">
    <script src="../js/modalManager.js"></script>
    <script src="../js/formHandler.js"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6/dist/tippy-bundle.umd.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/animations/scale.css">
    <title>Fakefolio</title>
</head>

<body>
    <div id="body">
        <div id="content-container">
            <?php include "../inc/header.php"; ?>
            <div id="content">
                <div class="inline-block">
                    <img class="inline-block" src="../_static/icons/key.png" alt="Key" width="40">
                    <strong class="text-3xl inline-block align-middle ml-1">Admin Panel</strong>
                </div>
                <div id="balance-bar" class="float-right items-right">
                    <div class="stats">
                        <span class="text-red-500 stat" id="userCount">0</span> <strong
                            class="text-sm text-gray-400">USERS</strong><br>
                        <span class="stat" id="adminCount">0</span> <strong
                            class="text-sm text-gray-400">ADMINS</strong><br>
                    </div>
                </div>
                <script>
                    // Update the user count and admin count
                    function updateUserCounts() {
                        console.log("calling fetch");
                        fetch('../api/get_user_counts.php')
                            .then(response => {
                                console.log("Raw response:", response);
                                return response.json();
                            })
                            .then(data => {
                                console.log("Parsed data:", data);

                                const userCount = parseInt(data.users) || 0;
                                const adminCount = parseInt(data.admins) || 0;

                                const format = (num) => num.toLocaleString('en-US');

                                document.getElementById('userCount').innerText = format(userCount);
                                document.getElementById('adminCount').innerText = format(adminCount);
                            })
                            .catch(error => {
                                console.error('Fetch failed:', error);
                            });
                    }

                    window.onload = function () {
                        // Call the function to update user counts when the page loads
                        updateUserCounts();
                        // Set an interval to update user counts every 30 sec
                        setInterval(updateUserCounts, 30000); // 30 seconds
                    };
                </script>
                <div><br>
                    <div class="profile-card">
                        <img id="pfp" src="../_static/pfp/default.png" alt="Default" width="100" class="inline-block">
                        <div class="profile-card-body ml-2 inline-block align-middle">
                            <strong id="username" class="text-2xl">User Management</strong><br>
                            <button id="find-user" class="btn-sm btn-primary mt-2">Find user</button>
                        </div>
                    </div><br>
                    <div class="profile-card">
                        <img id="pfp" src="../_static/icons/stocks.png" alt="Stocks" width="100" class="inline-block">
                        <div class="profile-card-body ml-2 inline-block align-middle">
                            <strong class="text-2xl">Manage Stock Exchange</strong><br>
                            <button id="create-stock" class="btn-sm btn-primary mt-2">Add new stock</button>
                            <button id="stock-ex-settings" class="btn-sm btn-primary mt-2">Change settings</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="modal" class="modal">
                <div id="modal-content" class="hidden"></div>
            </div>
            <script type="module">
                // Function to handle success response from the server
                function handleSuccess(response) {
                    // Check if the response is a success message
                    if (response.success) {
                        const formMessages = document.getElementById('form-messages');
                        formMessages.classList.remove('hidden');
                        formMessages.classList.add('text-green-600');
                        formMessages.innerHTML = "<br>Profile changes saved successfully!";

                        // Log everything
                        console.log('Success:', response.message);
                        console.log('Form data:', response.data);
                    } else {
                        handleError(response);
                    }
                }

                // Function to handle error response from the server
                function handleError(errors) {
                    const formMessages = document.getElementById('form-messages');
                    formMessages.classList.remove('hidden');
                    formMessages.classList.add('text-red-500');
                    formMessages.innerHTML = "<br>" + (errors.message || "An error occurred, please try again. Check all fields.");
                }

                document.getElementById('find-user').addEventListener('click', function () {
                    modalManager.load("../modals/admin/find_user.php", function () {
                        // Handle form submission
                        // Get form data and send it to the server
                        const form = document.getElementById('find-user-form');
                        const submit = document.getElementById('submit-form');
                        submit.addEventListener('click', function (event) {
                            event.preventDefault();

                            const formData = new FormData(form);
                            const url = form.action; // Get the form action URL
                            const username = formData.get('search_username'); // Get the username from the form data
                            const urlWithParam = `${url}?username=${encodeURIComponent(username)}`;

                            fetch(urlWithParam, {
                                // Append the username to the URL
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest' // Indicate that this is an AJAX request
                                }
                            })
                                .then(response => response.text())
                                .then(text => {
                                    // If the response is OK
                                    if (text) {
                                        // Parse the response text as JSON
                                        const data = JSON.parse(text);

                                        const userId = data.id;
                                        const profilePicturePath = data.profile_picture_path || "../_static/pfp/default.png";
                                        const username = data.username;
                                        const email = data.email;
                                        const dirty = data.dirty_money;
                                        const clean = data.clean_money;
                                        const risk = data.risk;
                                        const credibility = data.credibility;

                                        // Show the results in console
                                        document.getElementById('data').classList.remove('hidden');
                                        // Show the results in the modal
                                        const searchedUserInfo = document.getElementById('searched-user-info');
                                        searchedUserInfo.classList.remove('hidden');
                                        searchedUserInfo.querySelector('#pfp').src = "../_static/" + profilePicturePath;
                                        searchedUserInfo.querySelector('#username').innerText = username;
                                        searchedUserInfo.querySelector('#email-value').innerText = email;
                                        searchedUserInfo.querySelector('#user_id-value').innerText = userId;
                                        searchedUserInfo.querySelector('#account_created-value').innerText = data.account_created;

                                        if (username == undefined) {
                                            searchedUserInfo.querySelector('#username').innerText = "User not found.";
                                            document.getElementById('data').classList.add('hidden');
                                        } else {
                                            searchedUserInfo.querySelector('#username').innerText = username;
                                        }

                                        // Format balances
                                        const formatNumberDollar = (num) => parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                        searchedUserInfo.querySelector('#dirty-value').innerText = `$${formatNumberDollar(dirty)}`;
                                        searchedUserInfo.querySelector('#clean-value').innerText = `$${formatNumberDollar(clean)}`;

                                        const formatNumberRisk = (num) => parseInt(num).toLocaleString('en-US');
                                        searchedUserInfo.querySelector('#risk-value').innerText = formatNumberRisk(risk);
                                        searchedUserInfo.querySelector('#credibility-value').innerText = formatNumberRisk(credibility);

                                        // Tooltips

                                        tippy('#ban', {
                                            content: 'Ban User',
                                            theme: 'light',
                                            animation: 'scale',
                                            arrow: true,
                                            placement: 'bottom',
                                        });

                                        tippy('#warn', {
                                            content: 'View Warnings',
                                            theme: 'light',
                                            animation: 'scale',
                                            arrow: true,
                                            placement: 'bottom',
                                        });

                                        tippy('#edit', {
                                            content: 'Edit',
                                            theme: 'light',
                                            animation: 'scale',
                                            arrow: true,
                                            placement: 'bottom',
                                        });
                                    } else {
                                        handleError({ message: "User not found." });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    handleError({ message: "An error occurred while processing your request." });
                                });
                        });
                    });
                });

                document.getElementById('create-stock').addEventListener('click', function () {
                    modalManager.load("../modals/admin/create_stock.php", function () {
                        const submitBtn = document.getElementById('submit-form');
                        const generateFieldsBtn = document.getElementById('generate-fields');
                        const resetBtn = document.getElementById('reset-form');
                        const bulkGenerateBtn = document.getElementById('bulk-generate');

                        const form = document.getElementById('create-stock-form');
                        const formMessages = document.getElementById('form-messages');

                        // Handle form submission
                        submitBtn.addEventListener('click', function (event) {
                            event.preventDefault();

                            const formData = new FormData(form);

                            fetch("../api/admin/create_stock.php", {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest' // Indicate that this is an AJAX request
                                }
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        formMessages.classList.remove('hidden');
                                        formMessages.classList.add('text-green-600');
                                        formMessages.innerHTML = "<br>Stock created successfully!<br>";
                                        console.log('Success:', data.message);

                                        // Wait 3 seconds and hide text
                                        setTimeout(() => {
                                            formMessages.classList.add('hidden');
                                            formMessages.classList.remove('text-green-600');
                                        }, 3000);
                                    } else {
                                        formMessages.classList.remove('hidden');
                                        formMessages.classList.add('text-red-500');
                                        formMessages.innerHTML = "<br>" + (data.message + "<br>" || "An error occurred, please try again. Check all fields.<br>");
                                        console.error('Error:', data.message);

                                        // Wait 3 seconds and hide the message
                                        setTimeout(() => {
                                            formMessages.classList.add('hidden');
                                        }, 3000);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                });
                        });

                        // Handle generate fields button
                        generateFieldsBtn.addEventListener('click', function () {
                            const stockTicker = document.getElementById('stock_ticker').value;
                            const stockName = document.getElementById('stock_name').value;
                            const stockPrice = document.getElementById('stock_price').value;

                            const nameSet1 = [
                                "Green", "Silver", "Blue", "Red", "North", "South", "East", "West", "Union", "First",
                                "Prime", "Liberty", "Golden", "Pine", "River", "Oak", "Stone", "Summit", "Pioneer",
                                "Capital", "Civic", "Metro", "Pacific", "Atlas", "Global", "Crescent", "Iron", "Cobalt",
                                "Frontier", "Heritage", "Nova", "Echo", "Sun", "Ever", "Bright", "Lone", "Clear", "Canyon",
                                "Velocity", "Crown", "Unity", "True", "Magnet", "Zenith"
                            ];
                            const nameSet2 = [
                                "tech", "labs", "space", "systems", "net", "tronics", "dynamics", "cloud", "works",
                                "data", "ware", "zone", "solutions", "logic", "flow", "drive", "forge", "stack", "ai",
                                "pulse", "grid", "matrix", "engine", "stream", "spark", "boost", "path"
                            ];
                            const corpSet = [
                                "LLC", "Inc", "Corp.", "Ltd.", "Co.", "PLC", "Group", "AG", "S.A.", "Pty Ltd"
                            ];

                            const randomName1 = nameSet1[Math.floor(Math.random() * nameSet1.length)];
                            const randomName2 = nameSet2[Math.floor(Math.random() * nameSet2.length)];
                            const randomCorp = corpSet[Math.floor(Math.random() * corpSet.length)];
                            const randomName = `${randomName1}${randomName2} ${randomCorp}`;

                            // Generate a random 4-letter ticker based on randomName1 and randomName2
                            const randomTicker = randomName1.slice(0, 2).toUpperCase() + randomName2.slice(0, 2).toUpperCase();
                            const randomPrice = (Math.random(1, 5) * 100).toFixed(2);

                            // Set the generated values to the input fields
                            document.getElementById('stock_ticker').value = randomTicker;
                            document.getElementById('stock_name').value = randomName;
                            document.getElementById('stock_price').value = randomPrice;

                            // Show a message indicating that the fields have been generated
                            formMessages.classList.remove('hidden');
                            formMessages.classList.remove('text-red-500');
                            formMessages.classList.add('text-green-700');
                            formMessages.classList.add('font-bold');
                            formMessages.innerHTML = "<br>Fields generated successfully!<br>";
                            console.log('Generated:', {
                                ticker: randomTicker,
                                name: randomName,
                                price: randomPrice
                            });

                            // Wait 3 seconds and hide the message
                            setTimeout(() => {
                                formMessages.classList.add('hidden');
                            }, 3000);
                        });

                        // Handle reset button
                        resetBtn.addEventListener('click', function () {
                            // Reset the form fields
                            document.getElementById('stock_ticker').value = '';
                            document.getElementById('stock_name').value = '';
                            document.getElementById('stock_price').value = '2.00';

                            // Show a message indicating that the form has been reset
                            formMessages.classList.remove('hidden');
                            formMessages.classList.add('text-green-700');
                            formMessages.classList.add('font-bold');
                            formMessages.innerHTML = "<br>Form reset successfully!<br>";

                            // Wait 3 seconds and hide the message
                            setTimeout(() => {
                                formMessages.classList.add('hidden');
                            }, 3000);
                        });

                        // Handle bulk generate button
                        bulkGenerateBtn.addEventListener('click', function () {
                            modalManager.load("../modals/admin/bulk_generate_stocks.php", function () {
                            });
                        });
                    });
                });

                document.getElementById('stock-ex-settings').addEventListener('click', function () {
                    modalManager.load("../modals/admin/stock_ex_settings.php", function () {

                    });
                });
            </script>

        </div>
        <div id="footer" class="text-center">
            <p>Fakefolio is a game. All characters and events in this game - even those based on real people - are
                entirely
                fictional. Any resemblance to actual persons, living or dead, or actual events is purely coincidental.
            </p>
            <br><small>&copy; 2025 Fakefolio</small>
        </div>
    </div>
</body>

</html>