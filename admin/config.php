<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "ipdm";

define('BASE_URL', '/ipdm/admin/');
// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>
