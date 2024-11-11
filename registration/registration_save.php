<?php
// Include PHPMailer and Database connection
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';
require '../db.php'; // Include your database connection file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Collect POST data
$course_id = $_POST['course_id'] ?? '';
$student_name = $_POST['Student_name'] ?? '';
$contact = $_POST['Scontact'] ?? '';
$email = $_POST['Semail'] ?? '';

// Validate the input data
if (empty($course_id) || empty($student_name) || empty($contact) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit();
}

// Step 1: Fetch course title based on course_id
$course_query = "SELECT title FROM courses WHERE id = ?";
$course_stmt = $conn->prepare($course_query);

if ($course_stmt) {
    $course_stmt->bind_param('i', $course_id);
    $course_stmt->execute();
    $course_stmt->bind_result($course_name);
    $course_stmt->fetch();
    $course_stmt->close();
    
    if (empty($course_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Course not found.']);
        exit();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error preparing course query: ' . $conn->error]);
    exit();
}

// Step 2: Check if the registration already exists
$check_query = "SELECT id FROM registration WHERE course_name = ? AND student_name = ? AND contact = ? AND email = ?";
$check_stmt = $conn->prepare($check_query);

if ($check_stmt) {
    $check_stmt->bind_param('ssss', $course_name, $student_name, $contact, $email);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        // Registration already exists
        echo json_encode(['status' => 'error', 'message' => 'You have already registered for this course.']);
        $check_stmt->close();
        $conn->close();
        exit();
    }
    $check_stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error preparing check query: ' . $conn->error]);
    exit();
}

// Step 3: Insert registration details into the registration table
$query = "INSERT INTO registration (course_name, student_name, contact, email) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->bind_param('ssss', $course_name, $student_name, $contact, $email);
    if ($stmt->execute()) {
        // Successful registration
        echo json_encode(['status' => 'success', 'message' => 'Registration successful! An email has been sent.']);
        
        // Step 4: Send registration confirmation email using PHPMailer
        $mail = new PHPMailer(true); // Create instance of PHPMailer
        
        try {
            // Server settings
            $mail->SMTPDebug = 0;  // Disable verbose debug output
            $mail->isSMTP();       // Send using SMTP
            $mail->Host = 'sg2plzcpnl505319.prod.sin2.secureserver.net';  // Set the SMTP server
            $mail->SMTPAuth = true;  // Enable SMTP authentication
            $mail->Username = 'ipdm@famzpk.com';  // SMTP username
            $mail->Password = 'V*a?_8D*nVLi';  // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;   // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $mail->Port       = 465;                              // TCP port to connect to
            

            // Recipients
            $mail->setFrom('ipdm@famzpk.com', 'IPDM');
            $mail->addAddress($email);  // Add a recipient
            // Content
            $mail->isHTML(true);  // Set email format to HTML
            $mail->Subject = 'Registration Confirmation';
            $mail->Body = "
                <h1>Registration Confirmation</h1>
                <p>Hello $student_name,</p>
                <p>You have successfully registered for the course <strong>$course_name</strong>.</p>
                <p>We will contact you soon with further details.</p>
                <p>Thank you for choosing IPDM!</p>
            ";

            // Send the email
            $mail->send();
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
        }
        
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error during registration: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error preparing registration query: ' . $conn->error]);
}

$conn->close();
?>
