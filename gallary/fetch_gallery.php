<?php

// Include the db.php file to use the database connection
include '../db.php';

// Fetch gallery images from the database
$query = "SELECT * FROM gallery";
$result = $conn->query($query);

$gallery = [];

while ($row = $result->fetch_assoc()) {
    $gallery[] = $row;
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($gallery);

$conn->close();
?>
