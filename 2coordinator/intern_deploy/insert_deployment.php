<?php
session_start();
include("../../0config/database.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$student_id = $_POST['student_id'] ?? '';
$hte_id = $_POST['hte_id'] ?? '';
$ojt_status = $_POST['ojt_status'] ?? '';

if (empty($student_id) || empty($hte_id) || empty($ojt_status)) {
    echo json_encode(['error' => 'Missing fields']);
    exit;
}

// ✅ Insert student_id to HTE Table
$insertHte = $conn->prepare("UPDATE hte SET student_id = ? WHERE hte_id = ?");
$insertHte->bind_param("ii", $student_id, $hte_id);
$insertHte->execute();

// ✅ Update user's OJT status to 'Deployed'
$updateUser = $conn->prepare("UPDATE users SET ojt_status = ? WHERE users_id = ?");
$updateUser->bind_param("si", $ojt_status, $student_id);
$updateUser->execute();

echo json_encode(['success' => true]);
?>
