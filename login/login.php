<?php
// Include database connection
include('../db.php');

// Start the session to manage user session data
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user inputs
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = htmlspecialchars($_POST['password']);
    $security_answer = filter_var($_POST['security_answer'], FILTER_SANITIZE_NUMBER_INT); // Get the security question answer from the form

    // Ensure security question session data exists
    if (!isset($_SESSION['num1']) || !isset($_SESSION['num2'])) {
        $_SESSION['error'] = 'Security question is missing. Please try again.';
        header("Location: index.php");
        exit;
    }

    // Perform server-side validation of the security question
    $num1 = $_SESSION['num1']; // Retrieve the first number from the session
    $num2 = $_SESSION['num2']; // Retrieve the second number from the session
    $correct_answer = $num1 + $num2; // Calculate the correct answer

    if ($security_answer != $correct_answer) {
        $_SESSION['error'] = 'Incorrect answer to the security question.';
        header("Location: index.php");
        exit;
    }

    // Check if the user exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verify the password using password_verify()
        if (password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            // Redirect user based on their role
            if ($user['role'] == 'admin') {
                header("Location: ../admin");
            } else {
                header("Location: ../student");
            }
            exit;
        } else {
            $_SESSION['error'] = 'Invalid password.';
            header("Location: index.php");
            exit;
        }
    } else {
        $_SESSION['error'] = 'User with this email not found.';
        header("Location: index.php");
        exit;
    }

    // Clean up the prepared statement and database connection
    $stmt->close();
    $conn->close();
}
?>
