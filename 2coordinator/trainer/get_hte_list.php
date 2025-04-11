<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$trainer_id = isset($_GET['trainer_id']) ? intval($_GET['trainer_id']) : 0;

// Fetch HTEs assigned to coordinator but not assigned to other trainers
$sql = "SELECT hte_id, hte_name 
        FROM hte 
        WHERE coordinator_id = ? 
        AND (trainer_id IS NULL OR trainer_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $trainer_id);
$stmt->execute();
$result = $stmt->get_result();

$hteList = [];
while ($row = $result->fetch_assoc()) {
    $hteList[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($hteList);
?>
