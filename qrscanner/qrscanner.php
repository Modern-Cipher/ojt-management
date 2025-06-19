<?php
define('ROOT_DIR', realpath(__DIR__ . '/..'));
$database_path = ROOT_DIR . '/0config/database.php';
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
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container my-4">
        <h1 class="text-center mb-4">QR Code Scanner</h1>
        <div class="row scanner-row">
            <div class="col-md-5 scanner-section">
                <video id="video" class="w-100" autoplay></video>
                <canvas id="canvas" style="display: none;"></canvas>
                <div class="scanner-overlay"></div>
                <select id="cameraSelect" class="form-select mt-3"></select>
            </div>
            <div class="col-md-7 data-section">
                <div id="dataDisplay" class="card">
                    <div class="card-body shimmer-content">
                        <div class="shimmer-circle mx-auto"></div>
                        <div class="shimmer-text mt-3">
                            <div class="shimmer-line"></div>
                            <div class="shimmer-line"></div>
                            <div class="shimmer-line"></div>
                        </div>
                    </div>
                </div>
                <button id="resetDisplay" class="btn btn-secondary mt-2 btn-reset">Reset Display</button>
            </div>
        </div>
        <div class="row badge-row mt-3">
            <div class="col-12">
                <div id="badgeContainer" class="badge-container"></div>
            </div>
        </div>
    </div>

    <!-- Modal for image zoom -->
    <div class="modal fade" id="imageZoomModal" tabindex="-1" aria-labelledby="imageZoomModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageZoomModalLabel">Student Selfie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="zoomedImage" src="" alt="Selfie" class="img-fluid" style="max-height: 500px;">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>