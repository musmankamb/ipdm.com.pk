<?php
// db.php: Include this file for database connection
include '../db.php'; 

session_start(); // Assuming the user is logged in and session stores user_id or email

// Fetch user details
$user_id = $_SESSION['user_id'];
$email = $_SESSION['user_email'];

// Fetch from enrollments
$enrollments_query = "SELECT user_id, course_id, enrollment_date, status FROM enrollments WHERE user_id = ?";
$enrollments_stmt = $conn->prepare($enrollments_query);
$enrollments_stmt->bind_param("i", $user_id);
$enrollments_stmt->execute();
$enrollments_result = $enrollments_stmt->get_result()->fetch_assoc();


// Fetch all registrations for the user (grouped by course)
$registration_query = "SELECT course_name, student_name, contact, email, payment_method, voucher_number, transaction_id, registration_date FROM registration WHERE email = ?";
$registration_stmt = $conn->prepare($registration_query);
$registration_stmt->bind_param("s", $email);
$registration_stmt->execute();
$registration_results = $registration_stmt->get_result();

// Create an associative array to store course registrations
$registrations = [];
while ($row = $registration_results->fetch_assoc()) {
    $registrations[$row['course_name']] = $row; // Group by course_name
}

// Fetch from testimonials
$testimonials_query = "SELECT name, profession, image, testimonial_text, created_at, approved FROM testimonials WHERE user_id = ?";
$testimonials_stmt = $conn->prepare($testimonials_query);
$testimonials_stmt->bind_param("i", $user_id);
$testimonials_stmt->execute();
$testimonials_result = $testimonials_stmt->get_result()->fetch_assoc();


// Fetch all vouchers for the user
$vouchers_query = "SELECT voucher_number, generated_at, course_name, student_name, email, contact FROM vouchers WHERE email = ?";
$vouchers_stmt = $conn->prepare($vouchers_query);
$vouchers_stmt->bind_param("s", $email);
$vouchers_stmt->execute();
$vouchers_results = $vouchers_stmt->get_result();

