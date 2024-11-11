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

    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
</head>
<div id="content" class="content mt-4">
    <div class="header">
    <h1>Enrollments Management</h1>
    <img src="../../img/logo.webp" alt="Logo" class="logo" />
</div>
    <button class="btn btn-primary" data-toggle="modal" data-target="#addEnrollmentModal">Add Enrollment</button>
    
    <!-- Enrollments Table -->
    <table class="table table-bordered mt-4" id="enrollmentsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Course ID</th>
                <th>Enrollment Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Enrollment data will be loaded here via AJAX -->
        </tbody>
    </table>
</div>

<!-- Modal for Adding/Editing Enrollment -->
<div class="modal fade" id="addEnrollmentModal" tabindex="-1" role="dialog" aria-labelledby="enrollmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enrollmentModalLabel">Add Enrollment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="enrollmentForm">
                    <input type="hidden" id="enrollmentId">
                    <div class="form-group">
                        <label for="userId">User Name</label>
                        <select class="form-control" id="userId" required>
                            <!-- Categories will be loaded here dynamically -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="courseId">Course ID</label>
                        <input type="number" class="form-control" id="courseId" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Viewing Enrollment Details -->
<div class="modal fade" id="viewEnrollmentModal" tabindex="-1" role="dialog" aria-labelledby="viewEnrollmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewEnrollmentModalLabel">View Enrollment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>ID:</strong> <span id="viewEnrollmentId"></span></p>
                <p><strong>User ID:</strong> <span id="viewUserId"></span></p>
                <p><strong>Course ID:</strong> <span id="viewCourseId"></span></p>
                <p><strong>Enrollment Date:</strong> <span id="viewEnrollmentDate"></span></p>
                <!-- Additional enrollment details can be added here -->
            </div>
        </div>
    </div>
</div>

<!-- Modal for Inactivating Enrollment -->
<div class="modal fade" id="inactivateEnrollmentModal" tabindex="-1" role="dialog" aria-labelledby="inactivateEnrollmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inactivateEnrollmentModalLabel">Inactivate Enrollment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to inactivate this enrollment?</p>
                <input type="hidden" id="inactivateEnrollmentId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirmInactivateEnrollment">Inactivate</button>
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
// Fetch enrollments via AJAX and populate DataTable
function fetchEnrollments() {
    $.ajax({
        url: '../ajax/enrollments.php',
        method: 'GET',
        success: function(response) {
            $('#enrollmentsTable tbody').html(response);
            $('#enrollmentsTable').DataTable();  // Initialize DataTables
        }
    });
}
// Fetch categories and populate the select field in the form
function fetchUsers() {
    $.ajax({
        url: 'users.php',
        method: 'GET',
        success: function(response) {
            $('#userId').html(response);  // Populate the select dropdown
        }
    });
}

// Add/Edit enrollment via AJAX
$(document).ready(function() {

    fetchUsers();
    fetchEnrollments();

    $('#enrollmentForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#enrollmentId').val();
        const userId = $('#userId').val();
        const courseId = $('#courseId').val();
        
        $.ajax({
            url: '../ajax/enrollments.php',
            method: 'POST',
            data: { id, userId, courseId },
            success: function(response) {
                $('#addEnrollmentModal').modal('hide');  // Close modal on success
                fetchEnrollments();  // Reload enrollments
            }
        });
    });

    // View enrollment
    $('body').on('click', '.view-enrollment', function() {
        const id = $(this).data('id');
        const userId = $(this).data('user-id');
        const courseId = $(this).data('course-id');
        const enrollmentDate = $(this).data('enrollment-date');

        $('#viewEnrollmentId').text(id);
        $('#viewUserId').text(userId);
        $('#viewCourseId').text(courseId);
        $('#viewEnrollmentDate').text(enrollmentDate);
        $('#viewEnrollmentModal').modal('show');
    });

    // Inactivate enrollment
    $('body').on('click', '.inactivate-enrollment', function() {
        const id = $(this).data('id');
        $('#inactivateEnrollmentId').val(id);
        $('#inactivateEnrollmentModal').modal('show');
    });

    // Confirm inactivate enrollment
    $('#confirmInactivateEnrollment').on('click', function() {
        const id = $('#inactivateEnrollmentId').val();
        $.ajax({
            url: '../ajax/enrollments.php',
            method: 'POST',
            data: { inactivate: true, id: id },
            success: function(response) {
                $('#inactivateEnrollmentModal').modal('hide');  // Close modal on success
                fetchEnrollments();  // Reload enrollments
            }
        });
    });
});
</script>
