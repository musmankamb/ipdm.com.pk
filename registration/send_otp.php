<?php
session_start();

// Include PHPMailer and Database connection
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';
require '../db.php'; // Include your database connection file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



function log_error($message, $logfile) {
    file_put_contents($logfile, date("Y-m-d H:i:s")." - ".$message."\n", FILE_APPEND);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'];

    // Get the user's IP address
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Check if an OTP was recently requested from this IP address
    $stmt = $conn->prepare("SELECT request_time FROM otp_code WHERE ip_address = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("s", $ip_address);
    $stmt->execute();
    $stmt->bind_result($last_request_time);
    $stmt->fetch();
    $stmt->close();

    // Rate-limit logic: Check if the last request from this IP was made within 2 minutes
    $now = new DateTime();
    if ($last_request_time) {
        $last_request = new DateTime($last_request_time);
        $interval = $now->diff($last_request);

        if ($interval->i < 2) { // if less than 2 minutes since the last request
            echo json_encode(['status' => 'error', 'message' => 'Too many OTP requests from this IP. Please wait 2 minutes before requesting a new OTP.']);
            exit();
        }
    }

    // Generate a random OTP
    $otp = rand(100000, 999999);

    // Store OTP in session temporarily
    $_SESSION['otp'] = $otp;

    // Store OTP, request time, and IP address in the database (MySQL)
    $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes')); // OTP valid for 10 minutes
    $request_time = $now->format('Y-m-d H:i:s'); // Current timestamp

    $stmt = $conn->prepare("INSERT INTO otp_code (email, otp, expiry, request_time, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $email, $otp, $otp_expiry, $request_time, $ip_address);
    $stmt->execute();

    // Check if the OTP is stored successfully
    if ($stmt->affected_rows > 0) {
        // Send the OTP via email using PHPMailer
        $mail = new PHPMailer(true);

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
            $mail->Subject = 'Your OTP Code - IPDM';
            $mail->Body = "<html>
                <head>
                    <style>
                        .email-container {
                            font-family: Arial, sans-serif;
                            color: #333;
                            background-color: #f7f7f7;
                            padding: 20px;
                            border-radius: 5px;
                            box-shadow: 0 0 10px rgba(0,0,0,0.1);
                        }
                        .email-header {
                            font-size: 24px;
                            font-weight: bold;
                            margin-bottom: 10px;
                            color: #4CAF50;
                        }
                        .otp-code {
                            font-size: 36px;
                            font-weight: bold;
                            color: #4CAF50;
                            margin: 20px 0;
                        }
                        .email-footer {
                            font-size: 14px;
                            color: #888;
                            margin-top: 20px;
                        }
                    </style>
                </head>
                <body>
                    <div class='email-container'>
                        <div class='email-header'>Your OTP Code</div>
                        <p>Dear User,</p>
                        <p>Use the following One Time Password (OTP) to complete your sign-up process:</p>
                        <div class='otp-code'>$otp</div>
                        <p>This OTP is valid for 10 minutes. Please do not share this OTP with anyone.</p>
                        <div class='email-footer'>
                            If you did not request this code, please ignore this email.
                            <br>
                            Thank you,<br>
                           IPDM Team
                        </div>
                    </div>
                </body>
                </html>";
            $mail->AltBody = "Your OTP code is: $otp. Please use this code to complete your sign-up process. This OTP is valid for 10 minutes.";

            $mail->send();
            echo json_encode(['status' => 'success', 'message' => 'OTP sent successfully. Please check your email.']);
        } catch (Exception $e) {
            // Log the error
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}", 3, 'error_log.txt');
            echo json_encode(['status' => 'error', 'message' => 'Failed to send OTP email. Please try again later.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to generate OTP.']);
    }

    // Close the statement
    $stmt->close();
}
?>
