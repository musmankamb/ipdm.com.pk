<?php
session_start();

// Set session timeout duration (15 minutes)
$timeout_duration = 900; // 15 minutes = 900 seconds

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to login page
    header("Location: ../../login?auth=required");
    exit;
}

// Check if the session has expired due to inactivity
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    // Session has expired, unset and destroy session
    session_unset();
    session_destroy();
    header("Location: ../../login?session=expired");
    exit;
}

// Update last activity timestamp
$_SESSION['LAST_ACTIVITY'] = time(); // Reset last activity time to now
?>
