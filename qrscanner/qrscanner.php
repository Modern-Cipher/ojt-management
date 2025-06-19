<?php
$database_path = '../0config/database.php';
if (!file_exists($database_path)) {
    error_log('Database connection file not found at: ' . $database_path);
    die('Error: Database connection file not found.');
}
include_once $database_path;

if (!isset($conn) || !($conn instanceof mysqli) || $conn->connect_error) {
    $error_message = isset($conn->connect_error) ? $conn->connect_error : 'Database connection not established';
    error_log('Database connection error: ' . $error_message);
    die('Error: Failed to connect to database');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5 scanner-container">
        <h1 class="text-center mb-4">QR Code Scanner</h1>
        <div class="scanner-row">
            <div class="scanner-section">
                <video id="video" width="300" height="300" autoplay></video>
                <canvas id="canvas" style="display: none;"></canvas>
                <div class="scanner-overlay"></div>
            </div>
            <div class="data-section">
                <div id="dataDisplay" class="card shimmer">
                    <div class="card-body">
                        <div class="shimmer-content">
                            <div class="shimmer-circle"></div>
                            <div class="shimmer-text">
                                <div class="shimmer-line"></div>
                                <div class="shimmer-line"></div>
                                <div class="shimmer-line"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>