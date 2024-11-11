<?php
include 'db.php'; // Database connection

$categoryId = $_GET['id'];

// Fetch category details
$categoryStmt = $conn->prepare("SELECT id, name, course_count FROM category WHERE id = ?");
$categoryStmt->bind_param('i', $categoryId);
$categoryStmt->execute();
$categoryResult = $categoryStmt->get_result();
$category = $categoryResult->fetch_assoc();
$categoryStmt->close();

// Fetch courses for the category
$coursesStmt = $conn->prepare("SELECT id, title, description, price, instructor_id, created_at, category_id, schedule, batch FROM courses WHERE category_id = ?");
$coursesStmt->bind_param('i', $categoryId);
$coursesStmt->execute();
$coursesResult = $coursesStmt->get_result();
$courses = $coursesResult->fetch_all(MYSQLI_ASSOC);
$coursesStmt->close();

// Prepare the data to be returned as JSON
$response = [
    'category' => $category,
    'courses' => $courses
];

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
