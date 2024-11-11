<?php
// Start the session
session_start();

// Generate random numbers for the security question
$num1 = rand(1, 10);
$num2 = rand(1, 10);

// Store the numbers in the session
$_SESSION['num1'] = $num1;
$_SESSION['num2'] = $num2;

// Return the random numbers as a JSON response
echo json_encode(['num1' => $num1, 'num2' => $num2]);
?>
