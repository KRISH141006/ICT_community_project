<?php
// config/db.php — canonical DB connection
require_once __DIR__ . '/../includes/env.php';

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$name = $_ENV['DB_NAME'] ?? '';

// On macOS XAMPP, 'localhost' can cause "Permission denied" (socket issue).
// '127.0.0.1' forces a TCP connection.
if ($host === 'localhost') {
    $host = '127.0.0.1';
}

// Silence the warning and handle it manually to avoid "Permission denied" crash
mysqli_report(MYSQLI_REPORT_OFF);
$conn = @new mysqli($host, $user, $pass, $name);

if ($conn->connect_error) {
    // We don't die() here because it breaks JSON responses. 
    // We let the calling script check $conn->connect_error.
} else {
    $conn->set_charset('utf8mb4');
}
