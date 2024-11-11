<?php
include '../config.php'; // Include your database connection

// Fetch instructors
$result = $conn->query("SELECT id, name FROM instructors WHERE status = 1"); // Fetch active instructors

// Generate <option> elements for the dropdown
while ($row = $result->fetch_assoc()) {
    echo "<option value='{$row['id']}'>{$row['name']}</option>";
}

$conn->close();
?>
