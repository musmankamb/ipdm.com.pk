<?php
include '../config.php'; // Include your database connection

// Fetch enrollments
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $result = $conn->query("SELECT * FROM enrollments WHERE status = 1");  // Assuming 'status' indicates active/inactive
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['user_id']}</td>
                <td>{$row['course_id']}</td>
                <td>{$row['enrollment_date']}</td>
                <td>
                    <button class='btn btn-info view-enrollment' data-id='{$row['id']}' data-user-id='{$row['user_id']}' data-course-id='{$row['course_id']}' data-enrollment-date='{$row['enrollment_date']}'>
                        <i class='fas fa-eye'></i> View
                    </button>
                    <button class='btn btn-warning inactivate-enrollment' data-id='{$row['id']}'>
                        <i class='fas fa-ban'></i> Inactivate
                    </button>
                </td>
              </tr>";
    }
}

// Add/Edit enrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['inactivate'])) {
    $id = $_POST['id'] ?? null;
    $userId = $_POST['userId'];
    $courseId = $_POST['courseId'];

    if ($id) {
        // Update enrollment
        $stmt = $conn->prepare("UPDATE enrollments SET user_id = ?, course_id = ? WHERE id = ?");
        $stmt->bind_param('iii', $userId, $courseId, $id);
    } else {
        // Add new enrollment
        $stmt = $conn->prepare("INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $userId, $courseId);
    }
    $stmt->execute();
    $stmt->close();
}

// Inactivate enrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['inactivate'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("UPDATE enrollments SET status = 0 WHERE id = ?");  // Assuming 0 = inactive
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}
?>
