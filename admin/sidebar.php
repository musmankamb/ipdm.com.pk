<?php
include 'config.php'; // Include the config to access BASE_URL
// Get the current page to highlight the active link
$current_page = basename($_SERVER['REQUEST_URI']);
?>
<div id="sidebar" class="sidebar bg-dark">
    <div class="sidebar-header">
        <h4 class="sidebar-title">IPDM</h4>
        <button class="btn btn-outline-light sidebar-toggle" id="sidebarToggle">☰</button>
    </div>
    <ul class="list-unstyled components">
        <li class="<?= ($current_page == '' || $current_page == 'index.php') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>"><i class="fas fa-tachometer-alt"></i> <span class="sidebar-text">Home</span>
            <span class="popup-text">Home</span> <!-- Popup text -->
        </a>
        </li>
        <li class="<?= ($current_page == 'category') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>category/"><i class="fas fa-layer-group"></i> <span class="sidebar-text">Category</span>
            <span class="popup-text">Category</span> <!-- Popup text -->
        </a>
        </li>
        <li class="<?= ($current_page == 'courses') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>courses/"><i class="fas fa-book"></i> <span class="sidebar-text">Courses</span>
            <span class="popup-text">Courses</span> <!-- Popup text -->
        </a>
        </li>
        <li class="<?= ($current_page == 'enrollments') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>enrollments/"><i class="fas fa-users"></i> <span class="sidebar-text">Enrollments</span>
            <span class="popup-text">Enrollments</span> <!-- Popup text -->
        </a>
        </li>
        <li class="<?= ($current_page == 'events') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>events/"><i class="fas fa-calendar-alt"></i> <span class="sidebar-text">Events</span>
            <span class="popup-text">Events</span> <!-- Popup text -->
        </a>
        </li>
        <li class="<?= ($current_page == 'instructors') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>instructors/"><i class="fas fa-chalkboard-teacher"></i> <span class="sidebar-text">Instructors</span>
            <span class="popup-text">Instructors</span> <!-- Popup text -->
        </a>
        </li>
        <li class="<?= ($current_page == 'registration') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>registration/"><i class="fas fa-clipboard-list"></i> <span class="sidebar-text">Registration</span>
            <span class="popup-text">Registration</span> <!-- Popup text -->
        </a>
        </li>
        <li class="<?= ($current_page == 'testimonials') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>testimonials/"><i class="fas fa-comment-dots"></i> <span class="sidebar-text">Testimonials</span>
            <span class="popup-text">Testimonials</span> <!-- Popup text -->
        </a>
        </li>
        <!--<li class="<?= ($current_page == 'users') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>users/"><i class="fas fa-user"></i> <span class="sidebar-text">Users</span>
            <span class="popup-text">Users</span> 
        </a>
        </li> -->  
        <li class="<?= ($current_page == 'images') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>images/"><i class="fas fa-image"></i>  <span class="sidebar-text">Images</span>
            <span class="popup-text">Users</span> 
        </a>
        </li>
    </ul>
</div>
