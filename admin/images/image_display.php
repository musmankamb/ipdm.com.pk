<?php
$baseDirectory = "../../img/gallary/";  // Base directory where category folders are stored
$allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];  // Allowed image extensions

// Initialize arrays for each category
$heroImages = [];
$visionImages = [];
$welcomeImages = [];
$galleryImages = [];

// Fetch images from respective category directories
$heroImages = glob($baseDirectory . "hero/*.{jpg,jpeg,png,webp}", GLOB_BRACE);
$visionImages = glob($baseDirectory . "vision/*.{jpg,jpeg,png,webp}", GLOB_BRACE);
$welcomeImages = glob($baseDirectory . "welcome/*.{jpg,jpeg,png,webp}", GLOB_BRACE);
$galleryImages = glob($baseDirectory . "gallary/*.{jpg,jpeg,png,webp}", GLOB_BRACE);

// Function to display images with update and delete options
function displayImages($categoryImages, $categoryTitle, $categoryDirectory, $category) {
    if (!empty($categoryImages)) {
        echo '<h3>' . $categoryTitle . '</h3>';
        echo '<div class="category-images">';
        
        foreach ($categoryImages as $image) {
            $imagePath = basename($image);  // Get the base name of the image (e.g., image.jpg)
            $imageId = pathinfo($image, PATHINFO_FILENAME);  // Extract the filename without extension
            
            echo '
            <div class="image-item" style="display:inline-block; margin: 10px; text-align:center;">
                <img src="' . $categoryDirectory . $imagePath . '" class="img-thumbnail" style="width:150px;height:150px;">
                <br>
                <button class="btn btn-danger mt-2" onclick="deleteImage(\'' . $imagePath . '\', \'' . $categoryDirectory . '\')">Delete</button>
                <button class="btn btn-warning mt-2" data-toggle="modal" data-target="#updateModal' . $imageId . '">Update</button>
            </div>
            
            <!-- Update Modal for ' . $imagePath . ' -->
            <div class="modal fade" id="updateModal' . $imageId . '" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel' . $imageId . '" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel' . $imageId . '">Update Image: ' . $imagePath . '</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <input type="file" id="imageInput' . $imageId . '" class="form-control" required>
                    <input type="hidden" id="oldImage' . $imageId . '" value="' . $categoryDirectory . $imagePath . '">
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="updateImage(\'' . $imageId . '\', \'' . $category . '\')">Update Image</button>
                  </div>
                </div>
              </div>
            </div>
            ';
        }
        
        echo '</div>';
    } else {
        echo '<p>No images found for ' . $categoryTitle . '.</p>';
    }
}

// Display images by category
displayImages($heroImages, 'Hero Images', $baseDirectory . 'hero/', 'hero');
displayImages($visionImages, 'Vision, Mission, Values Images', $baseDirectory . 'vision/', 'vision');
displayImages($welcomeImages, 'Welcome Images', $baseDirectory . 'welcome/', 'welcome');
displayImages($galleryImages, 'Gallery Images', $baseDirectory . 'gallary/', 'gallary');
?>
