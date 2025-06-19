<?php
header('Content-Type: application/json');
$database_path = '../0config/database.php';
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
    // Fetch attendance record
    $stmt = $conn->prepare("
        SELECT school_id, first_name, middle_name, last_name, sex, email, institute, course,
               selfie_image_path, ip_address
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

    // Check IP address match
    $scanned_ip = $_SERVER['REMOTE_ADDR'];
    $ip_match = $scanned_ip === $data['ip_address'];

    // Update scan details
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

    echo json_encode([
        'success' => true,
        'data' => $data,
        'ip_match' => $ip_match
    ]);
} catch (Exception $e) {
    error_log('Error in process_scan.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>