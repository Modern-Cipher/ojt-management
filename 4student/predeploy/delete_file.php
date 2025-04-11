<?php
session_start();
include("../../0config/database.php");

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['uploads_id']) || !isset($data['filepath'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
    exit();
}

$uploadId = $data['uploads_id'];
$filepath = $data['filepath'];

// Delete from database
$stmt = $conn->prepare("DELETE FROM uploads WHERE uploads_id = ?");
$stmt->bind_param("i", $uploadId);
if ($stmt->execute()) {
    // Delete file in directory
    if (file_exists($filepath)) {
        unlink($filepath);
    }
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to delete."]);
}
$stmt->close();
$conn->close();
?>
