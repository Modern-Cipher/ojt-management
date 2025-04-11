<?php
session_start();
include("../../0config/database.php");
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_POST['user_id'] ?? null;
$hte_id = $_POST['hte_id'] ?? null;
$ojt_status = $_POST['ojt_status'] ?? null;

if (!$user_id) {
    echo json_encode(['error' => 'Missing student ID']);
    exit();
}

// Get current data
$sql = "SELECT hte_id, ojt_stats FROM users WHERE users_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($currentHte, $currentStatus);
$stmt->fetch();
$stmt->close();

// Build dynamic query
$updates = [];
$params = [];
$types = '';

if (!empty($hte_id) && $hte_id != $currentHte) {
    $updates[] = "hte_id = ?";
    $params[] = $hte_id;
    $types .= 'i';
}

if (!empty($ojt_status) && $ojt_status != $currentStatus) {
    $updates[] = "ojt_stats = ?";
    $params[] = $ojt_status;
    $types .= 's';
}

if (empty($updates)) {
    echo json_encode(['success' => false, 'error' => 'No changes detected']);
    exit();
}

$sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE users_id = ?";
$params[] = $user_id;
$types .= 'i';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}
$stmt->close();
?>
