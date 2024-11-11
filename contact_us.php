<?php
// Include the database connection file
include 'db.php';

// Define an array to hold validation errors
$response = ['success' => false, 'errors' => []];

// Check if the request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture and sanitize the form data
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $contact = filter_var(trim($_POST['contact']), FILTER_SANITIZE_STRING);
    $message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);

    // Validation: Check if all fields are provided
    if (empty($name)) {
        $response['errors']['name'] = "Name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['errors']['email'] = "A valid email is required.";
    }
    if (empty($contact)) {
        $response['errors']['contact'] = "Contact number is required.";
    }
    if (empty($message)) {
        $response['errors']['message'] = "Message is required.";
    }

    // Proceed if there are no validation errors
    if (empty($response['errors'])) {
        // Prepare the SQL statement to avoid SQL injection
        $stmt = $conn->prepare("INSERT INTO contact_us (name, email, contact, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $contact, $message);

        // Execute the query and check the result
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Message sent successfully!";
        } else {
            $response['errors']['database'] = "There was an error saving your message. Please try again later.";
        }

        // Close the statement and connection
        $stmt->close();
    }

    // Close the database connection
    $conn->close();
}

// Return the response as JSON
echo json_encode($response);
?>
