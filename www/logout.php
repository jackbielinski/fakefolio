<?php
    include './include/backend/main.php';

    // Destroy session and redirect to login
    session_unset();
    session_destroy();
    header("Location: ./login");
    exit();
?>