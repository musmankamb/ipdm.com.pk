<?php
// Include database connection
include 'db.php';

// Query to fetch events
$sql = "SELECT title, image, location, description, start_time, end_time, start_date, end_date FROM events ORDER by id ASC limit 4";
$result = $conn->query($sql);

$events = [];

if ($result->num_rows > 0) {
    // Fetch all rows into an associative array
    while($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

// Return the events data as JSON
header('Content-Type: application/json');
echo json_encode($events);

// Close the database connection
$conn->close();
?>
