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
<div id="content"  class="content mt-4">
    <div class="header">
    <h1>Testimonials Management</h1>
    <img src="../../img/logo.webp" alt="Logo" class="logo" />
</div>
    <button class="btn btn-primary" data-toggle="modal" data-target="#addTestimonialModal">Add Testimonial</button>
    <table class="table table-bordered mt-4" id="testimonialsTable">
    <colgroup>
					<col width="3%">
					<col width="10%">
					<col width="12%">
					<col width="10%">
					<col width="30%">
					<col width="10%">
                    <col width="8%">
                    <col width="17%">
				</colgroup>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Profession</th>
                <th>Image</th>
                <th>Testimonial</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Testimonials data will be loaded here via AJAX -->
        </tbody>
    </table>
</div>

<!-- Modal for Adding/Editing Testimonial -->
<div class="modal fade" id="addTestimonialModal" tabindex="-1" role="dialog" aria-labelledby="testimonialModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testimonialModalLabel">Add Testimonial</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form id="testimonialForm" enctype="multipart/form-data">
    <input type="hidden" name="id" id="testimonialId">
    <div class="form-group">
        <label for="testimonialName">Name</label>
        <input type="text" class="form-control" name="name" id="testimonialName" required>
    </div>
    <div class="form-group">
        <label for="testimonialProfession">Profession</label>
        <input type="text" class="form-control" name="profession" id="testimonialProfession" required>
    </div>
    <div class="form-group">
        <label for="testimonialImage">Upload Image</label>
        <input type="file" class="form-control" name="testimonialImage" id="testimonialImage" accept="image/*">
        <img id="imagePreview" src="" alt="Image Preview" style="max-width: 100px; display: none;">
    </div>
    <div class="form-group">
        <label for="testimonialText">Testimonial</label>
        <textarea class="form-control" name="testimonialText" id="testimonialText" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
</form>

            </div>
        </div>
    </div>
</div>

<!-- Modal for Delete Confirmation -->
<div class="modal fade" id="deleteTestimonialModal" tabindex="-1" role="dialog" aria-labelledby="deleteTestimonialModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTestimonialModalLabel">Delete Testimonial</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this testimonial?</p>
                <input type="hidden" id="deleteTestimonialId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteTestimonial">Delete</button>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/script.js"></script>
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Include Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    
    // Fetch testimonials
    function fetchTestimonials() {
        $.ajax({
            url: '../ajax/testimonials.php',
            method: 'GET',
            success: function(response) {
                $('#testimonialsTable tbody').html(response);
            }
        });
    }

    fetchTestimonials();
 
    // Submit testimonial form (Add/Edit)
    $('#testimonialForm').on('submit', function(e) {
        e.preventDefault();
       
        var formData = new FormData(this);  // Collect the form data, including the file upload
      //  console.log('Form Data:' + formData);
        // Log form data to ensure all fields are present (for debugging)
        for (var pair of formData.entries()) {
            console.log(pair[0]+ ': ' + pair[1]); 
        }

        $.ajax({
            url: '../ajax/testimonials.php',
            method: 'POST',
            data: formData,
            contentType: false,  // Important for file upload
            processData: false,  // Important for file upload
            success: function(response) {
                try {
                  
                    var parsedResponse = JSON.parse(response);
                    if (parsedResponse.error) {
                        alert('Error: ' + parsedResponse.error); // Display error message
                    } else {
                        $('#addTestimonialModal').modal('hide');
                        fetchTestimonials();  // Refresh the testimonials list
                    }
                } catch (e) {
                    console.error('Parsing error:', e);
                    console.error('Response:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + error);  // Log AJAX errors
            }
        });
    });

    // Edit testimonial
    $('body').on('click', '.edit-testimonial', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const profession = $(this).data('profession');
        const image = $(this).data('image');
        const testimonialText = $(this).data('testimonial-text');

        $('#testimonialId').val(id);
        $('#testimonialName').val(name);
        $('#testimonialProfession').val(profession);
        $('#testimonialText').val(testimonialText);

        // Display image preview if available
        if (image) {
            $('#imagePreview').attr('src', '../../' + image).show();
        } else {
            $('#imagePreview').hide();
        }

        $('#addTestimonialModal').modal('show');
    });

    // Delete testimonial
    $('body').on('click', '.delete-testimonial', function() {
        const id = $(this).data('id');
        $('#deleteTestimonialId').val(id);
        $('#deleteTestimonialModal').modal('show');
    });

    // Confirm delete testimonial
    $('#confirmDeleteTestimonial').on('click', function() {
        const id = $('#deleteTestimonialId').val();

        $.ajax({
            url: '../ajax/testimonials.php',
            method: 'POST',
            data: { delete: true, id: id },
            success: function(response) {
                $('#deleteTestimonialModal').modal('hide');
                fetchTestimonials();
            }
        });
    });

    // Approve testimonial
    $('body').on('click', '.approve-testimonial', function() {
        const id = $(this).data('id');

        $.ajax({
            url: '../ajax/testimonials.php',
            method: 'POST',
            data: { approve: true, id: id },
            success: function(response) {
                fetchTestimonials();
            }
        });
    });
});


</script>
