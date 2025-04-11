<?php
session_start();
include("../0config/database.php");

header('Content-Type: application/json');

if (!isset($_SESSION['reset_email'])) {
    echo json_encode(["status" => "error", "message" => "Session expired. Restart the process."]);
    exit();
}

$new_password = $_POST['new_password'];
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

// Update password in database
$updateQuery = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$updateQuery->bind_param("ss", $hashed_password, $_SESSION['reset_email']);
$updateQuery->execute();

// Destroy session after password reset
unset($_SESSION['reset_email']);

echo json_encode(["status" => "success", "message" => "Password updated successfully! Redirecting..."]);
?>
