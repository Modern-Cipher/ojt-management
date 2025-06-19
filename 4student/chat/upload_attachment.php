<?php
session_start();
include("../../0config/database.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['attachment']) || !$receiver_id) {
    echo json_encode(["status" => "error", "message" => "Invalid request or no file uploaded"]);
    exit();
}

$file = $_FILES['attachment'];
$uploadDir = "../../upload_chatfiles/";
$allowedTypes = [
    'image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp',
    'application/pdf',
    'application/msword', // .doc
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
    'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'text/plain', 'text/csv',
    'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
];
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv', 'ppt', 'pptx'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["status" => "error", "message" => "File upload error: " . $file['error']]);
    exit();
}

$fileType = mime_content_type($file['tmp_name']);
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

// Fallback for application/octet-stream
if ($fileType === 'application/octet-stream' && in_array($extension, $allowedExtensions)) {
    switch ($extension) {
        case 'doc':
            $fileType = 'application/msword';
            break;
        case 'docx':
            $fileType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            break;
        case 'pdf':
            $fileType = 'application/pdf';
            break;
        case 'xls':
            $fileType = 'application/vnd.ms-excel';
            break;
        case 'xlsx':
            $fileType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            break;
        case 'csv':
            $fileType = 'text/csv';
            break;
        case 'ppt':
            $fileType = 'application/vnd.ms-powerpoint';
            break;
        case 'pptx':
            $fileType = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
            break;
        case 'txt':
            $fileType = 'text/plain';
            break;
        default:
            $fileType = null;
    }
}

if (!in_array($fileType, $allowedTypes)) {
    echo json_encode(["status" => "error", "message" => "Unsupported file type: " . $fileType]);
    exit();
}

if ($file['size'] > 10485760) { // 10MB limit
    echo json_encode(["status" => "error", "message" => "File size exceeds 10MB"]);
    exit();
}

// Sanitize filename
$originalName = pathinfo($file['name'], PATHINFO_FILENAME);
$originalName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
$randomString = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 6);
$newFilename = $originalName . '_' . $randomString . '.' . $extension;
$destination = $uploadDir . $newFilename;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    echo json_encode(["status" => "error", "message" => "Failed to save file"]);
    exit();
}

$sql = "INSERT INTO chat_messages (sender_id, receiver_id, message, is_read, created_at) 
        VALUES (?, ?, ?, 'no', NOW())";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    unlink($destination);
    echo json_encode(["status" => "error", "message" => "Database query preparation failed"]);
    exit();
}
$filePath = "upload_chatfiles/" . $newFilename;
$message = "Attachment: {$filePath}|{$file['name']}|{$fileType}";
$stmt->bind_param("iis", $user_id, $receiver_id, $message);
if (!$stmt->execute()) {
    unlink($destination);
    echo json_encode(["status" => "error", "message" => "Failed to save message"]);
    exit();
}
$messageId = $stmt->insert_id;
$stmt->close();

$notifSql = "INSERT INTO chat_notifications 
    (receiver_id, sender_id, message, is_read, related_message_id, created_at) 
    VALUES (?, ?, ?, 'no', ?, NOW())";
$notifStmt = $conn->prepare($notifSql);
if (!$notifStmt) {
    echo json_encode(["status" => "error", "message" => "Database query preparation failed"]);
    exit();
}
$notifMsg = "New upload: " . $file['name'];
$notifStmt->bind_param("iisi", $receiver_id, $user_id, $notifMsg, $messageId);
$notifStmt->execute();
$notifStmt->close();

echo json_encode(["status" => "success", "message_id" => $messageId]);
$conn->close();
?>