<?php
include '../config.php'; // Include your database connection

// Fetch categories
$result = $conn->query("SELECT * FROM category");
while ($row = $result->fetch_assoc()) {
    echo "<option value='{$row['id']}'>{$row['name']}</option>";
}
?>