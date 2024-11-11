<?php
include '../config.php'; // Include your database connection
// Fetch registrations (GET)
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $result = $conn->query("SELECT * FROM registration");
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['course_name']}</td>
                <td>{$row['student_name']}</td>
                <td>{$row['contact']}</td>
                <td>{$row['email']}</td>
                <td>{$row['payment_method']}</td>
                <td>{$row['voucher_number']}</td>
                <td>{$row['transaction_id']}</td>
                <td>{$row['registration_date']}</td>
                <td>
                    <button class='btn btn-warning edit-registration' 
                            data-id='{$row['id']}'
                            data-course-name='{$row['course_name']}' 
                            data-student-name='{$row['student_name']}' 
                            data-contact='{$row['contact']}' 
                            data-email='{$row['email']}' 
                            data-payment-method='{$row['payment_method']}' 
                            data-voucher-number='{$row['voucher_number']}' 
                            data-transaction-id='{$row['transaction_id']}'>Edit</button>
                    <button class='btn btn-danger delete-registration' 
                            data-id='{$row['id']}'>Delete</button>
                </td>
            </tr>";
        }
    } catch (Exception $e) {
        logError("Error fetching registrations: " . $e->getMessage());
        echo "Error fetching registrations.";
    }
}

// Add/Edit registration (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $id = $_POST['id'] ?? null;
        $courseName = $_POST['courseName'];
        $studentName = $_POST['studentName'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        $paymentMethod = $_POST['paymentMethod'];
        $voucherNumber = $_POST['voucherNumber'] ?? null;
        $transactionId = $_POST['transactionId'] ?? null;

        if ($id) {
            $stmt = $conn->prepare("UPDATE registration SET course_name = ?, student_name = ?, contact = ?, email = ?, payment_method = ?, voucher_number = ?, transaction_id = ? WHERE id = ?");
            $stmt->bind_param('ssissssi', $courseName, $studentName, $contact, $email, $paymentMethod, $voucherNumber, $transactionId, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO registration (course_name, student_name, contact, email, payment_method, voucher_number, transaction_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('ssissss', $courseName, $studentName, $contact, $email, $paymentMethod, $voucherNumber, $transactionId);
        }

        $stmt->execute();
        $stmt->close();

        echo "Registration saved successfully!";
    } catch (Exception $e) {
        logError("Error saving registration: " . $e->getMessage());
        echo "Error saving registration.";
    }
}

// Delete registration (DELETE)
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'];

        $stmt = $conn->prepare("DELETE FROM registration WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();

        echo "Registration deleted successfully!";
    } catch (Exception $e) {
        logError("Error deleting registration: " . $e->getMessage());
        echo "Error deleting registration.";
    }
}

?>
