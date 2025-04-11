<?php
include("../0config/database.php");
include("../0config/email_config.php");
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$email = $_POST['email'];

if (empty($email)) {
    echo json_encode(["status" => "error", "message" => "Email field is required."]);
    exit();
}

$query = $conn->prepare("SELECT * FROM users WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Email does not exist."]);
    exit();
}

$otp = mt_rand(100000, 999999);
$updateQuery = $conn->prepare("UPDATE users SET otp = ? WHERE email = ?");
$updateQuery->bind_param("ss", $otp, $email);
$updateQuery->execute();

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port = SMTP_PORT;

    $mail->setFrom(FROM_EMAIL, FROM_NAME);
    $mail->addAddress($email);
    $mail->Subject = 'Your OTP Code';
    $mail->Body = "Your OTP is: " . $otp;

    $mail->send();
    echo json_encode(["status" => "success", "message" => "OTP sent successfully!"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error sending email: " . $mail->ErrorInfo]);
}
?>
