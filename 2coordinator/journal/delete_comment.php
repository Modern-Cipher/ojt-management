<?php
session_start();
include("../../0config/database.php");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$coordinatorId = $_SESSION['user_id'];

if (isset($data['commentId'])) {
    $commentId = $data['commentId'];

    // Check if the comment belongs to the logged-in coordinator
    $stmt = $conn->prepare("SELECT commenter_id FROM file_comments WHERE file_comment_id = ?");
    $stmt->bind_param("i", $commentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $comment = $result->fetch_assoc();
    $stmt->close();

    if ($comment && $comment['commenter_id'] == $coordinatorId) {
        $stmt = $conn->prepare("DELETE FROM file_comments WHERE file_comment_id = ?");
        $stmt->bind_param("i", $commentId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No rows affected.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'You can only delete your own comment.']);
    }
}
$conn->close();
?>
