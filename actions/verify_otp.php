<?php
session_start();
include("../includes/db.php");
require_once __DIR__ . '/../includes/env.php';

header('Content-Type: application/json');

$user_otp = $_POST['otp'] ?? '';

if (!isset($_SESSION['otp']) || !isset($_SESSION['user_data'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Session expired. Try again."
    ]);
    exit;
}

if ($user_otp == $_SESSION['otp']) {

    $data = $_SESSION['user_data'];

    $email = $conn->real_escape_string($data['email']);
    $phone = $conn->real_escape_string($data['phone']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $role = $conn->real_escape_string($data['role']);

    // check user exists
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        echo json_encode([
            "status" => "error",
            "message" => "User already exists"
        ]);
        exit;
    }

    // insert user
    $sql = "INSERT INTO users (email, phone, password, role, is_verified)
            VALUES ('$email', '$phone', '$password', '$role', 1)";

    if ($conn->query($sql)) {
        echo json_encode([
            "status" => "success",
            "message" => "Signup successful"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => $conn->error
        ]);
    }

} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid OTP"
    ]);
}
?>