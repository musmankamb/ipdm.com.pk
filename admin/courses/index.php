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
    
    <!-- Link your custom styles  -->
    <link rel="stylesheet" href="../assets/css/styles.css">
    <!-- For pages inside subfolders like pages/ -->

    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">




</head>
<style>
  /* Add custom styles for schedule input */
  #scheduleDate {
        border: 2px solid #007bff;
        padding: 10px;
        border-radius: 5px;
        font-size: 16px;
        width: 100%;
    }
    </style>
   <!-- Loader -->
<div id="loader" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:9999;">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<!-- Toast Container -->
<div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
    <!-- Toasts will be dynamically added here -->
</div>
 
<div id="content" class="content mt-4">
    <div class="header">
    <h1>Courses Management</h1>
    <img src="../../img/logo.webp" alt="Logo" class="logo" />
</div>
    <button class="btn btn-primary" data-toggle="modal" data-target="#courseModal">Add Course</button>

    <!-- Courses Table -->
    <table class="table table-bordered mt-4" id="coursesTable">
    <colgroup>
					<col width="3%">
					<col width="10%">
                    <col width="5%">
					<col width="15%">
					<col width="5%">
					<col width="10%">
					<col width="15%">
                    <col width="10%">
                    <col width="5%">
                    <col width="22%">
				</colgroup>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Schedule</th>
                <th>Description</th>
                <th>Price</th>
                <th>Instructor</th>
                <th>Category</th>
                <th>Image</th>
                <th>Popular</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Courses data will be loaded here via AJAX -->
        </tbody>
    </table>
</div>

<!-- Modal for Adding/Editing Course -->
<div class="modal fade" id="courseModal" tabindex="-1" role="dialog" aria-labelledby="courseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseModalLabel">Add Course</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="courseForm">
                    <input type="hidden" id="courseId">
                    <div class="form-group">
                        <label for="courseTitle">Course Title</label>
                        <input type="text" class="form-control" id="courseTitle" required>
                    </div>
                    <div class="form-group">
                        <label for="courseDescription">Course Description</label>
                        <textarea class="form-control" id="courseDescription" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="coursePrice">Price</label>
                        <input type="number" class="form-control" id="coursePrice" required>
                    </div>
                    <div class="form-group">
                        <label for="instructorId">Instructor</label>
                        <select class="form-control" id="instructorId" required>
                            <!-- Instructors will be loaded here dynamically -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="categoryId">Category</label>
                        <select class="form-control" id="categoryId" required>
                            <!-- Categories will be loaded here dynamically -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="scheduleDate">Schedule</label>
                        <input type="date" class="form-control" id="scheduleDate" placeholder="Pick a schedule date" required>
                    </div>

                    <div class="form-group">
        <label for="courseImage">Course Image</label>
        <input type="file" class="form-control" id="courseImage" accept="image/*">
        <img id="imagePreview" src="" alt="Image Preview" style="max-width: 100px; display: none;">
    </div>
    <div class="form-group">
        <label for="coursePopular">Is Popular?</label>
        <select class="form-control" id="coursePopular" required>
            <option value="1">Yes</option>
            <option value="0">No</option>
        </select>
    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Delete Confirmation -->
<div class="modal fade" id="deleteCourseModal" tabindex="-1" role="dialog" aria-labelledby="deleteCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCourseModalLabel">Delete Course</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this course?</p>
                <input type="hidden" id="deleteCourseId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteCourse">Delete</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal for Activate/Deactivate Confirmation -->
<div class="modal fade" id="togglePopularCourseModal" tabindex="-1" role="dialog" aria-labelledby="togglePopularCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="togglePopularCourseModalLabel">Change Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="statusMessage"></p>
                <input type="hidden" id="togglePopularCourseId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmTogglePopularCourse">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- Include Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>



<!-- Custom Script -->
<script src="../assets/js/script.js"></script>

<script>

    
// Function to show loader
function showLoader() {
    $('#loader').show();
}

// Function to hide loader
function hideLoader() {
    $('#loader').hide();
}
// Function to show toast
function showToast(message, type) {
    const toastHTML = `
        <div class="toast ${type === 'success' ? 'bg-success' : 'bg-danger'} text-white" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
            <div class="toast-body">
                ${message}
                <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    `;
    $('#toast-container').append(toastHTML);
    $('.toast').toast('show').on('hidden.bs.toast', function () {
        $(this).remove();  // Remove toast after it is hidden
    });
}
// Fetch courses via AJAX and populate DataTable
function fetchCourses() {
    $.ajax({
        url: '../ajax/courses.php',
        method: 'GET',
        success: function(response) {
            $('#coursesTable tbody').html(response);
            $('#coursesTable').DataTable();  // Initialize DataTables
        }
    });
}

// Fetch categories and populate the select field in the form
function fetchCategories() {
    $.ajax({
        url: 'categories.php',
        method: 'GET',
        success: function(response) {
            $('#categoryId').html(response);  // Populate the select dropdown
        }
    });
}

// Fetch instructors and populate the select field in the form
function fetchInstructors() {
    $.ajax({
        url: 'instructors.php',
        method: 'GET',
        success: function(response) {
            $('#instructorId').html(response);  // Populate the select dropdown
        }
    });
}

