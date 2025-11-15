<?php
    $errors = [
        "password_length" => "Password must be at least 8 characters long.",
        "username_length" => "Username must be 20 characters or fewer.",
        "email_invalid" => "Please enter a valid email address.",
        "terms_unchecked" => "You must agree to the Terms of Service and Privacy Policy.",
        "username_taken" => "This username is already taken. Please choose another.",
        "email_taken" => "An account with this email already exists. Please use a different email.",
        "generic_error" => "An error occurred. Please try again later." 
    ];

    function display_error($error_key) {
        global $errors;
        if (array_key_exists($error_key, $errors)) {
            echo "<strong id='error' class='block text-red-800 font-bold my-3'>" . $errors[$error_key] . "</strong>";
        }
    }

    function find_error_key($error_message) {
        global $errors;
        $key = array_search($error_message, $errors);
        if ($key !== false) {
            return $key;
        } else {
            return null;
        }
    }
?>