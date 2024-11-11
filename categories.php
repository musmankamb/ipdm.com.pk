<?php
// Include the database connection file
include 'db.php'; // Ensure this contains $conn as the active DB connection

// Prepare SQL query to fetch `name` and `course_count` from `category` table
$sql = "SELECT id, `name`, `course_count`,image FROM `category`";
$result = $conn->query($sql);

// Initialize an array to store the categories
$categories = [];

// Fetch the result as an associative array
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Add each row to the categories array
        $categories[] = $row;
    }
}

// Set the content type to JSON
header('Content-Type: application/json');

// Output the data as JSON
echo json_encode($categories);

// Close the database connection
$conn->close();
?>
