<?php 

session_start();
include 'sidebar.php';


// Set session timeout duration (15 minutes)
$timeout_duration = 900; // 15 minutes = 900 seconds

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to login page
    header("Location: ../login?auth=required");
    exit;
}

// Check if the session has expired due to inactivity
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    // Session has expired, unset and destroy session
    session_unset();
    session_destroy();
    header("Location: ../login?session=expired");
    exit;
}

// Update last activity timestamp
$_SESSION['LAST_ACTIVITY'] = time(); // Reset last activity time to now ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Link Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Link Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
    <!-- Link your custom styles -->
    <link rel="stylesheet" href="assets/css/styles.css"> <!-- For pages inside subfolders like pages/ -->
</head>
<style>
  /* Settings icon positioning */
.settings-dropdown {
    position: absolute;
    top: 20px; /* Adjust the distance from the top as needed */
    right: 120px; /* Adjust the distance from the right as needed */
    background-color: orange;
    padding:20px;
    border-radius: 50%;
}

.settings-icon {
    font-size: 24px;
    cursor: pointer;
    color: #333;
    transition: color 0.3s ease;
}

.settings-icon:hover {
    color: #007bff; /* Change color on hover */
}

/* Dropdown menu styles */
.dropdown-menu {
    display: none; /* Hidden by default */
    position: absolute;
    top: 40px; /* Adjust depending on icon size */
    right: 0;
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 150px;
    z-index: 1000;
}

.dropdown-menu a {
    display: block;
    padding: 10px 15px;
    color: #333;
    text-decoration: none;
    font-size: 14px;
    transition: background-color 0.3s ease;
}

.dropdown-menu a:hover {
    background-color: #f1f1f1;
    color: #007bff;
}

/* Show the dropdown when active */
.dropdown-menu.active {
    display: block;
}

    </style>
<div id="content" class="content mt-4">
    <div class="container mt-4">
    <div class="header">
    <h1>Admin Dashboard - Institute of Personal Development Multan</h1>
    <img src="../img/logo.webp" alt="Logo" class="logo" />
    <div class="settings-dropdown">
    <i class="fas fa-cog settings-icon" id="settingsIcon"></i> <!-- Settings icon -->
    <div class="dropdown-menu" id="settingsMenu">
       <!-- <a href="my_account.php">My Account</a>-->
        <a href="../logout">Logout</a>
    </div>
</div>
</div>


        <!-- Stats Cards using Flexbox -->
        <div class="stats-cards">
            <div class="card-item">
                <div class="card-icon"><i class="fas fa-book"></i></div>
                <div class="card-info">
                    <h5> Courses</h5>
                    <p><span id="totalCourses">0</span></p>
                    <small>Last Updated: <span id="coursesLastUpdated">N/A</span></small>
                </div>
            </div>

            <div class="card-item">
                <div class="card-icon"><i class="fas fa-layer-group"></i></div>
                <div class="card-info">
                    <h5> Categories</h5>
                    <p><span id="totalCategories">0</span></p>
                    <small>Last Updated: <span id="categoryLastUpdated">N/A</span></small>
                </div>
            </div>

            <div class="card-item">
                <div class="card-icon"><i class="fas fa-users"></i></div>
                <div class="card-info">
                    <h5> Enrollments</h5>
                    <p><span id="totalEnrollments">0</span></p>
                    <small>Last Updated: <span id="enrollmentsLastUpdated">N/A</span></small>
                </div>
            </div>

            <div class="card-item">
                <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="card-info">
                    <h5> Events</h5>
                    <p><span id="totalEvents">0</span></p>
                    <small>Last Updated: <span id="eventsLastUpdated">N/A</span></small>
                </div>
            </div>

            <div class="card-item">
                <div class="card-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="card-info">
                    <h5> Instructors</h5>
                    <p><span id="totalInstructors">0</span></p>
                    <small>Last Updated: <span id="instructorsLastUpdated">N/A</span></small>
                </div>
            </div>

            <div class="card-item">
                <div class="card-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="card-info">
                    <h5> Registrations</h5>
                    <p><span id="totalRegistrations">0</span></p>
                    <small>Last Updated: <span id="registrationLastUpdated">N/A</span></small>
                </div>
            </div>

            <div class="card-item">
                <div class="card-icon"><i class="fas fa-comment-dots"></i></div>
                <div class="card-info">
                    <h5> Testimonials</h5>
                    <p><span id="totalTestimonials">0</span></p>
                    <small>Last Updated: <span id="testimonialsLastUpdated">N/A</span></small>
                </div>
            </div>

            <div class="card-item">
                <div class="card-icon"><i class="fas fa-user"></i></div>
                <div class="card-info">
                    <h5> Users</h5>
                    <p><span id="totalUsers">0</span></p>
                    <small>Last Updated: <span id="usersLastUpdated">N/A</span></small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="assets/js/script.js"></script>

<!-- Fetch the stats and update the cards -->
<script>
$(document).ready(function() {
    // Fetch statistics from the server
    $.getJSON('get_stats.php', function(data) {
        // Update the counts and last updated values
        $('#totalCourses').text(data.courses.count);
        $('#coursesLastUpdated').text(data.courses.last_updated);

        $('#totalCategories').text(data.category.count);
        $('#categoryLastUpdated').text(data.category.last_updated);

        $('#totalEnrollments').text(data.enrollments.count);
        $('#enrollmentsLastUpdated').text(data.enrollments.last_updated);

        $('#totalEvents').text(data.events.count);
        $('#eventsLastUpdated').text(data.events.last_updated);

        $('#totalInstructors').text(data.instructors.count);
        $('#instructorsLastUpdated').text(data.instructors.last_updated);

        $('#totalRegistrations').text(data.registration.count);
        $('#registrationLastUpdated').text(data.registration.last_updated);

        $('#totalTestimonials').text(data.testimonials.count);
        $('#testimonialsLastUpdated').text(data.testimonials.last_updated);

        $('#totalUsers').text(data.users.count);
        $('#usersLastUpdated').text(data.users.last_updated);
    });
});
</script>
