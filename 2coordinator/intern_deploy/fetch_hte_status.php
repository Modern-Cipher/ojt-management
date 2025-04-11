<?php
session_start();
include("../../0config/database.php");
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$coordinator_id = $_SESSION['user_id'];
$student_id = $_GET['student_id'] ?? null;

// Fetch current student's hte_id
$currentHteId = null;
if ($student_id) {
    $stmt = $conn->prepare("SELECT hte_id FROM users WHERE users_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($currentHteId);
    $stmt->fetch();
    $stmt->close();
}

// Fetch all HTEs assigned to coordinator
$hte_names = [];
$sql = "SELECT hte_id, hte_name FROM hte WHERE coordinator_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $coordinator_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $hte_names[] = [
        'id' => $row['hte_id'],
        'name' => $row['hte_name'],
        'assigned' => ($row['hte_id'] == $currentHteId) ? true : false
    ];
}
$stmt->close();

$data = [
    'hte_names' => $hte_names,
    'ojt_statuses' => ['deployed', 'pending', 'pulled-out']
];

echo json_encode($data);
?>
