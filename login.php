<?php
session_start();
include("db.php");

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo "All fields required";
    exit;
}

// query
$sql = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    
    $user = $result->fetch_assoc(); // VERY IMPORTANT

    // check OTP verified
    if ($user['is_verified'] == 0) {
        echo "Please verify OTP first";
        exit;
    }

    // check password
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        echo "success";
    } else {
        echo "Wrong password";
    }

} else {
    echo "User not found";
}
?>