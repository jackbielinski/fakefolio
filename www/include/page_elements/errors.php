<?php
    $errors = [
        // General errors
        "generic_error" => "An error occurred. Please try again later.",
        "rate_limit" => "You are sending requests too quickly. Please slow down and try again later.",
        // Registration errors
        "password_length" => "Password must be at least 8 characters long.",
        "username_length" => "Username must be 20 characters or fewer.",
        "email_invalid" => "Please enter a valid email address.",
        "terms_unchecked" => "You must agree to the Terms of Service and Privacy Policy.",
        "username_taken" => "This username is already taken. Please choose another.",
        "email_taken" => "An account with this email already exists. Please use a different email.",
        // Login errors
        "invalid_credentials" => "Invalid username/email or password.",
        // Edit profile errors
        "invalid_avatar_type" => "Invalid avatar file type. Please upload a PNG or JPEG image under 100KB in size.",
        "upload_error" => "An error occurred while uploading the avatar. Please try again.",
        "save_failed" => "Failed to save profile changes. Please try again.",
        "invalid_banner_type" => "Invalid banner file type. Please upload a PNG or JPEG image under 2MB in size.",
        "banner_too_large" => "Banner image exceeds the maximum size of 2MB.",
        "mkdir_failed" => "Failed to create directory for banner uploads. Please contact support.",
        // Page protected errors
        "required" => "You must be logged in to access this page."
    ];

    function display_error($error_key) {
        global $errors;
        if (array_key_exists($error_key, $errors)) {
            echo "<strong id='error' class='block text-red-800 font-bold my-3'>" . $errors[$error_key] . "</strong>";
        }
    }

    function find_error_key($key)
    {
        global $errors;
        return $errors[$key] ?? "An unknown error occurred.";
    }
?>