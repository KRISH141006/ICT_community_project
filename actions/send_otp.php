<?php
session_start();
header('Content-Type: application/json');

// use composer autoload
require '../vendor/autoload.php';
require_once "../includes/env.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = $_POST['email'] ?? '';

// basic validation
if (!$email) {
    echo json_encode([
        "status" => "error",
        "message" => "Email required"
    ]);
    exit;
}

// generate OTP
$otp = rand(100000, 999999);

// store in session
$_SESSION['otp'] = $otp;
$_SESSION['user_data'] = $_POST;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $_ENV['MAIL_HOST'];
    $mail->SMTPAuth = true;

    $mail->Username = $_ENV['MAIL_USER'];
    $mail->Password = $_ENV['MAIL_PASS']; // app password

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $_ENV['MAIL_PORT'];

    $mail->setFrom('krishsondagar13@gmail.com', 'ICT Community');
    $mail->addAddress($email);

    $mail->Subject = 'Your OTP Code';
    $mail->Body = "Your OTP is: $otp";

    $mail->send();

    // ✅ ONLY JSON RESPONSE
    echo json_encode([
        "status" => "success",
        "message" => "OTP sent"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Mailer Error: " . $mail->ErrorInfo
    ]);
}