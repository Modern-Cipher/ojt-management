<?php
session_start();
include("../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$trainer_id = $_SESSION['user_id'];
$response = [];

// Get students deployed to this trainer (via hte_id)
$sql = "
    SELECT COUNT(*) as student_count
    FROM users
    WHERE role = 'student'
    AND ojt_stats = 'deployed'
    AND hte_id = (
        SELECT hte_id FROM hte WHERE trainer_id = ?
    )";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$response['students_deployed'] = $result['student_count'] ?? 0;

// Count submitted documents (uploads from these students)
$sql = "
    SELECT COUNT(*) as document_count
    FROM uploads
    WHERE uploadedby_id IN (
        SELECT users_id FROM users
        WHERE role = 'student'
        AND ojt_stats = 'deployed'
        AND hte_id = (SELECT hte_id FROM hte WHERE trainer_id = ?)
    )";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$response['documents_submitted'] = $result['document_count'] ?? 0;

header("Content-Type: application/json");
echo json_encode($response);
?>
