<?php include '../sidebar.php'; ?>
<?php include '../session_auth.php'; ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Link Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<style>
    /* Ensure the content is responsive */
body, html {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

#content {
    max-width: 1200px;
    margin: auto;
    padding: 20px;
}

.header h1 {
    text-align: center;
    margin-bottom: 30px;
    animation: fadeInDown 1s ease-in-out;
}

.button-group {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.btn-primary {
    margin: 10px;
    padding: 10px 20px;
    background-color: #007bff;
    border: none;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.btn-primary:hover {
    background-color: #0056b3;
    transform: scale(1.05);
}

.category-images {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
}

/* General Styles */
.image-item {
    display: inline-block;
    margin: 10px;
    text-align: center;
    position: relative;
    animation: fadeInUp 0.5s ease-in-out;
}

/* Styling for the images */
.image-item img {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Hover effect for images */
.image-item img:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
}

/* Buttons for Delete and Update */
.image-item button {
    margin-top: 10px;
    width: 80%;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

/* Button hover effects */
.image-item button:hover {
    transform: scale(1.05);
}

/* Delete button styling */
.image-item .btn-danger {
    background-color: #e74c3c;
    border: none;
}

.image-item .btn-danger:hover {
    background-color: #c0392b;
}

/* Update button styling */
.image-item .btn-warning {
    background-color: #f39c12;
    border: none;
}

.image-item .btn-warning:hover {
    background-color: #e67e22;
}

/* Responsive adjustments for smaller screens */
@media (max-width: 768px) {
    .image-item {
        width: 100%;
        margin-bottom: 20px;
    }

    .image-item img {
        width: 120px;
        height: 120px;
    }
}

/* Modal Styling */
.modal-content {
    animation: zoomIn 0.3s ease-in-out;
}

/* Keyframes for the animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes zoomIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

    </style>
<div id="content" class="content mt-4">
    <div class="header">
        <h1>Images Management for Gallary and Misc.</h1>
    </div>

    <!-- Buttons for adding images -->
    <button class="btn btn-primary" data-toggle="modal" data-target="#heroModal">Add images for Hero</button>
    <button class="btn btn-primary" data-toggle="modal" data-target="#visionModal">Add images for Vision, Mission, Values</button>
    <button class="btn btn-primary" data-toggle="modal" data-target="#welcomeModal">Add images for Welcome</button>
    <button class="btn btn-primary" data-toggle="modal" data-target="#gallaryModal">Add images for Gallery</button>

    <!-- Image Display Sections -->
    <div id="imageDisplay">
        <!-- Dynamic images from PHP will be loaded here -->
        <?php include 'image_display.php'; ?>
    </div>
</div>

<!-- Modals -->
<!-- Hero Modal -->
<div class="modal fade" id="heroModal" tabindex="-1" role="dialog" aria-labelledby="heroModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="heroForm" enctype="multipart/form-data" method="POST" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="heroModalLabel">Upload Image for Hero</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="file" name="image" class="form-control" required>
          <input type="hidden" name="category" value="hero">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Upload</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Vision Modal -->
<div class="modal fade" id="visionModal" tabindex="-1" role="dialog" aria-labelledby="visionModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="visionForm" enctype="multipart/form-data" method="POST" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="visionModalLabel">Upload Image for Vision, Mission, Values</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="file" name="image" class="form-control" required>
          <input type="hidden" name="category" value="vision">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Upload</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Welcome Modal -->
<div class="modal fade" id="welcomeModal" tabindex="-1" role="dialog" aria-labelledby="welcomeModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="welcomeForm" enctype="multipart/form-data" method="POST" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="welcomeModalLabel">Upload Image for Welcome</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="file" name="image" class="form-control" required>
          <input type="hidden" name="category" value="welcome">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Upload</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Gallery Modal -->
<div class="modal fade" id="gallaryModal" tabindex="-1" role="dialog" aria-labelledby="gallaryModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="gallaryForm" enctype="multipart/form-data" method="POST" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="gallaryModalLabel">Upload Image for Gallery</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="file" name="image" class="form-control" required>
          <input type="hidden" name="category" value="gallary">
          
          <!-- New fields for title and description -->
          <div class="form-group">
            <label for="title">Image Title</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="description">Image Description</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Upload</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js"></script>
<script>
 
$(document).ready(function() {
    // Handle Hero form submission via AJAX
    $('#heroForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission
        var formData = new FormData(this); // Create form data object

        $.ajax({
            url: 'upload_image.php', // PHP file to handle image upload
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                alert(response); // Display response message
                $('#heroModal').modal('hide'); // Hide the modal after success
                $('#imageDisplay').load('image_display.php'); // Reload the image display area
            },
            error: function(response) {
                alert('Image upload failed!'); // Display error message
            }
        });
    });

    // Handle Vision form submission via AJAX
    $('#visionForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: 'upload_image.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                alert(response);
                $('#visionModal').modal('hide');
                $('#imageDisplay').load('image_display.php');
            },
            error: function(response) {
                alert('Image upload failed!');
            }
        });
    });

    // Handle Welcome form submission via AJAX
    $('#welcomeForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: 'upload_image.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                alert(response);
                $('#welcomeModal').modal('hide');
                $('#imageDisplay').load('image_display.php');
            },
            error: function(response) {
                alert('Image upload failed!');
            }
        });
    });

    // Handle Gallery form submission via AJAX
    $('#gallaryForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: 'upload_image.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                alert(response);
                $('#gallaryModal').modal('hide');
                $('#imageDisplay').load('image_display.php');
            },
            error: function(response) {
                alert('Image upload failed!');
            }
        });
    });
});

function updateImage(imageId, category) {
    var fileInput = document.getElementById('imageInput' + imageId);
    var oldImage = document.getElementById('oldImage' + imageId).value;

    if (fileInput.files.length === 0) {
        alert('Please select an image to upload.');
        return;
    }

    var formData = new FormData();
    formData.append('image', fileInput.files[0]);
    formData.append('old_image', oldImage);
    formData.append('category', category); // Add category to the form data

    $.ajax({
        url: 'update_image.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            alert(response);
            location.reload();
        },
        error: function(xhr, status, error) {
            alert('Failed to update the image. Please try again.');
        }
    });
}



function deleteImage(imageName, categoryDirectory) {
    if (confirm('Are you sure you want to delete this image?')) {
        $.ajax({
            url: 'delete_image.php',
            type: 'POST',
            data: {
                imageName: imageName,
                categoryDirectory: categoryDirectory // Pass the category directory
            },
            success: function(response) {
                alert(response); // Show the response message
                location.reload(); // Reload the page to reflect the deleted image
            },
            error: function(xhr, status, error) {
                alert('Error deleting the image. Please try again.');
            }
        });
    }
}

    </script>
