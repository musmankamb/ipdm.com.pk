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

    <!-- Include Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<div id="content" class="content mt-4">
    <div class="header">
    <h1>Events and Sessions Management</h1>
    <img src="../../img/logo.webp" alt="Logo" class="logo" />
</div>
    <button class="btn btn-primary" data-toggle="modal" data-target="#addEventModal">Add Event</button>

    <!-- Events Table -->
    <table class="table table-bordered mt-4" id="eventsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Image</th>
                <th>Time Range</th>
                <th>Location</th>
                <th>Date Range</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Events data will be loaded here via AJAX -->
        </tbody>
    </table>
</div>

<!-- Modal for Adding/Editing Event -->
<div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Add Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
    <form id="eventForm" enctype="multipart/form-data">
        <input type="hidden" id="eventId" name="id">
        
        <div class="form-group">
            <label for="eventTitle">Event Title</label>
            <input type="text" class="form-control" id="eventTitle" name="title" required>
        </div>
        
        <div class="form-group">
            <label for="eventDescription">Event Description</label>
            <textarea class="form-control" id="eventDescription" name="description" required></textarea>  <!-- Added description textarea -->
        </div>

        <div class="form-group">
            <label for="eventImage">Image</label>
            <input type="file" class="form-control" id="eventImage" name="eventImage" accept="image/*" required>
            <img id="imagePreview" src="" alt="Image Preview" style="max-width: 100px; display: none;">
        </div>

        <!-- Time Range (Start and End Time) -->
        <div class="form-group">
            <label for="eventStartTime">Start Time</label>
            <input type="text" class="form-control" id="eventStartTime" name="startTime" required>
        </div>
        <div class="form-group">
            <label for="eventEndTime">End Time</label>
            <input type="text" class="form-control" id="eventEndTime" name="endTime" required>
        </div>

        <!-- Date Range (Start and End Date) -->
        <div class="form-group">
            <label for="eventDateRange">Date Range</label>
            <input type="text" class="form-control" id="eventDateRange" name="dateRange" required>
        </div>

        <div class="form-group">
            <label for="eventLocation">Location</label>
            <input type="text" class="form-control" id="eventLocation" name="location" required>
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
<!-- Include Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script src="../assets/js/script.js"></script>

<!-- Custom Script -->
<script>
function fetchEvents() {
    $.ajax({
        url: '../ajax/events.php',
        method: 'GET',
        success: function(response) {
            $('#eventsTable tbody').html(response); // Populate the table with fetched events
        }
    });
}

$(document).ready(function() {
    fetchEvents();

    // Initialize Flatpickr for Start and End Time
    $("#eventStartTime").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });

    $("#eventEndTime").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });

    // Initialize Flatpickr for Date Range
    $("#eventDateRange").flatpickr({
        mode: "range",
        dateFormat: "Y-m-d",
    });

    // Submit event form for adding/editing events
    $('#eventForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: '../ajax/events.php',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#addEventModal').modal('hide'); // Hide modal after saving
                fetchEvents(); // Reload events table
            }
        });
    });

    // Edit event
    $('body').on('click', '.edit-event', function() {
        const id = $(this).data('id');
        const title = $(this).data('title');
        const image = $(this).data('image');
        const startTime = $(this).data('start-time');
        const endTime = $(this).data('end-time');
        const startDate = $(this).data('start-date');
        const endDate = $(this).data('end-date');
        const location = $(this).data('location');
        const description = $(this).data('description'); // Get description

        $('#eventId').val(id);
        $('#eventTitle').val(title);
        $('#eventStartTime').val(startTime);
        $('#eventEndTime').val(endTime);
        $('#eventDateRange').val(`${startDate} to ${endDate}`); // Set date range in the format for Flatpickr
        $('#eventLocation').val(location);
        $('#eventDescription').val(description); // Set description in textarea
          // Display current image (if it exists)
    if (image) {
            $('#imagePreview').attr('src', '../../img/events/'+image).show();
        } else {
            $('#imagePreview').hide();
        }

        $('#eventModalLabel').text('Edit Event');
        $('#addEventModal').modal('show');
    });

    // Delete event - trigger the confirmation modal
    $('body').on('click', '.delete-event', function() {
        const id = $(this).data('id');
        $('#deleteEventId').val(id);  // Set the event ID in the hidden input
        $('#deleteEventModal').modal('show');  // Show the confirmation modal
    });

    // Confirm delete event
    $('#confirmDeleteEvent').on('click', function() {
        const id = $('#deleteEventId').val();  // Get the event ID from the hidden input

        $.ajax({
            url: '../ajax/events.php',
            method: 'POST',
            data: { delete: true, id: id },
            success: function(response) {
                $('#deleteEventModal').modal('hide');  // Hide the confirmation modal
                fetchEvents();  // Reload the events table
            }
        });
    });
});

</script>
