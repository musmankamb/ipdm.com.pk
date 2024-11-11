<?php
include '../config.php';  // Include the database connection

// Function to generate the next serial voucher number
function generateVoucherNumber($conn, $courseName, $studentName, $email, $contact) {
    // Check if a voucher already exists for the same course, student, email, and contact
    $stmt = $conn->prepare("SELECT voucher_number FROM vouchers WHERE course_name = ? AND student_name = ? AND email = ? AND contact = ?");
    $stmt->bind_param('ssss', $courseName, $studentName, $email, $contact);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // If a duplicate voucher is found, return the existing voucher number
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['voucher_number'];
    }
    
    // If no duplicate is found, generate a new voucher number
    $stmt->close();
    
    // Query to get the last voucher number
    $result = $conn->query("SELECT voucher_number FROM vouchers ORDER BY id DESC LIMIT 1");
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastVoucherNumber = $row['voucher_number'];

        // Extract the 4-digit number part from the last voucher number (assumed to be like "202409181234")
        $lastSerial = substr($lastVoucherNumber, -4);  // Get last 4 digits
        $newSerial = str_pad((int)$lastSerial + 1, 4, '0', STR_PAD_LEFT);  // Increment and pad with zeros

        // Generate the new voucher number (e.g., 202409181235)
        $newVoucherNumber = date('Ymd') . $newSerial;
    } else {
        // If no voucher exists, start with "0001" for the current date
        $newVoucherNumber = date('Ymd') . "0001";
    }

    // Insert the new voucher into the database
    $stmt = $conn->prepare("INSERT INTO vouchers (voucher_number, course_name, student_name, email, contact) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $newVoucherNumber, $courseName, $studentName, $email, $contact);
    $stmt->execute();
    $stmt->close();

    // Return the new voucher number
    return $newVoucherNumber;
}

// Example usage of generating a voucher number
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $courseName = $_POST['courseName'];
    $studentName = $_POST['studentName'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    
    // Generate voucher number if needed
    echo generateVoucherNumber($conn, $courseName, $studentName, $email, $contact);
}
?>
