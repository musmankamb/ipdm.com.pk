<?php
// Include the database connection file
include 'db.php';  // Assumes you have a db.php file with a $conn mysqli connection

// Set the charset to UTF-8 to handle special characters correctly
$conn->set_charset('utf8mb4');

// Query to fetch testimonials from the database
$sql = "SELECT `name`, `profession`, `image`, `testimonial_text` FROM `testimonials` WHERE approved = '1'";

if ($result = $conn->query($sql)) {
    $testimonials = [];

    // Fetch all results as an associative array
    while ($row = $result->fetch_assoc()) {
        // Sanitize the testimonial_text to prevent issues with special characters
        $row['testimonial_text'] = htmlspecialchars($row['testimonial_text'], ENT_QUOTES, 'UTF-8');
        $testimonials[] = $row;
    }

    // Return the data as JSON, ensuring no unnecessary escaping of Unicode and slashes
    echo json_encode($testimonials, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} else {
    // Return the error as a JSON response
    echo json_encode(['error' => $conn->error]);
}

// Close the database connection
$conn->close();
?>
