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
                <?php
                    // Get verification code
                    $db = getDB();
                    $stmt = $db->prepare("SELECT verified FROM verification_codes WHERE requesting_email = :email");
                    $stmt->bindValue(':email', getUserInfo($_SESSION['user_id'])["email"], PDO::PARAM_STR);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($result) {
                        $verified = $result[0]['verified'];
                        if ($verified == 0) {
                            echo '<div id="verifyEmail">';
                            echo '<strong class="text-3xl text-yellow-600">Verify your E-mail</strong><br>';
                            echo '<span>We collect your E-mail address to relay important account updates and will never send marketing E-mails to your inbox, ever. Leaving your account unverified may lessen your chance of retreiving your account in the event you get locked out.</span><br>';
                            if (getUserVerifiedEmails($_SESSION['user_id'])) {
                                // Display the verified email addresses
                                echo '<br><div id="existingEmails"><strong class="text-xl text-green-700">Verified E-mail(s)</strong><br>';
                                echo '<span>You have already verified the following E-mail(s):</span><br>';
                                $verifiedEmails = getUserVerifiedEmails($_SESSION['user_id']);
                                foreach ($verifiedEmails as $email) {
                                    echo '<span class="text-sm">' . htmlspecialchars($email['requesting_email']) . '</span><br>';
                                }
                                echo '</div>';
                            }
                            echo '<br><button id="verifyWithCode" class="btn-sm btn-primary">Enter verification code</button> ';
                            echo '<button id="resendEmail" class="btn-sm btn-primary">Resend verification E-mail</button><br>';
                            echo '<div id="verification-code" class="hidden mt-5">';
                            echo '<input type="text" id="code" placeholder="Enter verification code">';
                            echo '<button id="verifyBtn" class="btn-sm btn-primary">Verify</button>';
                            echo '</div><br>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger">Error fetching verification status.</div>';
                    }
                ?>
                <script>
                    // if element is not found, do nothing, but if it is found, add event listeners
                    const verifyWithCode = document.getElementById("verifyWithCode");
                    const verificationPanel = document.getElementById("verification-code");

                    verifyWithCode?.addEventListener("click", function() {
                        verificationPanel.classList.toggle("hidden");

                        // Get the verification code from the input field
                        const verifyBtn = document.getElementById("verifyBtn");
                        verifyBtn.addEventListener("click", function() {
                            const code = document.getElementById("code").value;
                            if (code) {
                                const xhr = new XMLHttpRequest();
                                xhr.open("POST", "../api/verify_email.php", true);
                                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                // Send user ID to the server
                                const userId = "<?php echo $_SESSION['user_id']; ?>";
                                xhr.onreadystatechange = function() {
                                    if (xhr.readyState === 4) {
                                        if (xhr.status === 200) {
                                            const response = JSON.parse(xhr.responseText);

                                            if (response.success) {
                                                // Update the UI to reflect verification success
                                                verificationPanel.classList.add("hidden");
                                                document.getElementById("verifyEmail").classList.add("hidden");
                                                document.getElementById("verification-ind").classList.remove("text-red-700");
                                                document.getElementById("verification-ind").classList.add("text-green-700");
                                                document.getElementById("verification-ind").textContent = "Verified";
                                            } else {
                                                alert(response.message);
                                            }
                                        } else {
                                            alert("An error occurred. Please try again.");
                                        }
                                    }
                                };
                                xhr.send(`verificationCode=${code}&userId=${userId}`);
                            } else {
                                alert("Please enter a verification code.");
                            }
                        });
                    });
                </script>
                <h2 class="font-bold text-2xl text-gray-400">Profile</h2><br>
                <div class="profile-card">
                    <img id="pfp" src="../_static/pfp/default.png?v=<?= time() ?>" class="inline-block" alt="Profile Picture" width="100">
                    <div class="profile-card-body ml-2 inline-block align-top">
                        <strong id="username" class="text-2xl"><?php echo htmlspecialchars(getUserInfo($_SESSION["user_id"])["username"]); ?></strong><br>
                        <span id="email"><?php echo htmlspecialchars(getUserInfo($_SESSION['user_id'])["email"] ?? 'Email not set'); ?></span>
                        <?php
                            // Check if the email is verified
                            if (isset($verified) && $verified == 0) {
                                echo '<span id="verification-ind" class="text-red-700 text-sm font-bold">Not verified</span>';
                            } else {
                                echo '<span id="verification-ind" class="text-green-700 text-sm font-bold"> Verified</span>';
                            }
                        ?>
                        <br><small class="text-gray-400 font-bold">Your E-mail is not shared publicly.</small><br>
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
                                    for (let [key, value] of formData.entries()) {
                                    console.log(`${key}:`, value);
                                    }
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

                            fetch(`../api/admin/query_user.php?user_id=${userId}`)
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
                                        profilePicture.src = '../_static/' + data.profile_picture_path + '?v=' + new Date().getTime(); // Cache-busting
                                    } else {
                                        profilePicture.src = '../_static/pfp/default.png'; // Default image if no profile picture is set
                                    }

                                    // Update the username
                                    document.getElementById('username').textContent = data.username;

                                    // Update the email
                                    const emailElement = document.getElementById('email');
                                    emailElement.textContent = data.email || 'Email not set';
                            
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
                <h2 class="font-bold text-2xl text-gray-400">Privacy & Security</h2>
                <div class="mt-1">
                    <strong class="text-xl text-gray-500">Social Settings</strong>
                    <!-- make a table checklist with 2 columns and one row to start -->
                    <table id="expandedtable" class="table-auto w-full mt-2">
                        <tr id="allow_messagesRow">
                            <td>
                                <input type="checkbox" name="allow_messages" id="allow_messages">
                            </td>
                            <td>
                                <span>Allow messages from other users</span>
                            </td>
                        </tr>
                        <tr id="allow_friend_requestsRow">
                            <td>
                                <input type="checkbox" name="allow_friend_requests" id="allow_friend_requests">
                            </td>
                            <td>
                                <span>Allow friend requests from other users</span>
                            </td>
                        </tr>
                        <tr id="allow_profile_commentsRow">
                            <td>
                                <input type="checkbox" name="allow_profile_comments" id="allow_profile_comments">
                            </td>
                            <td>
                                <span>Allow other users to comment on your comment wall</span>
                            </td>
                        </tr>
                    </table>
                    <small><strong>Because your E-mail is unverified, you are limited from interacting with other Fakefolio users. To gain access, click the link in your inbox from @fakefolio.com or enter the verification code manually on-site.</strong></small>
                </div>
                <script>
                    // When the tr is clicked, toggle the checkbox
                    document.querySelectorAll('#expandedtable tr').forEach(row => {
                        row.addEventListener('click', function() {
                            const checkbox = this.querySelector('input[type="checkbox"]');
                            if (checkbox) {
                                checkbox.checked = !checkbox.checked;
                            }
                        });
                    });
                </script>
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