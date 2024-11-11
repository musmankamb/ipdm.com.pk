<?php
include '../config.php';

// Fetch users
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $result = $conn->query("SELECT id, name, email, role, created_at FROM users");
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['role']}</td>
            <td>{$row['created_at']}</td>
            <td>
                <button class='btn btn-warning' onclick='editUser({$row['id']})'>Edit</button>
                <button class='btn btn-danger' onclick='deleteUser({$row['id']})'>Delete</button>
            </td>
        </tr>";
    }
}

// Add/Edit user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);  // Use PHP's password hashing
    $role = $_POST['role'];

    if ($id) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id = ?");
        $stmt->bind_param('ssssi', $name, $email, $password, $role, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $name, $email, $password, $role);
    }
    
    $stmt->execute();
    $stmt->close();
}

// Delete user
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $_DELETE['id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}
?>
