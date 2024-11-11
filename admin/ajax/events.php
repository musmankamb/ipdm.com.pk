<?php
include '../config.php'; // Include your database connection

// Function to process and save image as .webp with specified resolution
function processImage($image, $targetDir) {
    list($width, $height) = getimagesize($image['tmp_name']);
    $newWidth = 360;
    $newHeight = 220;

    // Create a new image with the desired dimensions
    $imageResource = imagecreatefromstring(file_get_contents($image['tmp_name']));
    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($resizedImage, $imageResource, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Generate the new file name
    $fileName = uniqid() . '.webp';
    $filePath = $targetDir . $fileName;

    // Save the image as .webp format
    imagewebp($resizedImage, $filePath, 80);

    // Free memory
    imagedestroy($imageResource);
    imagedestroy($resizedImage);

    return $fileName;
}

// Fetch events
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $result = $conn->query("SELECT * FROM events ORDER by id ASC");
        while ($row = $result->fetch_assoc()) {
            $startDate = (new DateTime($row['start_date']))->format('d-m-Y');
            $endDate = (new DateTime($row['end_date']))->format('d-m-Y');
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['title']}</td>
                    <td><img src='../../img/events/{$row['image']}' alt='Event Image' width='100'></td>
                    <td>{$row['start_time']} - {$row['end_time']}</td>
                    <td>{$row['location']}</td>
                     <td>{$startDate} to {$endDate}</td> <!-- Formatted date -->
                    <td>{$row['description']}</td> <!-- Displaying description -->
                    <td>
                        <button class='btn btn-warning edit-event' 
                            data-id='{$row['id']}'
                            data-title='{$row['title']}'
                            data-image='{$row['image']}'
                            data-start-time='{$row['start_time']}'
                            data-end-time='{$row['end_time']}'
                            data-start-date='{$row['start_date']}'
                            data-end-date='{$row['end_date']}'
                            data-location='{$row['location']}'
                            data-description='{$row['description']}'> <!-- Added description for editing -->
                            <i class='fas fa-edit'></i> Edit
                        </button>
                        <button class='btn btn-danger delete-event' data-id='{$row['id']}'>
                            <i class='fas fa-trash-alt'></i> Delete
                        </button>
                    </td>
                  </tr>";
        }
    } catch (Exception $e) {
        echo "Error fetching events";  // Custom error message
    }
}

// Add/Edit event
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete'])) {
    $id = $_POST['id'] ?? null;
    $title = $_POST['title'];
    $description = $_POST['description'];  // Added description
    $startTime = $_POST['startTime'];  // Start Time
    $endTime = $_POST['endTime'];      // End Time
    $dateRange = $_POST['dateRange'];  // Date range in the format "YYYY-MM-DD to YYYY-MM-DD"
    $location = $_POST['location'];

    // Extract start and end date from the date range
    list($startDate, $endDate) = explode(" to ", $dateRange);

    $targetDir = "../../img/events/";

    try {
        // Process the image if uploaded
        if (isset($_FILES['eventImage']) && $_FILES['eventImage']['error'] == 0) {
            $image = processImage($_FILES['eventImage'], $targetDir);
        } else {
            $image = $_POST['image'] ?? null;
        }

        // Check if we are adding or updating
        if ($id) {
            // Update existing event
            $stmt = $conn->prepare("UPDATE events SET title = ?, description = ?, image = ?, start_time = ?, end_time = ?, start_date = ?, end_date = ?, location = ? WHERE id = ?");
            $stmt->bind_param('ssssssssi', $title, $description, $image, $startTime, $endTime, $startDate, $endDate, $location, $id);
        } else {
            // Add new event
            $stmt = $conn->prepare("INSERT INTO events (title, description, image, start_time, end_time, start_date, end_date, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('ssssssss', $title, $description, $image, $startTime, $endTime, $startDate, $endDate, $location);
        }
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        echo "Error adding/updating event";  // Return a user-friendly message
    }
}

// Delete event
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $id = $_POST['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        echo "Error deleting event";  // Return a user-friendly message
    }
}
?>
