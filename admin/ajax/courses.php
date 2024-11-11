<?php
include '../config.php'; // Include your database connection

// Fetch courses
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $result = $conn->query("SELECT courses.*, instructors.name AS instructor_name, category.name AS category_name FROM courses 
                            JOIN instructors ON courses.instructor_id = instructors.id
                            JOIN category ON courses.category_id = category.id ORDER by id ASC");
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['title']}</td>
                <td>{$row['schedule']}</td>
                <td>{$row['description']}</td>
                <td>{$row['price']}</td>
                <td>{$row['instructor_name']}</td>
                <td>{$row['category_name']}</td>
                <td><img src='../../{$row['image']}' alt='Image' width='100'></td>
                <td>{$row['popular']}</td>
                <td>
                    <button class='btn btn-warning edit-course' 
                        data-id='{$row['id']}' 
                        data-title='{$row['title']}'
                        data-description='{$row['description']}'
                        data-price='{$row['price']}'
                        data-instructor-id='{$row['instructor_id']}'
                        data-category-id='{$row['category_id']}'
                        data-image='{$row['image']}'
                        data-popular='{$row['popular']}'
                        data-schedule='{$row['schedule']}'>
                        <i class='fas fa-edit'></i> Edit
                    </button>
                    <button class='btn btn-danger delete-course' data-id='{$row['id']}'>
                        <i class='fas fa-trash-alt'></i> Delete
                    </button>
                    <button class='btn btn-success toggle-status' data-id='{$row['id']}'>
                        Popular
                    </button>
                </td>
              </tr>";
    }
}

// Function to resize and save image
function resizeImage($source, $destination) {
    // Set the fixed width and height to 400x400
    $newWidth = 400;
    $newHeight = 400;

    // Get original image dimensions and type
    list($width, $height, $imageType) = getimagesize($source);

    // Create an image resource from the source based on the image type
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($source);
            break;
        case IMAGETYPE_WEBP:
            $image = imagecreatefromwebp($source);
            break;
        default:
            throw new Exception('Unsupported image type.');
    }

    // Create a blank true color image for the resized version (400x400)
    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

    // Handle transparency for PNG and GIF images
    if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
        imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);
    }

    // Resample the image to the fixed size of 400x400
    imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Convert and save the resized image as WebP format
    imagewebp($resizedImage, $destination);

    // Clean up resources
    imagedestroy($image);
    imagedestroy($resizedImage);
}

// Add/Edit course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete']) && !isset($_POST['toggle_status'])) {
    $id = $_POST['id'] ?? null;
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $instructorId = $_POST['instructorId'];
    $categoryId = $_POST['categoryId'];
    $popular = $_POST['coursePopular']; // 1 or 0
    $schedule = $_POST['scheduleDate']; // Schedule date field

    // Handle image upload
    if (isset($_FILES['courseImage']) && $_FILES['courseImage']['error'] == 0) {
        // Set the target directory
        $targetDir = '../../img/courses/';
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true); // Create the directory if it doesn't exist
        }

        // Generate a unique filename
        $fileName = uniqid() . '.webp';
        $targetFile = $targetDir . $fileName;

        // Move the uploaded file temporarily
        $tempFile = $_FILES['courseImage']['tmp_name'];
        move_uploaded_file($tempFile, $targetFile);

        // Resize the image to 400px width
        $resizedImagePath = $targetDir . $fileName;
        resizeImage($targetFile, $resizedImagePath);

        // Final image path for the database (relative path)
        $imagePath = 'img/courses/' . $fileName;
    } else {
        if ($id) {
            // If no new image uploaded, retain the old image from the database (edit mode)
            $stmt = $conn->prepare("SELECT image FROM courses WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->bind_result($existingImage);
            $stmt->fetch();
            $stmt->close();
            $imagePath = $existingImage;  // Retain the existing image
        } else {
            $imagePath = ''; // Handle if no image is uploaded
        }
    }

   
if ($id) {
    // Update course
    // First, get the old category_id of the course before updating
    $oldCategoryStmt = $conn->prepare("SELECT category_id FROM courses WHERE id = ?");
    $oldCategoryStmt->bind_param('i', $id);
    $oldCategoryStmt->execute();
    $oldCategoryStmt->bind_result($oldCategoryId);
    $oldCategoryStmt->fetch();
    $oldCategoryStmt->close();

    // Update course details
    $stmt = $conn->prepare("UPDATE courses SET title = ?, description = ?, price = ?, instructor_id = ?, category_id = ?, image = ?, popular = ?, schedule = ? WHERE id = ?");
    $stmt->bind_param('ssdiisssi', $title, $description, $price, $instructorId, $categoryId, $imagePath, $popular, $schedule, $id);
    $stmt->execute();
    
    // If the category has changed, update the course_count in both categories
    if ($categoryId != $oldCategoryId) {
        // Decrement the old category's course_count
        $decrementOldCategoryStmt = $conn->prepare("UPDATE category SET course_count = course_count - 1 WHERE id = ?");
        $decrementOldCategoryStmt->bind_param('i', $oldCategoryId);
        $decrementOldCategoryStmt->execute();
        $decrementOldCategoryStmt->close();

        // Increment the new category's course_count
        $incrementNewCategoryStmt = $conn->prepare("UPDATE category SET course_count = course_count + 1 WHERE id = ?");
        $incrementNewCategoryStmt->bind_param('i', $categoryId);
        $incrementNewCategoryStmt->execute();
        $incrementNewCategoryStmt->close();
    }
} else {
    // Add new course
    $stmt = $conn->prepare("INSERT INTO courses (title, description, price, instructor_id, category_id, image, popular, schedule) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssdiisss', $title, $description, $price, $instructorId, $categoryId, $imagePath, $popular, $schedule);
    $stmt->execute();

    // Increment the course_count in the category after inserting a new course
    $incrementCategoryStmt = $conn->prepare("UPDATE category SET course_count = course_count + 1 WHERE id = ?");
    $incrementCategoryStmt->bind_param('i', $categoryId);
    $incrementCategoryStmt->execute();
    $incrementCategoryStmt->close();
}

// Close the main statement
$stmt->close();

}

// Delete course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $id = $_POST['id'];

    // Step 1: Fetch the category_id of the course before deletion
    $getCategoryStmt = $conn->prepare("SELECT category_id FROM courses WHERE id = ?");
    $getCategoryStmt->bind_param('i', $id);
    $getCategoryStmt->execute();
    $getCategoryStmt->bind_result($categoryId);
    $getCategoryStmt->fetch();
    $getCategoryStmt->close();

    // Step 2: Delete the course from the database
    $deleteStmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $deleteStmt->bind_param('i', $id);
    $deleteStmt->execute();
    $deleteStmt->close();

    // Step 3: Decrement the course_count in the category table
    $decrementCategoryStmt = $conn->prepare("UPDATE category SET course_count = course_count - 1 WHERE id = ?");
    $decrementCategoryStmt->bind_param('i', $categoryId);
    $decrementCategoryStmt->execute();
    $decrementCategoryStmt->close();
}

// Toggle status (Active/Inactive)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_status'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("UPDATE courses SET popular = NOT popular WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}
?>
