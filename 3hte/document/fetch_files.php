<?php
session_start();
include("../../0config/database.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT 
            f.filename_id, 
            f.filename, 
            f.count, 
            COALESCE(u.is_valid, ' ') AS upload_status, 
            COALESCE(DATE_FORMAT(u.updated_on, '%M %d, %Y - %h:%i %p'), ' ') AS updated_on
        FROM 
            filename f
        LEFT JOIN 
            uploads u 
        ON 
            f.filename_id = u.filename_id 
        AND 
            u.uploadedby_id = ?
        ORDER BY 
            f.count ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$files = [];
while ($row = $result->fetch_assoc()) {
    $files[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode(['files' => $files]);
?>
