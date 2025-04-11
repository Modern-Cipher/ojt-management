<?php
session_start();
include("../../0config/database.php");
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$sql = "
SELECT 
    f.filename_id, 
    f.filename, 
    f.count, 
    u.uploads_id, 
    u.filepath, 
    u.upload_status, 
    u.updated_on,
    u.checkedby_id,
    CONCAT(c.fname, ' ', c.lname) AS checker_name
FROM filename f
LEFT JOIN uploads u ON f.filename_id = u.filename_id AND u.uploadedby_id = ?
LEFT JOIN users c ON u.checkedby_id = c.users_id
WHERE f.category = 'journal' AND f.count != 0
ORDER BY f.count ASC
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$files = [];
while ($row = $result->fetch_assoc()) {
    $files[] = [
        'filename_id' => $row['filename_id'],
        'filename' => $row['filename'],
        'count' => $row['count'],
        'uploads_id' => $row['uploads_id'],
        'filepath' => $row['filepath'],
        'upload_status' => $row['upload_status'],
        'updated_on' => $row['updated_on'],
        'checker_name' => $row['checker_name'] ?? " ",
    ];
}

echo json_encode(['files' => $files]);

$stmt->close();
$conn->close();
