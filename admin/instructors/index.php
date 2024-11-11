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
    <h1>Instructors Management</h1>
    <img src="../../img/logo.webp" alt="Logo" class="logo" />
</div>
    <button class="btn btn-primary" data-toggle="modal" data-target="#addInstructorModal">Add Instructor</button>
    <table class="table table-bordered mt-4" id="instructorsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Bio</th>
                <th>Image</th>
                <th>Status</th>
                <th>Social Links</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Instructors data will be loaded here via AJAX -->
        </tbody>
    </table>
</div>

<!-- Modal for Adding/Editing Instructor -->
<div class="modal fade" id="addInstructorModal" tabindex="-1" role="dialog" aria-labelledby="instructorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="instructorModalLabel">Add Instructor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="instructorForm" enctype="multipart/form-data">
                    <input type="hidden" id="instructorId" name="id">
                    <div class="form-group">
                        <label for="instructorName">Instructor Name</label>
                        <input type="text" class="form-control" name="name" id="instructorName" required>
                    </div>
                    <div class="form-group">
                        <label for="instructorBio">Bio</label>
                        <textarea class="form-control" name="bio" id="instructorBio" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="instructorImage">Upload Image</label>
                        <input type="file" class="form-control" name="instructorImage" id="instructorImage" accept="image/*">
                        <img id="imagePreview" src="" alt="Image Preview" style="max-width: 100px; display: none;">
                    </div>
                    <div class="form-group">
                        <label for="instructorStatus">Status</label>
                        <select class="form-control" name="status" id="instructorStatus" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <!-- Social Links Section -->
                    <div class="form-group">
                        <label for="socialSelect">Social Media</label>
                        <select class="form-control" name="social" id="socialSelect">
                            <option value="Facebook">Facebook</option>
                            <option value="YouTube">YouTube</option>
                            <option value="Instagram">Instagram</option>
                            <option value="LinkedIn">LinkedIn</option>
                            <option value="TikTok">TikTok</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="socialLink">Social Link</label>
                        <input type="text" class="form-control" id="socialLink" placeholder="Enter URL">
                    </div>
                    <button type="button" id="addSocialLink" class="btn btn-secondary">Add Social Link</button>
                    
                    <!-- Container to Display Added Social Links -->
                    <div id="socialLinksContainer" class="mt-3"></div>

                    <button type="submit" class="btn btn-primary mt-3">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal for Delete Confirmation -->
<div class="modal fade" id="deleteInstructorModal" tabindex="-1" role="dialog" aria-labelledby="deleteInstructorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteInstructorModalLabel">Delete Instructor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this instructor?</p>
                <input type="hidden" id="deleteInstructorId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteInstructor">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Activate/Deactivate Confirmation -->
<div class="modal fade" id="toggleStatusInstructorModal" tabindex="-1" role="dialog" aria-labelledby="toggleStatusInstructorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="toggleStatusInstructorModalLabel">Change Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="statusMessage"></p>
                <input type="hidden" id="toggleStatusInstructorId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmToggleStatusInstructor">Confirm</button>
            </div>
        </div>
    </div>
</div>


