<?php
define('ROOT_DIR', realpath(__DIR__ . '/..'));
require_once ROOT_DIR . '/0config/database.php';

if (!isset($conn) || !($conn instanceof mysqli) || $conn->connect_error) {
    $error_message = isset($conn->connect_error) ? $conn->connect_error : 'Database connection not established';
    error_log('Database connection error: ' . $error_message);
    die('Error: Failed to connect to database');
}

$attendance_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$attendance_id) {
    error_log('Invalid attendance ID: ' . var_export($_GET['id'], true));
    header('Location: attendance.php?error=' . urlencode('Invalid attendance ID'));
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT school_id, first_name, middle_name, last_name, sex, email, institute, course,
               selfie_image_path, qr_code_data
        FROM attendance
        WHERE attendance_id = ?
    ");
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param('i', $attendance_id);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();

    if (!$data) {
        error_log('No attendance record found for ID: ' . $attendance_id);
        header('Location: attendance.php?error=' . urlencode('Attendance record not found'));
        exit;
    }

    if (empty($data['qr_code_data']) || !is_string($data['qr_code_data'])) {
        error_log('Invalid QR code data for attendance ID: ' . $attendance_id);
        header('Location: attendance.php?error=' . urlencode('Invalid QR code data'));
        exit;
    }

    $selfie_path = $data['selfie_image_path'] ? $data['selfie_image_path'] : null;
    $absolute_path = $selfie_path ? realpath(ROOT_DIR . '/upload_selfie/' . basename($selfie_path)) : null;
    if ($selfie_path && !$absolute_path) {
        error_log('Selfie image not found at: ' . ROOT_DIR . '/upload_selfie/' . basename($selfie_path));
        $selfie_path = null;
    }
} catch (Exception $e) {
    error_log('Error in confirm.php: ' . $e->getMessage());
    header('Location: attendance.php?error=' . urlencode('Database error: ' . $e->getMessage()));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container mt-5 confirm-container">
        <h1 class="text-center mb-4">Confirm Your Submission</h1>
        <div class="card" id="cardContent">
            <div class="card-body">
                <div class="card-content">
                    <?php if ($selfie_path): ?>
                        <img src="../upload_selfie/<?php echo htmlspecialchars(basename($selfie_path)); ?>" class="selfie-img" alt="Selfie">
                    <?php else: ?>
                        <p>No selfie provided or image not found.</p>
                    <?php endif; ?>
                    <div class="info-section">
                        <h5 class="mb-3">Submitted Information</h5>
                        <p><strong>School ID:</strong> <?php echo htmlspecialchars($data['school_id']); ?></p>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($data['first_name'] . ' ' . ($data['middle_name'] ? $data['middle_name'] . ' ' : '') . $data['last_name']); ?></p>
                        <p><strong>Sex:</strong> <?php echo htmlspecialchars($data['sex']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($data['email']); ?></p>
                        <p><strong>Institute:</strong> <?php echo htmlspecialchars($data['institute']); ?></p>
                        <p><strong>Course:</strong> <?php echo htmlspecialchars($data['course']); ?></p>
                    </div>
                    <div id="qrCode">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?php echo urlencode($data['qr_code_data']); ?>&margin=10&ecl=H" class="qr-code-img" alt="QR Code">
                    </div>
                </div>
                <div class="note-row">
                    <img src="../resources/siplogo.png" class="logo-img" alt="SIP Logo">
                    <p class="note-text"><strong>Note:</strong> Please download or screenshot this information for your records. Keep your QR code safe. It’s required to validate your attendance. If lost or not scanned, you’ll be marked absent or not attended for the event/place.</p>
                </div>
            </div>
        </div>
        <div class="button-row">
            <button type="button" id="resetBtn" class="btn btn-danger btn-wide">Reset</button>
            <button type="button" id="downloadBtn" class="btn btn-primary btn-wide">Download</button>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        const qrCodeData = <?php echo json_encode($data['qr_code_data'], JSON_HEX_QUOT | JSON_HEX_APOS); ?>;
        const attendanceId = <?php echo json_encode($attendance_id, JSON_HEX_QUOT | JSON_HEX_APOS); ?>;
    </script>
</body>
</html>