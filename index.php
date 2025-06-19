<?php
date_default_timezone_set('Asia/Manila');
// Include database connection
include_once '0config/database.php';

// Test database connection
if ($conn->connect_error) {
    echo '<div class="loader"></div>';
    die("Database connection failed: " . $conn->connect_error);
} else {
    echo "<script>console.log('Database connected successfully');</script>";
}

// Redirect to login.php
header("Location: login/login.php");
// header("Location: registration/regs.php");
exit;
