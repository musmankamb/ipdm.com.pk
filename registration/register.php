<?php
// Include database connection
include('../db.php');

// Start the session to access session variables
session_start();

// Set response header for JSON
header('Content-Type: application/json');

$response = [
    "success" => false, // Default response
    "message" => ""
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs and sanitize them
    $name = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = htmlspecialchars($_POST['password']);
    $confirm_password = htmlspecialchars($_POST['confirm_password']);
    $security_answer = filter_var($_POST['security_answer'], FILTER_SANITIZE_NUMBER_INT);

    // Check if passwords match
    if ($password != $confirm_password) {
        $response['message'] = "Passwords do not match.";
        echo json_encode($response);
        exit;
    }

    // Validate the security question
    if (isset($_SESSION['num1']) && isset($_SESSION['num2'])) {
        $num1 = $_SESSION['num1']; 
        $num2 = $_SESSION['num2']; 
        $correct_answer = $num1 + $num2;

        if ((int)$security_answer !== (int)$correct_answer) {
            $response['message'] = "Incorrect answer to the security question.";
            echo json_encode($response);
            exit;
        }
    } else {
        $response['message'] = "Security question is missing. Please reload the page.";
        echo json_encode($response);
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response['message'] = "Email already registered.";
        echo json_encode($response);
        exit;
    }

    // Insert the user into the database
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Registration successful.";
    } else {
        $response['message'] = "Error: Could not register.";
    }

    // Close the prepared statement and connection
    $stmt->close();
    $conn->close();
    
    // Return JSON response
    echo json_encode($response);
}
?>