// Add/Edit course via AJAX
$(document).ready(function() {
    fetchCourses();
    fetchCategories();
    fetchInstructors();

  
    $('#courseForm').on('submit', function(e) {
    e.preventDefault();

    const id = $('#courseId').val();
    const title = $('#courseTitle').val();
    const description = $('#courseDescription').val();
    const price = $('#coursePrice').val();
    const instructorId = $('#instructorId').val();
    const categoryId = $('#categoryId').val();
    const courseImage = $('#courseImage')[0].files[0]; // Get the uploaded image file
    const popular = $('#coursePopular').val(); // Get the popular field value (1 or 0)
    const schedule = $('#scheduleDate').val(); 

    // Create FormData object to handle file uploads
    const formData = new FormData();
    formData.append('id', id);
    formData.append('title', title);
    formData.append('description', description);
    formData.append('price', price);
    formData.append('instructorId', instructorId);
    formData.append('categoryId', categoryId);
    formData.append('courseImage', courseImage); // Append the image file to the form data
    formData.append('coursePopular', popular); // Append the popular field value
    formData.append('scheduleDate', schedule); 
    showLoader();  // Show loader

    $.ajax({
        url: '../ajax/courses.php',
        method: 'POST',
        data: formData, // Send FormData with the request
        contentType: false, // Prevent jQuery from setting content type header
        processData: false, // Prevent jQuery from processing the data (needed for FormData)
        success: function(response) {
            hideLoader();  // Hide loader
            $('#courseModal').modal('hide');  // Close modal on success
            fetchCourses();  // Reload courses
            showToast('Course saved successfully!', 'success');
        },
        error: function() {
            hideLoader();  // Hide loader
            showToast('Failed to save course.', 'error');
        }
    });
});

// Edit course
// Edit course
$('body').on('click', '.edit-course', function() {
    const id = $(this).data('id');
    const title = $(this).data('title');
    const description = $(this).data('description');
    const price = $(this).data('price');
    const instructorId = $(this).data('instructor-id');
    const categoryId = $(this).data('category-id');
    const popular = $(this).data('popular');
    const schedule = $(this).data('schedule');
    const image = $(this).data('image'); // Get the current image path
    
     
       


    // Set the form fields with course data
    $('#courseId').val(id);
    $('#courseTitle').val(title);
    $('#courseDescription').val(description);
    $('#coursePrice').val(price);
    $('#instructorId').val(instructorId);
    $('#categoryId').val(categoryId);
    $('#coursePopular').val(popular);


      // Convert schedule to YYYY-MM-DD format if necessary and set the value
      if (schedule) {
        const formattedDate = new Date(schedule).toISOString().split('T')[0];
        $('#scheduleDate').val(formattedDate);
    }

    
    // Display current image (if it exists)
    if (image) {
            $('#imagePreview').attr('src', '../../'+image).show();
        } else {
            $('#imagePreview').hide();
        }


    $('#courseModalLabel').text('Edit Course');
    $('#courseModal').modal('show');
});

    // Delete course
    $('body').on('click', '.delete-course', function() {
        const id = $(this).data('id');
        $('#deleteCourseId').val(id);
        $('#deleteCourseModal').modal('show');
    });

    // Confirm delete course
    $('#confirmDeleteCourse').on('click', function() {
        const id = $('#deleteCourseId').val();
        showLoader();  // Show loader
        $.ajax({
            url: '../ajax/courses.php',
            method: 'POST',
            data: { delete: true, id: id },
            success: function(response) {
                hideLoader();  // Hide loader
                $('#deleteCourseModal').modal('hide');  // Close delete modal
                fetchCourses();  // Reload courses
                showToast('Course deleted successfully!', 'success');
            },
            error: function() {
                hideLoader();  // Hide loader
                showToast('Failed to delete course.', 'error');
            }
        });
    });
     
// Handle activate/deactivate confirmation modal
$('body').on('click', '.toggle-status', function() {
    const id = $(this).data('id');  // Get the course ID
    const isPopular = $(this).hasClass('active');  // Check if the course is currently popular (active)

    const statusText = isPopular ? 'unset' : 'set';  // Determine the action text (set or unset)
    const statusValue = isPopular ? 0 : 1;  // 1 for setting popular, 0 for unsetting popular

    // Update modal text and hidden input values
    $('#statusMessage').text(`Are you sure you want to ${statusText} popular for this course?`);
    $('#togglePopularCourseId').val(id);  // Set the course ID in the hidden input

    // Show the confirmation modal
    $('#togglePopularCourseModal').modal('show');

    // Handle the confirm button click event
    $('#confirmTogglePopularCourse').off('click').on('click', function() {
        showLoader();

        // AJAX request to toggle the popular status of the course
        $.ajax({
            url: '../ajax/courses.php',
            method: 'POST',
            data: {
                toggle_status: true,
                id: id,
                popular: statusValue  // Send the new popular status
            },
            success: function(response) {
                $('#togglePopularCourseModal').modal('hide');  // Hide the modal after success
                fetchCourses();  // Reload the courses table

                hideLoader();
                showToast('Course status updated successfully.', 'success');
            },
            error: function(xhr, status, error) {
                console.error('Error toggling status:', error);
                hideLoader();
                showToast('Failed to update course status. Please try again.', 'error');
            }
        });
    });
});


});
</script>
