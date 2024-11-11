<?php include '../sidebar.php';
include '../session_auth.php'; ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Link Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Link Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Link your custom styles -->
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- For pages inside subfolders like pages/ -->
</head>
<div id="content" class="content mt-4">
<div class="header">
    <h1>Category Management</h1>
    <img src="../../img/logo.webp" alt="Logo" class="logo" />
</div>
  
    <button class="btn btn-primary" data-toggle="modal" data-target="#categoryModal">Add Category</button>
    <table class="table table-bordered mt-4" id="categoryTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Course Count</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Category data will be loaded here via AJAX -->
        </tbody>
    </table>
</div>


<!-- Modal for Delete Confirmation -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" role="dialog" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCategoryModalLabel">Delete Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this category?</p>
                <input type="hidden" id="deleteCategoryId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteCategory">Delete</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal for Adding/Editing Category -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Add Category</h5> <!-- This title will change dynamically -->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="categoryForm" enctype="multipart/form-data"> <!-- enctype for image upload -->
                    <input type="hidden" id="categoryId">
                    <div class="form-group">
                        <label for="categoryName">Category Name</label>
                        <input type="text" class="form-control" id="categoryName" required>
                    </div>
                    <div class="form-group">
                        <label for="categoryImage">Category Image</label>
                        <input type="file" class="form-control" id="categoryImage" accept="image/*">
                        <img id="imagePreview" src="" alt="Image Preview" style="display: none; width: 100px; margin-top: 10px;" />
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- Include Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- Custom Script -->
<script src="../assets/js/script.js"></script>

<script>
// Fetch categories via AJAX
function fetchCategories() {
    $.ajax({
        url: '../ajax/category.php',
        method: 'GET',
        success: function(response) {
            $('#categoryTable tbody').html(response);
        }
    });
}

$(document).ready(function() {
    // Function to fetch categories and populate the table
    fetchCategories();

    // Function to handle form submission for adding/editing
    $('#categoryForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        const id = $('#categoryId').val(); // Hidden input for category ID
        const name = $('#categoryName').val();
        const image = $('#categoryImage')[0].files[0]; // Get the selected image file

        let formData = new FormData();
        formData.append('id', id); // ID can be null (for adding) or have a value (for editing)
        formData.append('name', name);
        if (image) {
            formData.append('image', image); // Append the image file if it exists
        }
        

        $.ajax({
            url: '../ajax/category.php',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#categoryModal').modal('hide'); // Close modal on success
                fetchCategories(); // Reload categories after adding/editing
            }
        });
    });

    // Handle the Add Category button click (empty modal for adding new category)
    $('#addCategoryButton').on('click', function() {
        $('#categoryId').val(''); // Clear hidden ID input
        $('#categoryName').val(''); // Clear the name field
        $('#courseCount').val(''); // Clear the count field
        $('#categoryImage').val(''); // Clear the file input
        $('#imagePreview').hide(); // Hide the image preview
        $('#categoryModalLabel').text('Add Category'); // Update modal title
        $('#categoryModal').modal('show'); // Show the modal
    });

    // Handle Edit button click (populate modal with existing category data)
    $('body').on('click', '.edit-category', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const count = $(this).data('count');
        const imageUrl = $(this).data('image'); // Image URL for preview
        $('#categoryId').val(id); // Set the hidden ID input
        $('#categoryName').val(name); // Set the name field
        $('#courseCount').val(count); // Set the count field

        if (imageUrl) {
           
            $('#imagePreview').attr('src', imageUrl).show(); // Display image preview if exists
        } else {
            $('#imagePreview').hide(); // Hide preview if no image
        }

        $('#categoryModalLabel').text('Edit Category'); // Update modal title
        $('#categoryModal').modal('show'); // Show the modal for editing
    });

  // Handle Delete button click - opens delete confirmation modal
  $('body').on('click', '.delete-category', function() {
        const id = $(this).data('id'); // Get category ID from data attribute
    
        $('#deleteCategoryId').val(id); // Store the ID in a hidden input in the modal
        $('#deleteCategoryModal').modal('show'); // Show the delete confirmation modal
    });

    $('#confirmDeleteCategory').on('click', function() {
        const id = $('#deleteCategoryId').val(); // Get the category ID from the hidden input
        // Send AJAX request to delete the category
        $.ajax({
            url: '../ajax/category.php', // Backend PHP file to handle deletion
            method: 'POST',
            data: { delete: true, id: id },
            success: function(response) {
                $('#deleteCategoryModal').modal('hide'); // Hide the modal after deletion
                fetchCategories(); // Reload categories after deletion
            },
            error: function() {
                alert('Failed to delete category. Please try again.');
            }
        });
    });
});

</script>
