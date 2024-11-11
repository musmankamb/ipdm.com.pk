<?php
include '../db.php';
session_start();

$response = ['errors' => []];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $profession = trim($_POST['profession']);
    $testimonial_text = trim($_POST['testimonial_text']);
    $image = $_FILES['image'];

    // Validation
    if (empty($name)) {
        $response['errors'][] = 'Name is required.';
    }
    if (empty($profession)) {
        $response['errors'][] = 'Profession is required.';
    }
    if (empty($testimonial_text)) {
        $response['errors'][] = 'Testimonial text is required.';
    }

    // Image validation
    if ($image['error'] == 0) {
        $target_dir = "../img/testimonial/";
        $image_name = basename($image['name']);
        $target_file = $target_dir . time() . "_" . $image_name;
        $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($image_type, $allowed_types)) {
            $response['errors'][] = "Invalid image format. Only JPG, JPEG, PNG, and GIF are allowed.";
        } else {
            if (!move_uploaded_file($image['tmp_name'], $target_file)) {
                $response['errors'][] = "Failed to upload image.";
            }
        }
    }

    // If no errors, insert into the database
    if (empty($response['errors'])) {
        $insert_query = "INSERT INTO testimonials (user_id, name, profession, testimonial_text, image, created_at, approved) 
                         VALUES (?, ?, ?, ?, ?, NOW(), 0)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("issss", $user_id, $name, $profession, $testimonial_text, $target_file);

        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['errors'][] = 'Failed to submit testimonial.';
        }
    }

    echo json_encode($response);
}
?>
