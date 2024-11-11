<?php
include '../config.php'; // Include database connection


// Fetch testimonials
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $result = $conn->query("SELECT id, name, profession, image, testimonial_text, created_at, approved FROM testimonials");

    while ($row = $result->fetch_assoc()) {
        $approvedStatus = $row['approved'] ? 'Approved' : 'Pending';

        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['profession']}</td>
            <td><img src='../../{$row['image']}' alt='Image' width='100'></td>
            <td>{$row['testimonial_text']}</td>
            <td>{$row['created_at']}</td>
            <td>{$approvedStatus}</td>
            <td>
                <button class='btn btn-warning edit-testimonial' data-id='{$row['id']}' 
                        data-name='{$row['name']}' 
                        data-profession='{$row['profession']}' 
                        data-image='{$row['image']}' 
                        data-testimonial-text='{$row['testimonial_text']}'>Edit</button>
                <button class='btn btn-danger delete-testimonial' data-id='{$row['id']}'>Delete</button>
                <button class='btn btn-success approve-testimonial' data-id='{$row['id']}' ".($row['approved'] ? 'disabled' : '').">Approve</button>
            </td>
        </tr>";
    }
}

// Function to process and save image as .webp with specified resolution
function processImage($image, $targetDir) {
    list($width, $height) = getimagesize($image['tmp_name']);
    $newWidth = 100;
    $newHeight = 100;

    // Create a new image with the desired dimensions
    $imageResource = imagecreatefromstring(file_get_contents($image['tmp_name']));
    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($resizedImage, $imageResource, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Generate the new file name and set the relative path to be stored in the database
    $fileName = uniqid() . '.webp';
    $filePath = $targetDir . $fileName;
    $relativePath = 'img/testimonial/' . $fileName; // Store relative path

    // Save the image as .webp format
    if (imagewebp($resizedImage, $filePath, 80)) {
        // Free memory
        imagedestroy($imageResource);
        imagedestroy($resizedImage);
        return $relativePath;  // Return the relative path to store in the database
    } else {
        // Log error if image processing fails
        return null;
    }
}

// Handle Add/Edit testimonials
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete']) && !isset($_POST['approve'])) {
    $id = $_POST['id'] ?? null;  // Check if ID is set (this means it's an edit)
    $name = $_POST['name'] ?? '';  // Get the name field
    $profession = $_POST['profession'] ?? '';  // Get the profession field
    $testimonialText = $_POST['testimonialText'] ?? '';  // Get the testimonial text

    // Validate if the necessary fields are set, if not, handle errors
    if (empty($name) || empty($profession) || empty($testimonialText)) {
        echo json_encode(['error' => 'All fields are required.']);
        exit;
    }

    // Handle image upload
    $targetDir = "../../img/testimonial/";
    if (isset($_FILES['testimonialImage']) && $_FILES['testimonialImage']['error'] == 0) {
        // Process the new image if uploaded
        $image = processImage($_FILES['testimonialImage'], $targetDir);
    } else {
        // If no new image uploaded, retain the old image from the database (edit mode)
        if ($id) {
            // Fetch the existing image path from the database
            $stmt = $conn->prepare("SELECT image FROM testimonials WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->bind_result($existingImage);
            $stmt->fetch();
            $stmt->close();
            $image = $existingImage;  // Retain the existing image
        } else {
            $image = null;  // No image for a new testimonial
        }
    }

    // Determine if this is an update or an insert
    if ($id) {
        // Update existing testimonial
        $stmt = $conn->prepare("UPDATE testimonials SET name = ?, profession = ?, image = ?, testimonial_text = ? WHERE id = ?");
        $stmt->bind_param('ssssi', $name, $profession, $image, $testimonialText, $id);

     
    } else {
        // Add new testimonial
        $stmt = $conn->prepare("INSERT INTO testimonials (name, profession, image, testimonial_text, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param('ssss', $name, $profession, $image, $testimonialText);

    }

    // Execute the query and check for errors
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => $stmt->error]);  // Output error message if something goes wrong
    }

    $stmt->close();
}

// Approve testimonial
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("UPDATE testimonials SET approved = 1 WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

// Delete testimonial
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM testimonials WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

?>
