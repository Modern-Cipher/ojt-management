<?php
session_start();
include("../../0config/database.php");

// Make sure the student is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch ONLY journal entries for that student
$sql = "SELECT filename, count 
        FROM filename 
        WHERE category = 'journal' AND user_id = ?
        ORDER BY CAST(count AS UNSIGNED) ASC"; // ensures 10 > 2

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$journalEntries = [];
while ($row = $result->fetch_assoc()) {
    $journalEntries[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($journalEntries);
