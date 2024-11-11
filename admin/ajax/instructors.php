<?php
include '../config.php'; // Include database connection


// Function to process and save image as .webp with specified resolution
function processImage($image, $targetDir) {
    list($width, $height) = getimagesize($image['tmp_name']);
    $newWidth = 500;
    $newHeight = 500;

    // Create a new image with the desired dimensions
    $imageResource = imagecreatefromstring(file_get_contents($image['tmp_name']));
    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($resizedImage, $imageResource, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Generate the new file name and set the relative path to be stored in the database
    $fileName = uniqid() . '.webp';
    $filePath = $targetDir . $fileName;
    $relativePath = 'img/instructors/' . $fileName; // Store relative path

    // Save the image as .webp format
    if (imagewebp($resizedImage, $filePath, 80)) {
        // Free memory
        imagedestroy($imageResource);
        imagedestroy($resizedImage);
        return $relativePath;  // Return the relative path to store in the database
    } else {
    
        return null;
    }
}

// Function to detect the platform based on the URL
function detectPlatform($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "https://$url";  // Default to https:// if no scheme is provided
    }
    if (strpos($url, 'facebook.com') !== false) {
        return "<a href='$url' target='_blank'><i class='fab fa-facebook'></i></a>";
    } elseif (strpos($url, 'youtube.com') !== false) {
        return "<a href='$url' target='_blank'><i class='fab fa-youtube'></i></a>";
    } elseif (strpos($url, 'instagram.com') !== false) {
        return "<a href='$url' target='_blank'><i class='fab fa-instagram'></i></a>";
    } elseif (strpos($url, 'linkedin.com') !== false) {
        return "<a href='$url' target='_blank'><i class='fab fa-linkedin'></i></a>";
    } elseif (strpos($url, 'tiktok.com') !== false) {
        return "<a href='$url' target='_blank'><i class='fab fa-tiktok'></i></a>";
    } else {
        return "<a href='$url' target='_blank'><i class='fas fa-link'></i></a>"; // General link icon for other platforms
    }
}

// Fetch instructors
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $result = $conn->query("SELECT id, name, bio, image, status, social FROM instructors");
    
    while ($row = $result->fetch_assoc()) {
        $statusText = $row['status'] ? 'Active' : 'Inactive';
        $socialLinks = explode(';', $row['social']);
        $socialIcons = '';

        foreach ($socialLinks as $link) {
            $socialIcons .= detectPlatform($link) . ' ';
        }

        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['bio']}</td>
            <td><img src='../../{$row['image']}' alt='Image' width='100'></td>
            <td>{$statusText}</td>
            <td>{$socialIcons}</td>
            <td>
                <button class='btn btn-warning edit-instructor' data-id='{$row['id']}'
                        data-name='{$row['name']}'
                        data-bio='{$row['bio']}'
                        data-image='../../{$row['image']}'
                        data-status='{$row['status']}'
                        data-social='{$row['social']}'>Edit</button>
                <button class='btn btn-danger delete-instructor' data-id='{$row['id']}'>Delete</button>
                <button class='btn btn-success toggle-status' data-id='{$row['id']}'>Toggle Status</button>
            </td>
        </tr>";
    }


}

// Add/Edit instructor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete']) && !isset($_POST['toggle_status'])) {
    $id = isset($_POST['id']) && !empty($_POST['id']) ? $_POST['id'] : null;
    $name = $_POST['name'];
    $bio = $_POST['bio'];
    $status = $_POST['status'];
    $socialLinks = $_POST['socialLinks'] ?? '';
    $image = '';

    // Handle image upload
    $targetDir = "../../img/instructors/";
    if (isset($_FILES['instructorImage']) && $_FILES['instructorImage']['error'] == 0) {
        // Process the new image if uploaded
        $image = processImage($_FILES['instructorImage'], $targetDir);
    } else {
        // If no new image uploaded, retain the old image from the database (edit mode)
        if ($id) {
            // Fetch the existing image path from the database
            $stmt = $conn->prepare("SELECT image FROM instructors WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->bind_result($existingImage);
            $stmt->fetch();
            $stmt->close();
            $image = $existingImage;  // Retain the existing image
        }
    }

    if ($id) {
        // Update existing instructor
        $stmt = $conn->prepare("UPDATE instructors SET name = ?, bio = ?, image = ?, status = ?, social = ? WHERE id = ?");
        $stmt->bind_param('sssisi', $name, $bio, $image, $status, $socialLinks, $id);
    
    } else {
        // Insert new instructor
        $stmt = $conn->prepare("INSERT INTO instructors (name, bio, image, status, social) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssis', $name, $bio, $image, $status, $socialLinks);
    
    }

    $stmt->execute();
    $stmt->close();
}

// Delete instructor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM instructors WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();


}

// Toggle status (Active/Inactive)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_status'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("UPDATE instructors SET status = NOT status WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

}
?>
