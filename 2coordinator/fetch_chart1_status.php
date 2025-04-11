<?php
session_start();
include("../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$coordinator_id = $_SESSION['user_id'];

// Get coordinator's institute
$institute = '';
$instituteSql = "SELECT institute FROM users WHERE users_id = ? AND role = 'coordinator'";
$instituteStmt = $conn->prepare($instituteSql);
$instituteStmt->bind_param("i", $coordinator_id);
$instituteStmt->execute();
$result = $instituteStmt->get_result();
if ($row = $result->fetch_assoc()) {
    $institute = $row['institute'];
} else {
    echo json_encode(["error" => "Institute not found"]);
    exit();
}

// Initialize counts
$counts = [
    "deployed" => 0,
    "pending" => 0,
    "pulled_out" => 0
];

// Fetch each ojt_stats count
$ojtQuery = "SELECT ojt_stats, COUNT(*) as count 
             FROM users 
             WHERE role = 'student' AND institute = ? 
             GROUP BY ojt_stats";

$stmt = $conn->prepare($ojtQuery);
$stmt->bind_param("s", $institute);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $status = $row['ojt_stats'];
    $counts[$status] = (int) $row['count'];
}

header("Content-Type: application/json");
echo json_encode($counts);
