<?php
session_start();
include("../../0config/database.php");
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$coordinator_id = $_SESSION['user_id'];

$response = [
    'hte_names' => [],
    'ojt_statuses' => ['deployed', 'pending', 'pulled-out']
];

$sql = "SELECT hte_id, hte_name FROM hte WHERE coordinator_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $coordinator_id);
$stmt->execute();
$result = $stmt->get_result();

$response['hte_names'][] = ['hte_id' => '', 'hte_name' => 'Select Host Establishment']; // placeholder
while ($row = $result->fetch_assoc()) {
    $response['hte_names'][] = $row;
}
$stmt->close();
$conn->close();

echo json_encode($response);
?>
