<?php
header('Content-Type: application/json');
define('ROOT_DIR', realpath(__DIR__ . '/..'));
define('UPLOAD_DIR', ROOT_DIR . '/upload_selfie');
require_once ROOT_DIR . '/0config/database.php';

if (!isset($conn) || !($conn instanceof mysqli) || $conn->connect_error) {
    error_log('Database connection error: ' . ($conn->connect_error ?? 'Connection not established'));
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$attendance_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$attendance_id) {
    error_log('Invalid attendance ID: ' . var_export($_GET['id'], true));
    echo json_encode(['success' => false, 'message' => 'Invalid attendance ID']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT selfie_image_path FROM attendance WHERE attendance_id = ?");
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
        throw new Exception('Attendance record not found');
    }

    $selfie_path = $data['selfie_image_path'];
    if ($selfie_path && file_exists(UPLOAD_DIR . '/' . basename($selfie_path))) {
        if (!unlink(UPLOAD_DIR . '/' . basename($selfie_path))) {
            error_log('Failed to delete selfie image: ' . UPLOAD_DIR . '/' . basename($selfie_path));
        } else {
            error_log('Successfully deleted selfie image: ' . UPLOAD_DIR . '/' . basename($selfie_path));
        }
    }

    $stmt = $conn->prepare("DELETE FROM attendance WHERE attendance_id = ?");
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param('i', $attendance_id);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    $stmt->close();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log('Error in delete.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>