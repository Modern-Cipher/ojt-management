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
$sql = "SELECT institute FROM users WHERE users_id = ? AND role = 'coordinator'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $coordinator_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $institute = $row['institute'];
} else {
    echo json_encode(["error" => "Institute not found"]);
    exit();
}

// Initialize document counts
$docCounts = [
    "pre" => 0,
    "post" => 0,
    "hte" => 0,
    "journal" => 0
];

// Query uploads joined with filename and users
$query = "SELECT f.category, COUNT(DISTINCT u.uploadedby_id) as total
          FROM uploads u
          JOIN filename f ON u.filename_id = f.filename_id
          JOIN users us ON u.uploadedby_id = us.users_id
          WHERE us.role = 'student' AND us.institute = ?
          GROUP BY f.category";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $institute);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $category = strtolower($row['category']);
    if (isset($docCounts[$category])) {
        $docCounts[$category] = (int) $row['total'];
    }
}

header("Content-Type: application/json");
echo json_encode($docCounts);
