<?php
session_start();
require '../db.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $otp = $data['otp'];
    $email = $data['email'];

    // Fetch the OTP and expiry time from the database
    $stmt = $conn->prepare("SELECT otp, expiry FROM otp_code WHERE email = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($stored_otp, $expiry);
    $stmt->fetch();
    $stmt->close();

    // Check if the OTP exists
    if ($stored_otp) {
        // Check if the OTP has expired
        if (new DateTime() > new DateTime($expiry)) {
            // If expired, delete the OTP from the database
            $stmt_delete = $conn->prepare("DELETE FROM otp_code WHERE email = ? AND otp = ?");
            $stmt_delete->bind_param("si", $email, $stored_otp);
            $stmt_delete->execute();
            $stmt_delete->close();

            // Respond with OTP expired message
            echo json_encode(['success' => false, 'message' => 'OTP has expired.']);
        } elseif ($stored_otp == $otp) {
            // OTP is valid and verified successfully
            echo json_encode(['success' => true, 'message' => 'OTP verified successfully.']);
        } else {
            // Invalid OTP
            echo json_encode(['success' => false, 'message' => 'Invalid OTP.']);
        }
    } else {
        // No OTP found for the email
        echo json_encode(['success' => false, 'message' => 'No OTP found for this email.']);
    }
}
?>
