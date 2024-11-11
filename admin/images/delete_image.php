<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $baseDirectory = "../../img/gallary/";
    $imageName = $_POST['imageName'];      // Image name (e.g., image.jpg)
    $categoryDirectory = $_POST['categoryDirectory']; // The category folder (e.g., hero/, vision/, etc.)
    
    // Construct the full file path
    $filePath = $baseDirectory . $categoryDirectory . $imageName;

    // Check if the file exists before attempting to delete
    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            echo "Image deleted successfully.";
        } else {
            echo "Error deleting the image.";
        }
    } else {
        echo "Image not found.";
    }
}
?>
