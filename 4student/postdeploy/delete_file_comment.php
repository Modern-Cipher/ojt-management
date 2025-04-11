<?php
include '../../0config/database.php';
session_start();

$data = json_decode(file_get_contents("php://input"), true);
$comment_id = $data['file_comment_id'] ?? null;
$commenter_id = $_SESSION['user_id'] ?? null;

if (!$comment_id || !$commenter_id) {
    echo json_encode(["status" => "error", "message" => "Missing data"]);
    exit;
}

$query = "
    DELETE FROM file_comments 
    WHERE file_comment_id = ? AND commenter_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $comment_id, $commenter_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to delete comment"]);
}
?>
