<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo json_encode(["error" => "Unauthorized or missing student ID"]);
    exit();
}

$student_id = intval($_GET['id']);

$sql = "
    SELECT f.filename, u.filepath, u.upload_status, u.updated_on
    FROM uploads u
    JOIN filename f ON u.filename_id = f.filename_id
    WHERE u.uploadedby_id = ?
      AND f.category = 'pre'
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$documents = [];
while ($row = $result->fetch_assoc()) {
    $documents[] = [
        "filename" => $row['filename'],
        "filepath" => $row['filepath'],
        "upload_status" => $row['upload_status'],
        "updated_on" => $row['updated_on']
    ];
}

echo json_encode($documents);
?>
