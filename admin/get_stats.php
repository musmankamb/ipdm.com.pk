<?php
include 'config.php'; // Include your database connection

// Array to hold table counts and last updated dates
$stats = [];

// Fetch the count and last updated record for each table
$tables = [
    'courses' => 'created_at',
    'category' => 'NULL', // No last updated column, you may update this based on your schema
    'enrollments' => 'enrollment_date',
    'events' => 'created_at',
    'instructors' => 'created_at',
    'registration' => 'registration_date',
    'testimonials' => 'created_at',
    'users' => 'created_at'
];

foreach ($tables as $table => $dateColumn) {
    // Fetch the count of records
    $result = $conn->query("SELECT COUNT(*) AS count FROM $table");
    $row = $result->fetch_assoc();
    $stats[$table]['count'] = $row['count'];

    // Fetch the last updated or created date, if available
    if ($dateColumn !== 'NULL') {
        $result = $conn->query("SELECT MAX($dateColumn) AS last_updated FROM $table");
        $row = $result->fetch_assoc();
        $stats[$table]['last_updated'] = $row['last_updated'] ?? 'N/A';
    } else {
        $stats[$table]['last_updated'] = 'N/A';
    }
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($stats);
?>
