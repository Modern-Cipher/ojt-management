<?php
header('Content-Type: application/json');
define('ROOT_DIR', realpath(__DIR__ . '/..'));
define('IMAGE_UPLOAD_DIR', 'upload_selfie/');

$database_path = ROOT_DIR . '/0config/database.php';
if (!file_exists($database_path)) {
    error_log('Database connection file not found at: ' . $database_path);
    echo json_encode(['success' => false, 'message' => 'Database connection file not found']);
    exit;
}
include_once $database_path;

if (!isset($conn) || !($conn instanceof mysqli) || $conn->connect_error) {
    error_log('Database connection error: ' . ($conn->connect_error ?? 'Connection not established'));
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$qr_code_data = $input['qr_code_data'] ?? '';

if (empty($qr_code_data)) {
    echo json_encode(['success' => false, 'message' => 'Invalid QR code data']);
    exit;
}

try {
    // Check if QR code exists and its scan status
    $stmt = $conn->prepare("
        SELECT school_id, first_name, middle_name, last_name, sex, email, institute, course,
               selfie_image_path, ip_address, is_qr_scanned, scanned_timestamp
        FROM attendance
        WHERE qr_code_data = ?
    ");
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param('s', $qr_code_data);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();

    if (!$data) {
        throw new Exception('No attendance record found for this QR code');
    }

    // Handle image path
    $selfie_path = $data['selfie_image_path'] ? trim($data['selfie_image_path']) : null;
    $image_base_path = rtrim(ROOT_DIR, '/') . '/' . IMAGE_UPLOAD_DIR;
    $image_filename = $selfie_path ? basename($selfie_path) : null;
    $absolute_path = $image_filename ? realpath($image_base_path . $image_filename) : null;
    $relative_web_path = $image_filename ? '../' . IMAGE_UPLOAD_DIR . $image_filename : '../resources/placeholder.png';

    if ($selfie_path && $image_filename && $absolute_path && file_exists($absolute_path)) {
        $data['selfie_image_path'] = $relative_web_path;
    } else {
        $data['selfie_image_path'] = '../resources/placeholder.png';
        error_log('Selfie image not found. Details: ' .
                  'QR Code: ' . $qr_code_data . ', ' .
                  'Database selfie_path: ' . ($selfie_path ?: 'NULL') . ', ' .
                  'Filename: ' . ($image_filename ?: 'N/A') . ', ' .
                  'Attempted path: ' . ($image_filename ? $image_base_path . $image_filename : 'N/A') . ', ' .
                  'Absolute path: ' . ($absolute_path ?: 'N/A') . ', ' .
                  'File exists: ' . ($absolute_path ? (file_exists($absolute_path) ? 'Yes' : 'No') : 'N/A'));
    }

    // Check IP address and scan status
    $scanned_ip = $_SERVER['REMOTE_ADDR'];
    $ip_match = $scanned_ip === $data['ip_address'];
    $already_scanned = $data['is_qr_scanned'] == 1;

    $messages = [];
    if (!$already_scanned) {
        // Record attendance for first scan
        $scanned_timestamp = date('Y-m-d H:i:s');
        $scanned_device_info = $_SERVER['HTTP_USER_AGENT'];
        $is_qr_scanned = 1;

        $stmt = $conn->prepare("
            UPDATE attendance
            SET is_qr_scanned = ?, scanned_timestamp = ?, scanned_by_ip_address = ?, scanned_by_device_info = ?
            WHERE qr_code_data = ?
        ");
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param('issss', $is_qr_scanned, $scanned_timestamp, $scanned_ip, $scanned_device_info, $qr_code_data);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        $stmt->close();

        $messages = ['QR Validated', 'Attendance Recorded'];
    } else {
        $messages = ['Already Scanned'];
        if ($ip_match) {
            $messages[] = 'Similar Device';
        }
    }

    echo json_encode([
        'success' => true,
        'data' => $data,
        'messages' => $messages
    ]);
} catch (Exception $e) {
    error_log('Error in process_scan.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>