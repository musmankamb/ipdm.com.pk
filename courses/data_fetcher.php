<?php
// Enable error reporting for debugging (comment out in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the db.php file to use the database connection
include '../db.php';

// Function to fetch courses with optional filtering
function fetchCourses($conn, $categoryId = null, $instructorName = null, $sortPrice = null, $searchQuery = null) {
    $sql = "SELECT 
                courses.title, 
                courses.description, 
                courses.price, 
                courses.image as image,
                courses.schedule AS schedule, 
                courses.batch AS batch,
                instructors.name AS instructor_name, 
                instructors.bio AS instructor_bio, 
                instructors.image AS instructor_image, 
                category.name AS cname,
                category.id AS category_id
            FROM courses 
            JOIN instructors ON courses.instructor_id = instructors.id
            JOIN category ON category.id = courses.category_id
            WHERE courses.popular = '1'";

    // Apply category filtering if provided
    if ($categoryId) {
        $sql .= " AND category.id = '" . $conn->real_escape_string($categoryId) . "'";
    }

    // Apply instructor filtering if provided
    if ($instructorName) {
        $sql .= " AND instructors.name = '" . $conn->real_escape_string($instructorName) . "'";
    }

    // Apply search query if provided
    if ($searchQuery) {
        $searchQuery = $conn->real_escape_string($searchQuery);
        $sql .= " AND (courses.title LIKE '%$searchQuery%' OR courses.description LIKE '%$searchQuery%')";
    }

    // Apply sorting by price if provided
    if ($sortPrice) {
        $sql .= $sortPrice === 'asc' ? " ORDER BY courses.price ASC" : " ORDER BY courses.price DESC";
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

// Function to fetch instructors
function fetchInstructors($conn) {
    $sql = "SELECT `name`, `bio`, `image`, social, `created_at` FROM `instructors` WHERE status = '1'";
    $result = $conn->query($sql);

    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return ['error' => $conn->error];
    }
}

// Function to fetch categories
function fetchCategories($conn) {
    $sql = "SELECT id, `name`, `course_count`, image FROM `category`";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

// Determine the type of request and parameters
$action = isset($_GET['action']) ? $_GET['action'] : '';
$categoryId = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$instructorName = isset($_GET['instructor_name']) ? $_GET['instructor_name'] : null;
$sortPrice = isset($_GET['sort_price']) ? $_GET['sort_price'] : null;
$searchQuery = isset($_GET['search_query']) ? $_GET['search_query'] : null;

// Handle the request based on the action parameter
if ($action == 'courses') {
    // Fetch courses with optional filters and sorting
    $courses = fetchCourses($conn, $categoryId, $instructorName, $sortPrice, $searchQuery);
    header('Content-Type: application/json');
    echo json_encode($courses);
} elseif ($action == 'instructors') {
    // Fetch instructors
    $instructors = fetchInstructors($conn);
    header('Content-Type: application/json');
    echo json_encode($instructors);
} elseif ($action == 'categories') {
    // Fetch categories
    $categories = fetchCategories($conn);
    header('Content-Type: application/json');
    echo json_encode($categories);
} else {
    // Invalid action
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid action']);
}

// Close the database connection
$conn->close();
?>
