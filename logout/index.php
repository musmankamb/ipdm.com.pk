<?php
// Start the session to access session data
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page with a query parameter indicating session expiration
header("Location: ../login?session=closed");
exit;
?>
