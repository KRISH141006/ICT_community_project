<?php
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (empty($email) || empty($phone) || empty($password) || empty($role)) {
        echo "All fields required";
        exit;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    // check user
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        echo "User already exists";
        exit;
    }

    $sql = "INSERT INTO users (email, phone, password, role)
            VALUES ('$email', '$phone', '$password', '$role')";

    if ($conn->query($sql)) {
        echo "Signup successful";
    } else {
        echo "Error: " . $conn->error;
    }

} else {
    echo "Invalid request";
}
?>