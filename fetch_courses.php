<?php
// Database connection
require_once 'db.php'; // Ensure you have a db.php file for database connection
header('Content-Type: application/json');

// Check if connection was successful
if ($conn->connect_error) {
    // Return JSON response for connection error
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Query to get course details
$query = "SELECT c.id, c.title, c.description, c.price, i.name as iname, c.schedule FROM courses c INNER JOIN instructors i ON i.id=c.instructor_id";

// Execute query and handle errors
if ($result = $conn->query($query)) {
    $courses = [];
    
    // Fetch data
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    
    // Return JSON-encoded data
    echo json_encode($courses);
    
    // Free result set
    $result->free();
} else {
    // Return JSON response for query error
    echo json_encode(['error' => 'Query failed: ' . $conn->error]);
}
//var_dump($courses);
// Close database connection
$conn->close();
?>
