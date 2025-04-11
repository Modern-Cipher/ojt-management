<?php
include("../0config/database.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get the institute of the logged-in user
$getInstitute = "SELECT institute FROM users WHERE users_id = ?";
$stmt = $conn->prepare($getInstitute);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$institute = $user['institute'] ?? null;

if (!$institute) {
    echo json_encode(["error" => "Institute not found"]);
    exit();
}

// Count students grouped by ojt_stats
$sql = "SELECT ojt_stats, COUNT(*) AS total 
        FROM users 
        WHERE role = 'student' AND institute = ?
        GROUP BY ojt_stats";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $institute);
$stmt->execute();
$result = $stmt->get_result();

$data = [
    "deployed" => 0,
    "pending" => 0,
    "pulled_out" => 0
];

while ($row = $result->fetch_assoc()) {
    $status = strtolower($row['ojt_stats']);
    if (isset($data[$status])) {
        $data[$status] = (int)$row['total'];
    }
}

echo json_encode($data);
