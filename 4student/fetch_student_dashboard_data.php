<?php
session_start();
include("../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch deployment status
$sql = "SELECT ojt_stats FROM users WHERE users_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$status = $user['ojt_stats'] ?? 'unknown';
$stmt->close();

// Count submissions per category
$categories = ['pre', 'post', 'journal'];
$counts = [
    'pre' => 0,
    'post' => 0,
    'journal' => 0
];

foreach ($categories as $cat) {
    $sql = "SELECT COUNT(*) as count FROM uploads 
            INNER JOIN filename ON uploads.filename_id = filename.filename_id
            WHERE filename.category = ? AND uploads.uploadedby_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $cat, $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $counts[$cat] = $row['count'] ?? 0;
    $stmt->close();
}

echo json_encode([
    "status" => $status,
    "pre_submitted" => $counts['pre'],
    "post_submitted" => $counts['post'],
    "journal_submitted" => $counts['journal']
]);
?>
