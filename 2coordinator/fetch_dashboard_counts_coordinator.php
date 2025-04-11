<?php
session_start();
include("../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$coordinator_id = $_SESSION['user_id'];

// Fetch the coordinator's institute
$institute = '';
$instituteSql = "SELECT institute FROM users WHERE users_id = ? AND role = 'coordinator'";
$instituteStmt = $conn->prepare($instituteSql);
$instituteStmt->bind_param("i", $coordinator_id);
$instituteStmt->execute();
$instituteResult = $instituteStmt->get_result();

if ($instituteRow = $instituteResult->fetch_assoc()) {
    $institute = $instituteRow['institute'];
} else {
    echo json_encode(["error" => "Institute not found"]);
    exit();
}

$response = [];

// ✅ Total Interns
$sql = "SELECT COUNT(*) as total FROM users WHERE role = 'student' AND institute = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $institute);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$response['total_interns'] = $result['total'] ?? 0;

// ✅ Ongoing (deployed)
$sql = "SELECT COUNT(*) as ongoing FROM users WHERE role = 'student' AND institute = ? AND ojt_stats = 'deployed'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $institute);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$response['ongoing'] = $result['ongoing'] ?? 0;

// ✅ Pending (not deployed)
$sql = "SELECT COUNT(*) as pending FROM users WHERE role = 'student' AND institute = ? AND ojt_stats = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $institute);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$response['pending'] = $result['pending'] ?? 0;

// ✅ Establishments created by this coordinator
$sql = "SELECT COUNT(*) as estabs FROM hte WHERE coordinator_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $coordinator_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$response['establishment'] = $result['estabs'] ?? 0;

// ✅ Placeholder for completed
$response['completed'] = 0;

header("Content-Type: application/json");
echo json_encode($response);
