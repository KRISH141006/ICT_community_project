<?php
session_start();
include("db.php");

$user_otp = $_POST['otp'];

if ($user_otp == $_SESSION['otp']) {

    $data = $_SESSION['user_data'];

    $email = $data['email'];
    $phone = $data['phone'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $role = $data['role'];

    // check user exists
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        echo "User already exists";
        exit;
    }

    // insert user
    $sql = "INSERT INTO users (email, phone, password, role, is_verified)
            VALUES ('$email', '$phone', '$password', '$role', 1)";

    if ($conn->query($sql)) {
        echo "Signup successful";
    } else {
        echo "Error: " . $conn->error;
    }

} else {
    echo "Invalid OTP";
}
?>