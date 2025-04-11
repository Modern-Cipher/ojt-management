<?php
require_once '../0config/database.php';
require_once '../0config/email_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Data validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<div class='alert alert-danger'>Invalid email format.</div>";
        exit;
    }

    // Check if email exists in database
    $stmt = $conn->prepare("SELECT users_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "<div class='alert alert-danger email-not-found-alert'>Email not found.</div>";
        exit;
    }


    $row = $result->fetch_assoc();
    $user_id = $row['users_id'];

    // Generate OTP
    $otp = rand(100000, 999999);

    // Store OTP in database
    $stmt = $conn->prepare("UPDATE users SET otp = ? WHERE users_id = ?");
    $stmt->bind_param("ii", $otp, $user_id);
    if ($stmt->execute()) {
        // Send OTP via email
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
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for Password Reset';
            $mail->Body = 'Your OTP is: ' . $otp;

            $mail->send();

            // Store email in session after successful OTP storage
            $_SESSION['email'] = $email;

            echo "<div class='alert alert-success'>OTP sent to your email. Please check. <a href='verify_otp.php'>Verify OTP</a></div>";
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Failed to store OTP.</div>";
    }
}
