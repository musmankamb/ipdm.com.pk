<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $baseDirectory = "../../img/gallary/";

    // Retrieve and validate the category
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $validCategories = ['hero', 'vision', 'welcome', 'gallary'];

    if (!in_array($category, $validCategories)) {
        echo "Invalid category selected.";
        exit;
    }

    // Set the target directory for the specified category
    $targetDirectory = $baseDirectory . $category . "/";

    if (!file_exists($targetDirectory)) {
        mkdir($targetDirectory, 0777, true);
    }

    $oldImage = isset($_POST['old_image']) ? $_POST['old_image'] : '';
    $fileType = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);

    // Delete the old image if it exists
    if ($oldImage && file_exists($oldImage)) {
        unlink($oldImage);
    }

    // Generate a new unique name for the image, retaining the original file extension
    $newFileName = pathinfo($oldImage, PATHINFO_FILENAME) . '.' . $fileType;
    $targetFilePath = $targetDirectory . $newFileName;

    // Allowed file types
    $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];
    
    // Validate file type and move the uploaded file to the target directory
    if (in_array(strtolower($fileType), $allowedTypes)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            echo "Image updated successfully!";
        } else {
            echo "Failed to update the image.";
        }
    } else {
        echo "Invalid file type. Only JPG, JPEG, PNG, and WEBP are allowed.";
    }
} else {
    echo "Invalid request method. Please use POST to upload files.";
}
