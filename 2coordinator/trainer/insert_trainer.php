<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo "unauthorized";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch coordinator's institute & course
$detailsQuery = "SELECT institute, course FROM users WHERE users_id = ?";
$detailsStmt = $conn->prepare($detailsQuery);
$detailsStmt->bind_param("i", $user_id);
$detailsStmt->execute();
$detailsResult = $detailsStmt->get_result();
$details = $detailsResult->fetch_assoc();
$detailsStmt->close();

if (!$details) {
    echo "unauthorized";
    exit();
}

$institute = $details['institute'];
$course = $details['course'];

// Sanitize inputs
$fname = trim($_POST['fname']);
$lname = trim($_POST['lname']);
$email = !empty($_POST['email']) ? trim($_POST['email']) : null;
$username = trim($_POST['username']);
$designation = !empty($_POST['designation']) ? trim($_POST['designation']) : null;
$sex = isset($_POST['sex']) ? trim($_POST['sex']) : null;
$password = $_POST['password'];

// VALIDATE IF USERNAME OR EMAIL EXISTS
$checkQuery = "SELECT * FROM users WHERE username = ? OR email = ?";
$checkStmt = $conn->prepare($checkQuery);
$checkStmt->bind_param("ss", $username, $email);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo "exists";
    exit();
}
$checkStmt->close();

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Default values
$defaultImage = "siplogo.png";
$role = "trainer";
$chat_stats = "offline";
$users_account = "enabled";
$created_on = date('Y-m-d H:i:s');
$temppass = $hashedPassword; // for temp pass storage

// Insert Query
$insertQuery = "INSERT INTO users (
    coordinator_id, fname, lname, email, username, designation, sex, 
    password, temppass, role, institute, course, users_account, chat_stats, 
    image_profile, created_on
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$insertStmt = $conn->prepare($insertQuery);
$insertStmt->bind_param(
    "isssssssssssssss",
    $user_id, $fname, $lname, $email, $username, $designation, $sex,
    $hashedPassword, $temppass, $role, $institute, $course, $users_account, $chat_stats,
    $defaultImage, $created_on
);

if ($insertStmt->execute()) {
    echo "success";
} else {
    echo "error: " . $insertStmt->error;
}

$insertStmt->close();
$conn->close();
?>
