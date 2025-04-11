<?php
session_start();
include("../0config/database.php");

header('Content-Type: application/json');

$otp = $_POST['otp'];

if (empty($otp)) {
    echo json_encode(["status" => "error", "message" => "OTP field is required."]);
    exit();
}

// Check if OTP matches in the database
$query = $conn->prepare("SELECT email FROM users WHERE otp = ?");
$query->bind_param("s", $otp);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid OTP. Please try again."]);
    exit();
}

// Store user's email in session for password reset
$row = $result->fetch_assoc();
$_SESSION['reset_email'] = $row['email'];

// Clear OTP from database after verification
$clearOtpQuery = $conn->prepare("UPDATE users SET otp = NULL WHERE email = ?");
$clearOtpQuery->bind_param("s", $_SESSION['reset_email']);
$clearOtpQuery->execute();

echo json_encode(["status" => "success", "message" => "OTP verified successfully! Redirecting..."]);
?>
