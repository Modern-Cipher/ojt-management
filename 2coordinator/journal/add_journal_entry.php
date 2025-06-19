<?php
session_start();
header('Content-Type: application/json');
require("../../0config/database.php");

$data = json_decode(file_get_contents("php://input"), true);

$filename = trim($data['filename'] ?? '');
$count = trim($data['count'] ?? '');
$user_id = $_SESSION['user_id'] ?? null;

if (!$filename || !$count || !$user_id) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
    exit;
}

// Validate count
if (!is_numeric($count) || $count < 1 || $count > 20) {
    echo json_encode(['success' => false, 'error' => 'Invalid week number.']);
    exit;
}

// Check for duplicate count
$stmt = $conn->prepare("SELECT COUNT(*) FROM filename WHERE count = ? AND user_id = ? AND category = 'journal'");
$stmt->bind_param("si", $count, $user_id);
$stmt->execute();
$stmt->bind_result($existing_count);
$stmt->fetch();
$stmt->close();

if ($existing_count > 0) {
    echo json_encode(['success' => false, 'error' => 'This week is already assigned.']);
    exit;
}

// Insert new entry (omit id to let auto-increment handle it)
$stmt = $conn->prepare("INSERT INTO filename (filename, count, category, updated_at, user_id) VALUES (?, ?, 'journal', NOW(), ?)");
$stmt->bind_param("ssi", $filename, $count, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database insert failed.']);
}

$stmt->close();
$conn->close();
?>