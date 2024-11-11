<?php
include '../config.php'; // Include your database connection

// Function to resize and save image as webp
function resizeImage($file, $targetDir, $newWidth, $newHeight, $fileName) {
    list($width, $height, $type) = getimagesize($file);
    $src = imagecreatefromstring(file_get_contents($file));

    $tmp = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    $webpPath = $targetDir . $fileName . '.webp';
    imagewebp($tmp, $webpPath, 80); // Save image as webp with 80% quality

    imagedestroy($src);
    imagedestroy($tmp);

    return $webpPath;
}

// Fetch categories (GET request)
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $result = $conn->query("SELECT * FROM category");
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['course_count']}</td>
                <td><img src='../../{$row['image']}' alt='Category Image' style='width: 50px; height: auto;' /></td>
                <td>
                    <button class='btn btn-warning edit-category' data-id='{$row['id']}' data-name='{$row['name']}' data-count='{$row['course_count']}' data-image='../../{$row['image']}'>
                        <i class='fas fa-edit'></i> Edit
                    </button>
                    <button class='btn btn-danger delete-category' data-id='{$row['id']}'>
                        <i class='fas fa-trash-alt'></i> Delete
                    </button>
                </td>
              </tr>";
    }
}

// Add/Edit category (POST request)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $count = $_POST['count'];

    // Handle image upload if provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageFileName = uniqid(); // Generate a unique name for the image
        $targetDir = '../../img/category/';

        // Resize and save the image
        $imagePath = resizeImage($_FILES['image']['tmp_name'], $targetDir, 600, 400, $imageFileName);

        // Store only the relative path for the database
        $relativeImagePath = 'img/category/' . $imageFileName . '.webp';
    }

    if ($id) {
        // Edit existing category
        $sql = "UPDATE category SET name = ?, course_count = ?";
        if (isset($relativeImagePath)) {
            $sql .= ", image = ?";
        }
        $sql .= " WHERE id = ?";

        $stmt = $conn->prepare($sql);
        if (isset($relativeImagePath)) {
            $stmt->bind_param('sisi', $name, $count, $relativeImagePath, $id);
        } else {
            $stmt->bind_param('sii', $name, $count, $id);
        }
    } else {
        // Add new category
        $count='0';
        $stmt = $conn->prepare("INSERT INTO category (name, course_count, image) VALUES (?, ?, ?)");
        $stmt->bind_param('sis', $name, $count, $relativeImagePath);
    }

    $stmt->execute();
    $stmt->close();
}

// Delete category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM category WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}


?>