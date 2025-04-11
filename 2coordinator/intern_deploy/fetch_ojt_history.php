<?php
session_start();
include("../../0config/database.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$student_id = $_GET['student_id'] ?? null;

if (!$student_id) {
    echo json_encode(['error' => 'Student ID is required']);
    exit();
}

$sql = "
    SELECT 
        h.old_status, 
        h.new_status, 
        DATE_FORMAT(h.date_assigned, '%M %d, %Y - %h:%i %p') AS date_assigned,
        DATE_FORMAT(h.date_changed, '%M %d, %Y - %h:%i %p') AS date_changed,
        old_hte.hte_name AS old_hte_name,
        new_hte.hte_name AS new_hte_name
    FROM ojt_status_history h
    LEFT JOIN hte old_hte ON h.old_hte_id = old_hte.hte_id
    LEFT JOIN hte new_hte ON h.new_hte_id = new_hte.hte_id
    WHERE h.student_id = ?
    ORDER BY h.date_changed DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode(['history' => $history]);
?>
