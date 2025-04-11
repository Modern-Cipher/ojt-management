<?php
session_start();
require_once '../0config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['email'];

    // Validation
    if ($password != $confirm_password) {
        echo "<div class='alert alert-danger'>Passwords do not match.</div>";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update password in database
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Password updated successfully. <a href='../login/login.php'>Login</a></div>";
        unset($_SESSION['email']); // Clear session
    } else {
        echo "<div class='alert alert-danger'>Failed to update password.</div>";
    }
}
?>