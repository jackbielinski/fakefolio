<?php
include "../inc/main.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fakefolio-dist.css">
    <!-- JS -->
    <script src="../js/modalManager.js"></script>
    <script src="../js/formHandler.js"></script>
    <title>Fakefolio</title>
</head>
<body>
    <div id="body">
        <div id="content-container">
            <?php include "../inc/header.php"; ?>
            <div class="inline-block">
                <img class="inline-block" src="../_static/icons/wrench.png" alt="Wrench" width="40">
                <strong class="text-3xl inline-block align-middle ml-1">Account Settings</strong>
            </div><br><br>
            <hr><br>
            <div class="content">
                <div id="verifyEmail">
                    <h2 class="font-bold text-2xl text-yellow-600">Verify your E-mail!</h2>
                    <span>You have not verified your E-mail address yet. Please check your inbox for a verification link. Without verifying your E-mail address, you could lose access to your account. Before resending, check your junk/spam folders for an E-mail originating from <strong>@fakefolio.com</strong>.</span><br><br>
                    <button id="resendVerify" class="btn-sm btn-primary">Resend verification E-mail</button>
                </div><br>
                <h2 class="font-bold text-2xl text-gray-400">Profile</h2><br>
                <div class="profile-card">
                    <img id="pfp" src="../_static/pfp/default.png" alt="Default" width="100" class="inline-block">
                    <div class="profile-card-body ml-2 inline-block align-top">
                        <strong id="username" class="text-2xl">Username</strong><br>
                        <span id="email"><?php echo htmlspecialchars(getUserInfo($_SESSION['user_id'])["email"] ?? 'Email not set'); ?> <span id="email_Verified_Indicator" class="text-sm font-bold text-red-800">Unverified</span></span><br>
                        <small class="text-gray-400 font-bold">Your E-mail is not shared publicly.</small><br>
                        <span id="membersince">Member since</span><br>
                        <button id="edit-profile" class="btn-sm btn-primary mt-2">Edit Profile</button>
                        <strong id="edit-profile-form-messages" class="hidden ml-2"></strong>
                    </div>
                    <script>
                        // Function to handle success response from the server
                        function handleSuccess(response) {
                            // Check if the response is a success message
                            if (response.success) {
                                modalManager.close(); // Close the modal after success
                                updateUserInfo(); // Update user info after successful edit
                                const formMessages = document.getElementById('edit-profile-form-messages');
                                formMessages.classList.remove('hidden');
                                formMessages.classList.add('text-green-600');
                                formMessages.innerHTML = "Profile changes saved successfully!";
                                // Wait for 3 seconds before hiding the message
                                setTimeout(() => {
                                    formMessages.classList.add('hidden');
                                    formMessages.classList.remove('text-green-600');
                                }, 3000);
                            } else {
                                handleError(response);
                            }
                        }

                        // Function to handle error response from the server
                        function handleError(errors) {
                            modalManager.close(); // Close the modal on error

                            const formMessages = document.getElementById('edit-profile-form-messages');
                            formMessages.classList.remove('hidden');
                            formMessages.classList.add('text-red-500');
                            formMessages.innerHTML = "<br><br>" + (errors.message || "An error occurred when editing your profile. Make sure all fields are valid and try again.");

                            // Wait for 3 seconds before hiding the message
                            setTimeout(() => {
                                formMessages.classList.add('hidden');
                                formMessages.classList.remove('text-red-500');
                            }, 3000);
                        }

                        document.getElementById('edit-profile').addEventListener('click', function() {
                            console.debug('Edit Profile button clicked');
                            modalManager.load('../modals/edit_profile.php', function() {
                                console.debug('Edit Profile modal loaded');
                                const form = document.getElementById('edit-profile-form');
                                const submit = document.getElementById('save-changes');

                                submit.addEventListener('click', function(event) {

                                    const formData = new FormData(form);

                                    fetch('../api/edit_profile.php', {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.error) {
                                            handleError(data);
                                        } else {
                                            handleSuccess(data);
                                            updateUserInfo(); // Update user info after successful edit
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        handleError({ message: 'An error occurred while processing your request.' });
                                    });
                                });
                            });
                        });

                        function updateUserInfo() {
                            // Get user data from the server
                            const userId = '<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>';
                            if (!userId) {
                                console.error('User ID is not set in the session.');
                                return;
                            }

                            fetch(`../api/get_profile_data.php?user_id=${userId}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! Status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || data.error) {
                                        console.error('Error fetching user data:', data ? data.error : 'No data received');
                                        return;
                                    }

                                    // Update the profile picture
                                    const profilePicture = document.getElementById('pfp');
                                    if (data.profile_picture_path) {
                                        profilePicture.src = '../_static/' + data.profile_picture_path;
                                    } else {
                                        profilePicture.src = '../_static/pfp/default.png'; // Default image if no profile picture is set
                                    }

                                    // Update the username
                                    document.getElementById('username').textContent = data.username;
                            
                                    // Update member since date
                                    const accountCreatedDate = new Date(data.account_created);
                                    const options = { year: 'numeric', month: 'long', day: 'numeric' };
                                    const formattedDate = accountCreatedDate.toLocaleDateString('en-US', options);
                                    document.getElementById('membersince').textContent = 'Member since ' + formattedDate;
                                })
                                .catch(error => {
                                    console.error('Error fetching user data:', error);
                                });
                        }

                        // Call the function to set initial user info
                        updateUserInfo();

                        // Update user info every 10 seconds
                        setInterval(updateUserInfo, 10000);
                    </script>
                </div>
                <br>
                <h2 class="font-bold text-2xl text-gray-400">Onboarding</h2>
                <a href="onboarding.html" class="text-blue-600 underline font-bold hover:text-red-500 hover:decoration-transparent">View onboarding</a>
            </div>
            <div id="modal" class="modal">
                <div id="modal-content"></div>
            </div>
            <script>
                // Profile picture preview logic
                document.addEventListener('change', function(event) {
                    if (event.target && event.target.id === 'profile_picture') {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const profilePicturePrev = document.getElementById('profile_picture_prev');
                                if (profilePicturePrev) {
                                    profilePicturePrev.src = e.target.result;
                                    profilePicturePrev.style.objectFit = 'cover';
                                    profilePicturePrev.style.height = '75px';
                                }
                            };
                            reader.readAsDataURL(file);
                        }
                    }
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