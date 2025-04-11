<?php
include("../../0config/database.php");

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['commentId'])) {
    $commentId = $data['commentId'];

    $stmt = $conn->prepare("DELETE FROM file_comments WHERE file_comment_id = ?");
    $stmt->bind_param("i", $commentId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No rows affected.']);
    }
    $stmt->close();
}
$conn->close();
?>
