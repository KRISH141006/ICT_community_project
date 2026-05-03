<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "All fields required"]);
    exit;
}

$sql = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    
    $user = $result->fetch_assoc();

    if ($user['is_verified'] == 0) {
        echo json_encode(["status" => "error", "message" => "Verify OTP first"]);
        exit;
    }

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Wrong password"]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "User not found"]);
}