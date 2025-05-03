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
                        <span class="text-red-500 stat" id="userCount">0</span> <strong class="text-sm text-gray-400">USERS</strong><br>
                        <span class="stat" id="adminCount">0</span> <strong class="text-sm text-gray-400">ADMINS</strong><br>
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
                            <button id="edit-user" class="btn-sm btn-primary mt-2">Edit user</button>
                            <button id="delete-user" class="btn-sm btn-primary mt-2">Remove user cooldown(s)</button>
                        </div>
                    </div>
                </div>
                <div><br>
                    <div class="profile-card">
                        <img id="pfp" src="../_static/icons/package.png" alt="Package" width="100" class="inline-block">
                        <div class="profile-card-body ml-2 inline-block align-middle">
                            <strong id="username" class="text-2xl">Item Manager</strong><br>
                            <button id="view-items" class="btn-sm btn-primary mt-2">View existing item</button>
                            <button id="create-item" class="btn-sm btn-secondary mt-2">Create new item</button>
                        </div>
                    </div>
                </div><br>
            </div>
            <div id="modal" class="modal">
                <div id="modal-content" class="hidden"></div>
            </div>
                <script>
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

                        document.getElementById('find-user').addEventListener('click', function() {
                            modalManager.load("../modals/admin/find_user.php", function() {
                                // Handle form submission
                                // Get form data and send it to the server
                                const form = document.getElementById('find-user-form');
                                const submit = document.getElementById('submit-form');
                                submit.addEventListener('click', function(event) {
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

                                            document.getElementById('action-view-btn').addEventListener('click', function() {
                                                modalManager.close();
                                                modalManager.load("../modals/admin/actions.php?user_id=" + userId, function() {
                                                    // Callback function after the modal is opened
                                                    console.log("Edit user modal opened.");
                                                });
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

                        document.getElementById('create-item').addEventListener('click', function() {
                            modalManager.load("../modals/admin/create_new_item.php", function() {
                                // Callback function after the modal is opened
                                console.log("Edit user modal opened.");
                            });
                        });

                        document.getElementById('send-message').addEventListener('click', function() {
                            modalManager.load("../modals/admin/send_system_message.php", function() {
                                // Callback function after the modal is opened
                                console.log("Edit user modal opened.");
                            });
                        });
                </script>
            
        </div>
        <div id="footer" class="text-center">
            <p>Fakefolio is a game. All characters and events in this game - even those based on real people - are entirely
            fictional. Any resemblance to actual persons, living or dead, or actual events is purely coincidental.</p>
            <br><small>&copy; 2025 Fakefolio</small>
        </div>
    </div>
</body>
</html>