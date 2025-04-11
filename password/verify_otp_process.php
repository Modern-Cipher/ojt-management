<?php
session_start();
require_once '../0config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    $email = $_SESSION['email']; // Assuming you stored email in session

    // Retrieve stored OTP from database
    $stmt = $conn->prepare("SELECT otp FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_otp = $row['otp'];

        if ($otp == $stored_otp) {
            // Clear OTP in database after successful verification
            $stmt = $conn->prepare("UPDATE users SET otp = NULL WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();

            echo "<div class='alert alert-success'>OTP verified. <a href='reset_password.php'>Reset password</a></div>";
        } else {
            echo "<div class='alert alert-danger'>Invalid OTP.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>User not found.</div>";
    }
}
?>