<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- Include Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>


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
$(document).ready(function() {
    let socialLinks = [];

    // Add Social Link
    $('#addSocialLink').on('click', function() {
        const link = $('#socialLink').val();
        if (link) {
            socialLinks.push(link);  // Only push the URL (no platform needed)
            $('#socialLinksContainer').append(`<p>${link}</p>`);
            $('#socialLink').val('');  // Clear the input field
        }
    });

    // Fetch instructors and populate the table
    function fetchInstructors() {
        showLoader();
        $.ajax({
            url: '../ajax/instructors.php',
            method: 'GET',
            success: function(response) {
                if (response.trim() !== '') {
                    hideLoader();
                    $('#instructorsTable tbody').html(response);
                } else {
                    hideLoader();
                    $('#instructorsTable tbody').html('<tr><td colspan="7">No instructors found.</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                hideLoader();
                showToast('Failed to fetch instructors. Please try again', 'error');
            }
        });
    }

    fetchInstructors();

    // Submit instructor form (Add/Edit)
    $('#instructorForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        // Handle empty socialLinks
        if (!socialLinks.length) {
            socialLinks = [''];  // Ensure it's at least an empty string if no links are added
        }
        formData.append('socialLinks', socialLinks.join(';'));

        const instructorId = $('#instructorId').val(); // Get the hidden instructor ID value

        if (instructorId) {
            // If instructorId is present, we are editing, append it to form data
            formData.append('id', instructorId);
        }

        showLoader();

        $.ajax({
            url: '../ajax/instructors.php',  // Send to the same endpoint
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                hideLoader();
                $('#addInstructorModal').modal('hide');
                fetchInstructors();
                clearForm();  // Clear the form after submission
               // alert(instructorId ? 'Instructor updated successfully.' : 'Instructor added successfully.');

                showToast(instructorId ? 'Instructor updated successfully.' : 'Instructor added successfully.', 'success');
            },
            error: function(xhr, status, error) {
                hideLoader();
                showToast('Failed to save the instructor. Please check your input and try again', 'error');
                
            }
        });
    });

    // Edit instructor
    $('body').on('click', '.edit-instructor', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const bio = $(this).data('bio');
        const image = $(this).data('image');
        const status = $(this).data('status');
        const social = $(this).data('social');

        // Split social links into array (URLs only)
        socialLinks = typeof social === 'string' && social ? social.split(';') : [];

        $('#instructorId').val(id);  // Set hidden input with ID for editing
        $('#instructorName').val(name);
        $('#instructorBio').val(bio);
        $('#instructorStatus').val(status);
     

        $('#socialLinksContainer').html('');  // Clear previous social links
        socialLinks.forEach(link => {
            let platform = detectPlatform(link);  // Detect platform based on URL
            $('#socialLinksContainer').append(`<p>${platform}: ${link}</p>`);
        });

        if (image) {
            $('#imagePreview').attr('src', image).show();
        } else {
            $('#imagePreview').hide();
        }

        $('#addInstructorModal').modal('show');  // Show the modal in edit mode
    });

    // Function to detect the platform based on the URL
    function detectPlatform(link) {
        if (link.includes('facebook.com')) {
            return 'Facebook';
        } else if (link.includes('youtube.com')) {
            return 'YouTube';
        } else if (link.includes('instagram.com')) {
            return 'Instagram';
        } else if (link.includes('linkedin.com')) {
            return 'LinkedIn';
        } else {
            return 'Other';
        }
    }

    // Clear the form after submission or modal close
    function clearForm() {
        $('#instructorForm')[0].reset();  // Reset the form
        socialLinks = [];  // Reset social links array
        $('#socialLinksContainer').html('');  // Clear social links container
        $('#imagePreview').hide();  // Hide the image preview
        $('#instructorId').val('');  // Clear hidden ID field for new instructor
    }

    // Delete instructor
    $('body').on('click', '.delete-instructor', function() {
        const id = $(this).data('id');
        $('#deleteInstructorId').val(id);  // Set the instructor ID in the hidden input
        $('#deleteInstructorModal').modal('show');  // Show the confirmation modal
    });

    // Confirm delete instructor
    $('#confirmDeleteInstructor').on('click', function() {
        const id = $('#deleteInstructorId').val();  // Get the instructor ID from the hidden input
        showLoader();
        $.ajax({
            url: '../ajax/instructors.php',
            method: 'POST',
            data: { delete: true, id: id },
            success: function(response) {
                $('#deleteInstructorModal').modal('hide');  // Hide the confirmation modal
                fetchInstructors();  // Reload the instructors table

                hideLoader();
      
                showToast('Instructor deleted successfully.', 'success');
            },
            error: function(xhr, status, error) {
               // console.error('Error deleting instructor:', error);
                hideLoader();
                showToast('Failed to delete instructor. Please try again.', 'error');
            }
        });
    });

    // Handle activate/deactivate confirmation modal
    $('body').on('click', '.toggle-status', function() {
        const id = $(this).data('id');
        const statusText = $(this).hasClass('active') ? 'deactivate' : 'activate';  // Set the status text based on current status

        $('#statusMessage').text(`Are you sure you want to ${statusText} this instructor?`);
        $('#toggleStatusInstructorId').val(id);  // Set the instructor ID in the hidden input
        $('#toggleStatusInstructorModal').modal('show');  // Show the confirmation modal
    });

    // Confirm activate/deactivate instructor
    $('#confirmToggleStatusInstructor').on('click', function() {
        const id = $('#toggleStatusInstructorId').val();  // Get the instructor ID from the hidden input
        showLoader();
        $.ajax({
            url: '../ajax/instructors.php',
            method: 'POST',
            data: { toggle_status: true, id: id },
            success: function(response) {
                $('#toggleStatusInstructorModal').modal('hide');  // Hide the confirmation modal
                fetchInstructors();  // Reload the instructors table
                
                hideLoader();
      
                showToast('Instructor status updated successfully.', 'success');
            },
            error: function(xhr, status, error) {
                console.error('Error toggling status:', error);
                hideLoader();
                showToast('Failed to update instructor status. Please try again.', 'error');
            }
        });
    });
});



</script>
