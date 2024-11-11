<?php
include '../config.php';  // Database connection

$result = $conn->query("SELECT id, title FROM courses");

$options = "<option value=''>Select Course</option>";  // Default option

while ($row = $result->fetch_assoc()) {
    $options .= "<option value='{$row['title']}'>{$row['title']}</option>";
}

echo $options;
?>
