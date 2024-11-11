<?php
// Include the database connection file
require_once 'db.php';

// Query to fetch instructors from the database
$sql = "SELECT `name`, `bio`, `image`, social, `created_at` FROM `instructors` WHERE status = '1' limit 4";

if ($result = $conn->query($sql)) {
    $instructors = [];

    // Fetch all results as an associative array
    while ($row = $result->fetch_assoc()) {
        $instructors[] = $row;
    }

    // Return the data as JSON
    echo json_encode($instructors);
} else {
    // Return the error as a JSON response
    echo json_encode(['error' => $conn->error]);
}

// Close the database connection
$conn->close();
?>
