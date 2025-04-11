<?php
include("../../0config/database.php");

if (!isset($_GET['trainer_id'])) {
    echo json_encode(["error" => "Trainer ID missing"]);
    exit();
}

$trainer_id = $_GET['trainer_id'];

$sql = "SELECT 
            f.filename,
            u.file_name,
            u.filepath,
            u.upload_status,
            u.updated_on
        FROM uploads u
        INNER JOIN filename f ON u.filename_id = f.filename_id
        WHERE u.uploadedby_id = ? AND f.category = 'hte'
        ORDER BY u.updated_on DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$result = $stmt->get_result();

$documents = [];
while ($row = $result->fetch_assoc()) {
    $documents[] = [
        "filename" => $row['filename'],
        "file_name" => $row['file_name'],
        "filepath" => $row['filepath'],
        "upload_status" => $row['upload_status'],
        "updated_on" => $row['updated_on'] ? date("F d, Y - h:i A", strtotime($row['updated_on'])) : ''
    ];
}

echo json_encode($documents);
