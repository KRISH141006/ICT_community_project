<?php
session_start();

// use composer autoload
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = $_POST['email'] ?? '';
$otp = rand(100000, 999999);

// store in session
$_SESSION['otp'] = $otp;
$_SESSION['user_data'] = $_POST;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    $mail->Username = 'krishsondagar13@gmail.com';

    // APP PASSWORD (no spaces ideally)
    $mail->Password = 'mojcgzpmqtqsbyct';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('krishsondagar13@gmail.com', 'ICT Community');
    $mail->addAddress($email);

    $mail->Subject = 'Your OTP Code';
    $mail->Body = "Your OTP is: $otp";

    $mail->send();

    echo "OTP sent";

} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}
?>