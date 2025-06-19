<?php
session_start();
header('Content-Type: application/json');
require("../../0config/database.php");

$data = json_decode(file_get_contents("php://input"), true);

$filename = trim($data['filename'] ?? '');
$user_id = $_SESSION['user_id'] ?? null;

if (!$filename || !$user_id) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM filename WHERE filename = ? AND user_id = ? AND category = 'journal'");
$stmt->bind_param("si", $filename, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database delete failed.']);
}

$stmt->close();
$conn->close();
?>