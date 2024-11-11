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
    <h1>Registration Management</h1>
    <img src="../../img/logo.webp" alt="Logo" class="logo" />
</div>
    <button class="btn btn-primary" data-toggle="modal" data-target="#addRegistrationModal">Add Registration</button>
    <table class="table table-bordered mt-4" id="registrationTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Course Name</th>
                <th>Student Name</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Payment Method</th>
                <th>Voucher Number</th>
                <th>Transaction ID</th>
                <th>Registration Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Registration data will be loaded here via AJAX -->
        </tbody>
    </table>
</div>

<!-- Modal for Adding/Editing Registration -->
<div class="modal fade" id="addRegistrationModal" tabindex="-1" role="dialog" aria-labelledby="registrationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registrationModalLabel">Add Registration</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="registrationForm">
                    <input type="hidden" id="registrationId">
                    <div class="form-group">
                        <label for="courseName">Course Name</label>
                        <select class="form-control" id="courseName" required>
                            <option value="">Select Course</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="studentName">Student Name</label>
                        <input type="text" class="form-control" id="studentName" required>
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact</label>
                        <input type="text" class="form-control" id="contact" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="paymentMethod">Payment Method</label>
                        <select class="form-control" id="paymentMethod" required>
                            <option value="manual">Manual</option>
                            <option value="jazzcash">Jazzcash</option>
                            <option value="easypaisa">Easypaisa</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="voucherNumber">Voucher Number</label>
                        <input type="text" class="form-control" id="voucherNumber" readonly>
                    </div>
                    <div class="form-group">
                        <label for="transactionId">Transaction ID</label>
                        <input type="text" class="form-control" id="transactionId">
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Delete Confirmation -->
<div class="modal fade" id="deleteRegistrationModal" tabindex="-1" role="dialog" aria-labelledby="deleteRegistrationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteRegistrationModalLabel">Delete Registration</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this registration?</p>
                <input type="hidden" id="deleteRegistrationId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteRegistration">Delete</button>
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

<script src="../assets/js/script.js"></script>

<script>
// Fetch registrations and courses via AJAX
$(document).ready(function() {
    fetchRegistrations();
    fetchCourses();  // Fetch the list of courses when the page is ready

    // Fetch Registrations
    function fetchRegistrations() {
        $.ajax({
            url: '../ajax/registration.php',
            method: 'GET',
            success: function(response) {
                $('#registrationTable tbody').html(response);
            },
            error: function() {
                alert('Failed to fetch registrations.');
            }
        });
    }

    // Fetch Courses
    function fetchCourses() {
        $.ajax({
            url: 'fetch_courses.php',  // Adjust the endpoint if needed
            method: 'GET',
            success: function(response) {
                $('#courseName').html(response);  // Populate course dropdown
            },
            error: function() {
                alert('Failed to fetch courses.');
            }
        });
    }

    // Handle payment method change and voucher generation
    $('#paymentMethod').on('change', function() {
        const paymentMethod = $(this).val();
        const courseName = $('#courseName').val();
        const studentName = $('#studentName').val();
        const contact = $('#contact').val();
        const email = $('#email').val();

        if (paymentMethod === 'manual' || paymentMethod === 'jazzcash' || paymentMethod === 'easypaisa') {
            $.ajax({
                url: 'generate_voucher.php',
                method: 'POST',
                data: {
                    courseName: courseName,
                    studentName: studentName,
                    contact: contact,
                    email: email
                },
                success: function(voucherNumber) {
                    $('#voucherNumber').val(voucherNumber);
                },
                error: function() {
                    alert('Failed to generate voucher.');
                }
            });
            $('#transactionId').prop('disabled', paymentMethod === 'manual');
        } else {
            $('#voucherNumber').val('');
            $('#transactionId').prop('disabled', false);
        }
    });

    // Submit registration form (Add/Edit)
    $('#registrationForm').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            id: $('#registrationId').val(),
            courseName: $('#courseName').val(),
            studentName: $('#studentName').val(),
            contact: $('#contact').val(),
            email: $('#email').val(),
            paymentMethod: $('#paymentMethod').val(),
            voucherNumber: $('#voucherNumber').val(),
            transactionId: $('#transactionId').val()
        };

        $.ajax({
            url: 'check_duplicate_voucher.php', 
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.duplicate) {
                    alert('Duplicate registration found.');
                } else {
                    let actionUrl = formData.id ? '../ajax/registration.php' : '../ajax/registration.php'; // Same URL for both insert and update
                    $.ajax({
                        url: actionUrl,
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            $('#addRegistrationModal').modal('hide');
                            fetchRegistrations();
                        },
                        error: function() {
                            alert('Failed to save registration.');
                        }
                    });
                }
            },
            error: function() {
                alert('Failed to check duplicate voucher.');
            }
        });
    });

    // Edit Registration
    $('body').on('click', '.edit-registration', function() {
        const id = $(this).data('id');
        const courseName = $(this).data('course-name');
        const studentName = $(this).data('student-name');
        const contact = $(this).data('contact');
        const email = $(this).data('email');
        const paymentMethod = $(this).data('payment-method');
        const voucherNumber = $(this).data('voucher-number');
        const transactionId = $(this).data('transaction-id');

        $('#registrationId').val(id);
        $('#courseName').val(courseName);
        $('#studentName').val(studentName);
        $('#contact').val(contact);
        $('#email').val(email);
        $('#paymentMethod').val(paymentMethod).trigger('change');
        $('#voucherNumber').val(voucherNumber);
        $('#transactionId').val(transactionId);

        $('#addRegistrationModal').modal('show');
    });

    // Delete Registration
    $('body').on('click', '.delete-registration', function() {
        const id = $(this).data('id');
        $('#deleteRegistrationId').val(id);
        $('#deleteRegistrationModal').modal('show');
    });

    // Confirm Delete Registration
    $('#confirmDeleteRegistration').on('click', function() {
        const id = $('#deleteRegistrationId').val();
        $.ajax({
            url: '../ajax/registration.php',
            method: 'DELETE',  // Change to DELETE method
            data: JSON.stringify({ id: id }),
            success: function(response) {
                $('#deleteRegistrationModal').modal('hide');
                fetchRegistrations();
            },
            error: function() {
                alert('Failed to delete registration.');
            }
        });
    });
});


</script>
