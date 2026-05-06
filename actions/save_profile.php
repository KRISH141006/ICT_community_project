<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.html");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$name    = trim($_POST['name']   ?? '');
$branch  = trim($_POST['branch'] ?? '');
$skills  = trim($_POST['skills'] ?? '');

if (empty($name) || empty($branch)) {
    $_SESSION['profile_error'] = "Name and branch are required.";
    header("Location: ../public/profile.php");
    exit;
}

// Upsert profile
$stmt = $conn->prepare(
    "INSERT INTO profiles (user_id, name, branch, skills)
     VALUES (?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE name=VALUES(name), branch=VALUES(branch), skills=VALUES(skills)"
);
$stmt->bind_param("isss", $user_id, $name, $branch, $skills);

if ($stmt->execute()) {
    header("Location: ../public/dashboard.php");
} else {
    $_SESSION['profile_error'] = "Could not save profile. Try again.";
    header("Location: ../public/profile.php");
}
exit;
