<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

$user_id = $_SESSION['user_id'];
$file_path = isset($_GET['path']) ? $_GET['path'] : '';

if (empty($file_path) || strpos($file_path, 'upload_chatfiles/') !== 0) {
    header("HTTP/1.1 400 Bad Request");
    exit();
}

$sql = "SELECT message, sender_id, receiver_id 
        FROM chat_messages 
        WHERE message LIKE ? 
        AND (sender_id = ? OR receiver_id = ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    header("HTTP/1.1 500 Internal Server Error");
    exit("Database query preparation failed");
}
$likePath = "Attachment: " . $file_path . "%";
$stmt->bind_param("sii", $likePath, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$message = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$message) {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

$parts = explode("|", $message['message']);
$filePath = "../../" . $file_path;
$fileType = isset($parts[2]) ? $parts[2] : 'application/octet-stream';
$originalName = isset($parts[1]) ? $parts[1] : basename($file_path);

if (!file_exists($filePath)) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

header("Content-Type: " . $fileType);
header("Content-Disposition: inline; filename=\"" . $originalName . "\"");
header("Content-Length: " . filesize($filePath));
readfile($filePath);
exit();
?>