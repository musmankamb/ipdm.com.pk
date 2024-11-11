<?php
// Start session
session_start();

// Include PHPMailer and Database connection
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';
require '../db.php'; // Ensure you have a db.php file for database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

// Read the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

// Validate the received data
if (!empty($data['course_name']) && !empty($data['student_name']) && !empty($data['contact']) && !empty($data['email']) && isset($data['payment_method'])) {
    
    // Prepare the values to be checked and inserted
    $course_name = $data['course_name'];
    $student_name = $data['student_name'];
    $email = $data['email'];
    $contact = $data['contact'];
    $payment_method = $data['payment_method']; // 1 for voucher, 2 for JazzCash, etc.
    $voucher_number = isset($data['voucher_number']) ? $data['voucher_number'] : null;
    $transaction_id = isset($data['transaction_id']) ? $data['transaction_id'] : null;

    // Check for duplication in the database
    $check_sql = "SELECT * FROM registration WHERE course_name = ? AND student_name = ? AND email = ? AND contact = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ssss", $course_name, $student_name, $email, $contact);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Duplicate found, return an error message
        echo json_encode(['success' => false, 'error' => 'Duplicate registration found.']);
    } else {
        // If payment method is voucher, generate the voucher number
        if ($payment_method == 1) {
            $voucher_number = generateVoucherNumber($conn, $course_name, $student_name, $email, $contact);
        }

        // Insert the new record
        $sql = "INSERT INTO registration (course_name, student_name, email, contact, payment_method, voucher_number, transaction_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        // Prepare the statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiiss", $course_name, $student_name, $email, $contact, $payment_method, $voucher_number, $transaction_id);

        // Execute and check if it was successful
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'voucher_number' => $voucher_number]);

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
                    <p>Your voucher number (if applicable) is: <strong>$voucher_number</strong>.</p>
                    <p>We will contact you soon with further details.</p>
                    <p>Thank you for choosing IPDM!</p>
                ";

                // Send the email
                $mail->send();
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
            }
            
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to register.']);
        }

        // Close the statement
        $stmt->close();
    }

    // Close the check statement
    $check_stmt->close();

} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input.']);
}

// Close the database connection
$conn->close();
?>
