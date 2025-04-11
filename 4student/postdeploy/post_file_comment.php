<?php
session_start();
include '../../0config/database.php';

$data = json_decode(file_get_contents("php://input"), true);

$filename_id = $data['filename_id'] ?? null;
$comment = $data['comment'] ?? null;
$commenter_id = $_SESSION['user_id'] ?? null;
$uploadedby_id = $data['uploadedby_id'] ?? null; // optional

if (!$filename_id || !$comment || !$commenter_id) {
    echo json_encode(["status" => "error", "message" => "Missing data"]);
    exit;
}

// ✅ If checker nag-comment (uploadedby_id required)
if ($uploadedby_id) {
    $query = "
        INSERT INTO file_comments (filename_id, commenter_id, uploadedby_id, comment)
        VALUES (?, ?, ?, ?)
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiis", $filename_id, $commenter_id, $uploadedby_id, $comment);
} else {
    // ✅ Normal comment (uploader)
    $query = "
        INSERT INTO file_comments (filename_id, commenter_id, comment)
        VALUES (?, ?, ?)
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $filename_id, $commenter_id, $comment);
}

$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to insert comment"]);
}
?>