// Create an associative array to store vouchers
$vouchers = [];
while ($row = $vouchers_results->fetch_assoc()) {
    $vouchers[$row['voucher_number']] = $row; // Group by voucher_number
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="student.css"> <!-- Assume styles.css for basic styling -->
    <!-- Include Bootstrap CSS (add this in your HTML file inside <head>) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        /* Fading animation for tab content */
        .tab-pane {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        .tab-pane.active {
            opacity: 1;
        } 
    </style>
</head>
<body>

<header>
    <h1>  <img src="../img/logo.webp" alt="Logo" class="logo me-3" /> Institute of Personal Development Multan</h1>
    <nav>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="../">Home</a></li>
            <li><a href="#setting">Settings</a></li>
            <li><a href="../courses">Courses</a></li>
        </ul>
    </nav>
</header>

<main>
    <!-- Two-column container for sections -->
    <div class="section-container">
        <!-- Left Section: Enrollment Details -->
        <section id="user-details" class="section">
            <h2>Your Enrollment Details</h2>
            <p><strong>Course ID:</strong> <?= !empty($enrollments_result['course_id']) ? $enrollments_result['course_id'] : 'Not Available' ?></p>
            <p><strong>Enrollment Date:</strong> <?= !empty($enrollments_result['enrollment_date']) ? $enrollments_result['enrollment_date'] : 'Not Available' ?></p>
            <p><strong>Status:</strong> <?= !empty($enrollments_result['status']) ? $enrollments_result['status'] : 'Not Available' ?></p>
        </section>

        <!-- Right Section: Registration Details -->
       
        <section id="registration-details" class="section">
        <h2>Your Registration Details</h2>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="courseTabs" role="tablist">
            <?php 
            $active = 'active'; // To set the first tab as active
            $index = 0;
            foreach ($registrations as $course_name => $details): 
            ?>
                <li class="nav-item">
                    <a class="nav-link <?= $active ?>" id="course-<?= $index ?>-tab" data-toggle="tab" href="#course-<?= $index ?>" role="tab" aria-controls="course-<?= $index ?>" aria-selected="true"><?= $course_name ?></a>
                </li>
            <?php 
                $active = ''; // Reset active after the first tab
                $index++;
            endforeach; 
            ?>
        </ul>

        <!-- Tab content -->
        <div class="tab-content" id="courseTabsContent">
            <?php 
            $active = 'active'; // To set the first content as active
            $index = 0;
            foreach ($registrations as $course_name => $details): 
            ?>
                <div class="tab-pane fade show <?= $active ?>" id="course-<?= $index ?>" role="tabpanel" aria-labelledby="course-<?= $index ?>-tab">
                    <p><strong>Course Name:</strong> <?= $details['course_name'] ?></p>
                    <p><strong>Student Name:</strong> <?= $details['student_name'] ?></p>
                    <p><strong>Contact:</strong> <?= $details['contact'] ?></p>
                    <p><strong>Email:</strong> <?= $details['email'] ?></p>
                    <p><strong>Payment Method:</strong> <?= $details['payment_method'] ?></p>
                    <p><strong>Voucher Number:</strong> <?= $details['voucher_number'] ?></p>
                </div>
            <?php 
                $active = ''; // Reset active after the first tab
                $index++;
            endforeach; 
            ?>
        </div>
    </section>

    </div>

    <div class="section-container">
        <!-- Left Section: Testimonial Details -->
        <section id="testimonial-details" class="section">
            <h2>Your Testimonial</h2>
            <?php if ($testimonials_result): ?>
                <p><strong>Name:</strong> <?= !empty($testimonials_result['name']) ? $testimonials_result['name'] : 'Not Available' ?></p>
                <p><strong>Profession:</strong> <?= !empty($testimonials_result['profession']) ? $testimonials_result['profession'] : 'Not Available' ?></p>
                <p><strong>Testimonial:</strong> <?= !empty($testimonials_result['testimonial_text']) ? $testimonials_result['testimonial_text'] : 'Not Available' ?></p>
                <p><strong>Approved:</strong> <?= !empty($testimonials_result['approved']) ? ($testimonials_result['approved'] ? 'Yes' : 'No') : 'Not Available' ?></p>
            <?php else: ?>
                <p>You have not added any testimonials yet.</p>
            <?php endif; ?>
        </section>

        <!-- Right Section: Voucher Details -->
        <section id="voucher-details" class="section">
        <h2>Your Voucher Details</h2>

        <!-- Nav tabs for vouchers -->
        <ul class="nav nav-tabs" id="voucherTabs" role="tablist">
            <?php 
            $active = 'active'; // To set the first tab as active
            $index = 0;
            foreach ($vouchers as $voucher_number => $details): 
            ?>
                <li class="nav-item">
                    <a class="nav-link <?= $active ?>" id="voucher-<?= $index ?>-tab" data-toggle="tab" href="#voucher-<?= $index ?>" role="tab" aria-controls="voucher-<?= $index ?>" aria-selected="true">
                        Voucher #<?= $voucher_number ?>
                    </a>
                </li>
            <?php 
                $active = ''; // Reset active after the first tab
                $index++;
            endforeach; 
            ?>
        </ul>

        <!-- Tab content for voucher details -->
        <div class="tab-content" id="voucherTabsContent">
            <?php 
            $active = 'active'; // To set the first content as active
            $index = 0;
            foreach ($vouchers as $voucher_number => $details): 
            ?>
                <div class="tab-pane fade show <?= $active ?>" id="voucher-<?= $index ?>" role="tabpanel" aria-labelledby="voucher-<?= $index ?>-tab">
                    <p><strong>Voucher Number:</strong> <?= $details['voucher_number'] ?></p>
                    <p><strong>Generated At:</strong> <?= $details['generated_at'] ?></p>
                    <p><strong>Course Name:</strong> <?= $details['course_name'] ?></p>
                    <p><strong>Student Name:</strong> <?= $details['student_name'] ?></p>
                    <p><strong>Email:</strong> <?= $details['email'] ?></p>
                    <p><strong>Contact:</strong> <?= $details['contact'] ?></p>
                </div>
            <?php 
                $active = ''; // Reset active after the first tab
                $index++;
            endforeach; 
            ?>
        </div>
    </section>
    </div>

    <!-- Two forms side by side -->
    <div class="section-container">
        <!-- Add Testimonial Form -->
        <div class="section">
            <h2>Add Testimonial</h2>
            <div id="testimonial-errors"></div> <!-- This will display validation errors -->
            <p id="testimonial-loading" style="display: none;">Submitting testimonial...</p> <!-- Loading message -->

            <form action="submit_testimonial.php" method="POST" enctype="multipart/form-data">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="profession">Profession:</label>
                <input type="text" id="profession" name="profession" required>

                <label for="testimonial_text">Testimonial:</label>
                <textarea id="testimonial_text" name="testimonial_text" required></textarea>

                <label for="image">Upload Image:</label>
                <input type="file" id="image" name="image">

                <button type="submit" class="btn btn-primary">Submit Testimonial</button>
            </form>
        </div>

        <!-- Settings Section -->
        <div class="section" id="setting">
            <h2>Update Your Credentials</h2>
            <div id="settings-errors"></div> <!-- This will display validation errors -->
            <p id="settings-loading" style="display: none;">Updating settings...</p> <!-- Loading message -->

            <form action="update_settings.php" method="POST">
                <label for="name">Update Name:</label>
                <input type="text" id="name" name="name" value="<?= $_SESSION['name'] ?>" required>

                <label for="password">Update Password:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit" class="btn btn-primary">Update Settings</button>
            </form>
        </div>
    </div>
</main>
<!-- Include Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>