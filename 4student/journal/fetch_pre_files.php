<?php
session_start();
include("../../0config/database.php");
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Fetch the logged-in user's institute, course, and hte_id
$sql = "SELECT institute, course, hte_id FROM users WHERE users_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$institute = $user['institute'] ?? null;
$course = $user['course'] ?? null;
$hte_id = $user['hte_id'] ?? null;
$stmt->close();

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
    CONCAT(c.fname, ' ', c.lname) AS checker_name,
    COALESCE(u.original_file_name, '-') AS original_file_name
FROM filename f
LEFT JOIN uploads u ON f.filename_id = u.filename_id AND u.uploadedby_id = ?
LEFT JOIN users c ON u.checkedby_id = c.users_id
INNER JOIN users u2 ON f.user_id = u2.users_id
WHERE f.category = 'journal' 
    AND f.count != 0
    AND (
        u2.institute = ? 
        OR u2.course = ? 
        OR u2.hte_id = ?
        OR u2.institute IS NULL AND ? IS NULL
        OR u2.course IS NULL AND ? IS NULL
        OR u2.hte_id IS NULL AND ? IS NULL
    )
ORDER BY f.count ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isssiii", $user_id, $institute, $course, $hte_id, $institute, $course, $hte_id);
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
        'checker_name' => $row['checker_name'] ?? "N/A",
        'original_file_name' => $row['original_file_name']
    ];
}

echo json_encode(['files' => $files]);

$stmt->close();
$conn->close();
?>