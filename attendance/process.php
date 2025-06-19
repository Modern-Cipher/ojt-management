<?php
define('ROOT_DIR', realpath(__DIR__ . '/..'));
define('UPLOAD_DIR', ROOT_DIR . '/upload_selfie');
require_once ROOT_DIR . '/0config/database.php';

session_start();
$csrf_token = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
    error_log('Invalid CSRF token');
    die('Error: Invalid CSRF token');
}

function generateQRCodeData() {
    return bin2hex(random_bytes(16));
}

function generateRandomString($length = 6) {
    return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 6);
}

if (!is_dir(UPLOAD_DIR)) {
    if (!mkdir(UPLOAD_DIR, 0777, true)) {
        error_log('Failed to create upload directory: ' . UPLOAD_DIR);
        die('Error: Failed to create upload directory');
    }
}

if (!is_writable(UPLOAD_DIR)) {
    error_log('Upload directory is not writable: ' . UPLOAD_DIR);
    die('Error: Upload directory is not writable');
}

try {
    $school_id = filter_input(INPUT_POST, 'school_id', FILTER_SANITIZE_SPECIAL_CHARS);
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $middle_name = filter_input(INPUT_POST, 'middle_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $sex = filter_input(INPUT_POST, 'sex', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $institute = filter_input(INPUT_POST, 'institute', FILTER_SANITIZE_SPECIAL_CHARS);
    $course = filter_input(INPUT_POST, 'course', FILTER_SANITIZE_SPECIAL_CHARS);
    $selfie_data = $_POST['selfie_data'] ?? '';

    error_log('Received POST data: ' . print_r($_POST, true));

    if (empty($school_id) || empty($first_name) || empty($last_name) || empty($sex) || empty($email) || empty($institute) || empty($course)) {
        throw new Exception('All required fields must be filled.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format.');
    }

    if (!in_array($sex, ['Male', 'Female', 'Other'])) {
        throw new Exception('Invalid sex value.');
    }

    $stmt = $conn->prepare("SELECT attendance_id FROM attendance WHERE school_id = ?");
    $stmt->bind_param("s", $school_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        throw new Exception('School ID is already in use.');
    }
    $stmt->close();

    $stmt = $conn->prepare("SELECT attendance_id FROM attendance WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        throw new Exception('Email is already in use.');
    }
    $stmt->close();

    $selfie_path = null;
    if (!empty($selfie_data)) {
        if (!preg_match('/^data:image\/(jpeg|png);base64,/', $selfie_data)) {
            throw new Exception('Invalid selfie data format.');
        }

        $selfie_data = preg_replace('#^data:image/\w+;base64,#i', '', $selfie_data);
        $selfie_data = base64_decode($selfie_data, true);
        if ($selfie_data === false) {
            throw new Exception('Failed to decode selfie data.');
        }

        $randomString = generateRandomString(6);
        $filename = 'selfie_' . time() . '_' . $randomString . '.jpg';
        $selfie_path = 'upload_selfie/' . $filename;
        $full_path = UPLOAD_DIR . '/' . $filename;

        error_log('Attempting to save selfie to: ' . $full_path);

        if (!file_put_contents($full_path, $selfie_data)) {
            error_log('Failed to save selfie to: ' . $full_path);
            throw new Exception('Failed to save selfie.');
        }
    }

    $qr_code_data = generateQRCodeData();
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $device_info = $_SERVER['HTTP_USER_AGENT'];

    $stmt = $conn->prepare("
        INSERT INTO attendance (
            school_id, first_name, middle_name, last_name, sex, email, institute, course,
            selfie_image_path, ip_address, device_info, qr_code_data
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param(
        'ssssssssssss',
        $school_id,
        $first_name,
        $middle_name,
        $last_name,
        $sex,
        $email,
        $institute,
        $course,
        $selfie_path,
        $ip_address,
        $device_info,
        $qr_code_data
    );

    if (!$stmt->execute()) {
        error_log('Database execute failed: ' . $stmt->error);
        throw new Exception('Execute failed: ' . $stmt->error);
    }

    $attendance_id = $conn->insert_id;
    $stmt->close();

    error_log('Successfully inserted attendance record ID: ' . $attendance_id);

    // Set userRegistered flag in local storage via JavaScript on confirm.php
    header('Location: confirm.php?id=' . $attendance_id);
    exit;

} catch (Exception $e) {
    error_log('Error in process.php: ' . $e->getMessage());
    header('Location: attendance.php?error=' . urlencode($e->getMessage()));
    exit;
}
?>