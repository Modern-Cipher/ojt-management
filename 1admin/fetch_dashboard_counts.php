<?php
session_start();
include("../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT institute FROM users WHERE users_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$institute = $user['institute'] ?? null;
$stmt->close();

if (!$institute) {
    echo json_encode(["error" => "Institute not found"]);
    exit();
}

$response = [
    "total_interns" => 0,
    "ongoing" => 0,
    "pending" => 0,
    "completed" => 0,
    "establishment" => 0
];

$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE role = 'student' AND institute = ?");
$stmt->bind_param("s", $institute);
$stmt->execute();
$stmt->bind_result($response['total_interns']);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE role = 'student' AND institute = ? AND ojt_stats = 'deployed'");
$stmt->bind_param("s", $institute);
$stmt->execute();
$stmt->bind_result($response['ongoing']);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE role = 'student' AND institute = ? AND ojt_stats = 'pending'");
$stmt->bind_param("s", $institute);
$stmt->execute();
$stmt->bind_result($response['pending']);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) FROM hte WHERE coordinator_id IN (SELECT users_id FROM users WHERE institute = ? AND role = 'coordinator')");
$stmt->bind_param("s", $institute);
$stmt->execute();
$stmt->bind_result($response['establishment']);
$stmt->fetch();
$stmt->close();

$conn->close();
echo json_encode($response);
?>
