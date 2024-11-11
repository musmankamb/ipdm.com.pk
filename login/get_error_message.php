<?php
// Start the session
session_start();

// Initialize response array
$response = ['error' => '', 'success' => ''];

// Check for error message in the session
if (isset($_SESSION['error'])) {
    $response['error'] = $_SESSION['error'];
    unset($_SESSION['error']); // Clear the error after showing it
}

// Check for success message in the session (if applicable)
if (isset($_SESSION['success'])) {
    $response['success'] = $_SESSION['success'];
    unset($_SESSION['success']); // Clear the success message after showing it
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
