<?php

// Include the db.php file to use the database connection
include 'db.php';

// Query to fetch course details and join with instructors
$sql = "SELECT 
            courses.title, 
            courses.description, 
            courses.price, courses.image as image,
            courses.schedule AS schedule, 
            courses.batch AS batch,
            instructors.name AS instructor_name, 
            instructors.bio AS instructor_bio, 
            instructors.image AS instructor_image, 
            category.name AS cname
        FROM courses 
        JOIN instructors ON courses.instructor_id = instructors.id
        JOIN category ON category.id = courses.category_id
        where courses.popular = '1' ORDER by courses.id ASC LIMIT 4";

$result = $conn->query($sql);

// Check if the query was successful
if ($result->num_rows > 0) {
    // Fetch all courses
    $courses = $result->fetch_all(MYSQLI_ASSOC);
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($courses);
} else {
    // If no courses were found, return an empty array
    echo json_encode([]);
}

// Close the database connection
$conn->close();
?>
