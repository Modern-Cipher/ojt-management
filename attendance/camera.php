<?php
define('ROOT_DIR', realpath(__DIR__ . '/..'));
require_once ROOT_DIR . '/0config/database.php';

session_start();
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selfie Capture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container mt-5 camera-page">
        <h1 class="text-center mb-4">Capture Selfie</h1>
        <form id="cameraForm" action="process.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" id="school_id" name="school_id">
            <input type="hidden" id="first_name" name="first_name">
            <input type="hidden" id="middle_name" name="middle_name">
            <input type="hidden" id="last_name" name="last_name">
            <input type="hidden" id="sex" name="sex">
            <input type="hidden" id="email" name="email">
            <input type="hidden" id="institute" name="institute">
            <input type="hidden" id="course" name="course">
            <div class="camera-container">
                <div class="mb-2">
                    <label for="cameraSelect" class="form-label">Select Camera</label>
                    <select class="form-select" id="cameraSelect">
                        <option value="" disabled selected>Loading cameras...</option>
                    </select>
                </div>
                <video id="video" class="img-fluid" autoplay></video>
                <canvas id="canvas" style="display: none;"></canvas>
                <div class="d-flex justify-content-center gap-2 mt-2 camera-buttons">
                    <button type="button" id="selectImageBtn" class="btn btn-secondary btn-wide">Select Image</button>
                    <button type="button" id="captureBtn" class="btn btn-primary btn-wide">Capture Selfie</button>
                </div>
                <input type="file" id="imageInput" accept="image/jpeg,image/png" style="display: none;">
                <div id="previewSection" class="d-none mt-3">
                    <img id="preview" class="img-fluid">
                    <div class="d-flex justify-content-center mt-2">
                        <button type="button" id="confirmBtn" class="btn btn-success me-2">Confirm</button>
                        <button type="button" id="retakeBtn" class="btn btn-secondary">Retake</button>
                    </div>
                </div>
                <input type="hidden" id="selfie_data" name="selfie_data">
            </div>
            <div id="submitSection" class="text-center mt-3 d-none">
                <button type="submit" class="btn btn-success btn-wide">Submit</button>
            </div>
        </form>
        <div class="text-center mt-3">
            <button type="button" id="backBtn" class="btn btn-secondary">Back to Form</button>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>