<?php
include '../config.php';  // Database connection

$courseName = $_POST['courseName'];
$studentName = $_POST['studentName'];
$contact = $_POST['contact'];
$email = $_POST['email'];

// Check if a voucher with the same course, student name, contact, and email already exists
$stmt = $conn->prepare("SELECT * FROM vouchers WHERE course_name = ? AND student_name = ? AND contact = ? AND email = ?");
$stmt->bind_param('ssss', $courseName, $studentName, $contact, $email);
$stmt->execute();
$result = $stmt->get_result();

$response = array('duplicate' => $result->num_rows > 0);
echo json_encode($response);

$stmt->close();
?>
