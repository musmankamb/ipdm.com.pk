<?php
include '../db.php';
session_start();

$response = ['errors' => []];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);

    // Validation
    if (empty($name)) {
        $response['errors'][] = 'Name is required.';
    }
    if (empty($password)) {
        $response['errors'][] = 'Password is required.';
    }

    if (empty($response['errors'])) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $update_query = "UPDATE users SET name = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $name, $hashed_password, $user_id);

        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['errors'][] = 'Failed to update settings.';
        }
    }

    echo json_encode($response);
}
?>
