<?php

include '../config.php'; // Include database connection

// Function to resize and save an image as webp
function resizeAndConvertToWebp($sourcePath, $destinationPath, $width, $height) {
    list($originalWidth, $originalHeight) = getimagesize($sourcePath);
    $newImage = imagecreatetruecolor($width, $height);
    $imageType = exif_imagetype($sourcePath);

    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_WEBP:
            $sourceImage = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }

    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);
    $success = imagewebp($newImage, $destinationPath, 80);

    imagedestroy($newImage);
    imagedestroy($sourceImage);

    return $success;
}

// Function to find the next available image number, reusing deleted slots
function getNextAvailableImageNumber($directory, $prefix) {
    $files = glob($directory . "{$prefix}_*.webp");
    $usedNumbers = [];

    foreach ($files as $file) {
        $filename = basename($file, '.webp');
        preg_match('/' . $prefix . '_(\d+)/', $filename, $matches);
        if (!empty($matches[1])) {
            $usedNumbers[] = (int)$matches[1];
        }
    }

    sort($usedNumbers);
    $nextNumber = 1;

    foreach ($usedNumbers as $num) {
        if ($num == $nextNumber) {
            $nextNumber++;
        } else {
            break;
        }
    }

    return $nextNumber;
}

// Function to count images in a category folder
function countImagesInCategory($directory) {
    $files = glob($directory . "*.webp");
    return count($files);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $baseDirectory = "../../img/gallary/";
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $validCategories = ['hero', 'vision', 'welcome', 'gallary'];

    if (!in_array($category, $validCategories)) {
        echo "Invalid category selected.";
        exit;
    }

    $imageLimits = [
        'hero' => 2,
        'vision' => 3,
        'welcome' => 1,
    ];

    if (isset($imageLimits[$category])) {
        $currentImageCount = countImagesInCategory($baseDirectory . $category . "/");
        if ($currentImageCount >= $imageLimits[$category]) {
            echo "Image limit for the " . ucfirst($category) . " category has been reached.";
            exit;
        }
    }

    $targetDirectory = $baseDirectory . $category . "/";

    if (!file_exists($targetDirectory)) {
        mkdir($targetDirectory, 0777, true);
    }

    $fileName = basename($_FILES["image"]["name"]);
    $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
    $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array(strtolower($fileType), $allowedTypes)) {
        echo "Only JPG, JPEG, PNG, and WEBP file types are allowed.";
        exit;
    }

    switch ($category) {
        case 'hero':
            $width = 1920;
            $height = 800;
            break;
        case 'vision':
            $width = 400;
            $height = 400;
            break;
        case 'welcome':
            $width = 700;
            $height = 700;
            break;
        case 'gallary':
            $mainWidth = 1024;
            $mainHeight = 768;
            $thumbWidth = 100;
            $thumbHeight = 100;
            break;
    }

    if ($category == 'gallary') {
        $nextNumber = getNextAvailableImageNumber($targetDirectory, 'main_gallery');
        $mainImageName = "main_gallery_{$nextNumber}.webp";
        $thumbImageName = "thumb_gallery_{$nextNumber}.webp";

        $mainImagePath = $targetDirectory . $mainImageName;
        $thumbImagePath = $targetDirectory . $thumbImageName;

        if (!resizeAndConvertToWebp($_FILES["image"]["tmp_name"], $mainImagePath, $mainWidth, $mainHeight)) {
            echo "Failed to process the main image.";
            exit;
        }

        if (!resizeAndConvertToWebp($_FILES["image"]["tmp_name"], $thumbImagePath, $thumbWidth, $thumbHeight)) {
            echo "Failed to process the thumbnail image.";
            exit;
        }

        $title = isset($_POST['title']) ? $_POST['title'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $thumbnailPath = $thumbImagePath;
        $fullImagePath = $mainImagePath;

        $sql = "INSERT INTO gallery (thumbnail_path, full_image_path, title, description) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $thumbnailPath, $fullImagePath, $title, $description);

        if ($stmt->execute()) {
            echo "Gallery images uploaded successfully as {$mainImageName} and {$thumbImageName}.";
        } else {
            echo "Failed to save image details to the database.";
        }

        $stmt->close();
        $conn->close();
    } else {
        $nextNumber = getNextAvailableImageNumber($targetDirectory, $category);
        $newFileName = "{$category}_{$nextNumber}.webp";
        $targetFilePath = $targetDirectory . $newFileName;

        if (resizeAndConvertToWebp($_FILES["image"]["tmp_name"], $targetFilePath, $width, $height)) {
            echo ucfirst($category) . " image uploaded successfully as " . $newFileName;
        } else {
            echo "Failed to process the image.";
        }
    }
} else {
    echo "Invalid request method. Please use POST to upload files.";
}

?>
