<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../public/dashboard.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$skill   = trim($_POST['skill'] ?? '');

if (empty($skill)) {
    $_SESSION['req_error'] = "Please enter a skill.";
    header("Location: ../public/request.php");
    exit;
}

$stmt = $conn->prepare("INSERT INTO requests (user_id, skill) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $skill);

if ($stmt->execute()) {
    $_SESSION['req_success'] = "Skill test requested successfully!";
} else {
    $_SESSION['req_error'] = "Could not submit request. Try again.";
}

header("Location: ../public/request.php");
exit;
