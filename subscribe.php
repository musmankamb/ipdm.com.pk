<?php
// Include the database connection
include 'db.php'; // Assumes 'db.php' contains $conn = new mysqli(...) or similar

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the email from the request
    $email = $_POST['email'];
    $date_created = date('Y-m-d H:i:s'); // Get the current date and time

    // Validate the email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    // Prepare the SQL statement to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO subscribe (`email`, `date_created`) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $date_created);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Thank you for subscribing!";
    } else {
        echo "There was an error. Please try again.";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
