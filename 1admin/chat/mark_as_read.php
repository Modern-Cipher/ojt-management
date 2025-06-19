<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$sender_id = isset($_POST['sender_id']) ? intval($_POST['sender_id']) : 0;

if (!$sender_id) {
    echo json_encode(["status" => "error", "message" => "Invalid sender ID"]);
    exit();
}

$sql = "UPDATE chat_messages 
        SET is_read = 'yes' 
        WHERE sender_id = ? AND receiver_id = ? AND is_read = 'no'";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database query preparation failed"]);
    exit();
}
$stmt->bind_param("ii", $sender_id, $user_id);
if ($stmt->execute()) {
    // Clear notifications
    $notifSql = "DELETE FROM chat_notifications 
                 WHERE sender_id = ? AND receiver_id = ?";
    $notifStmt = $conn->prepare($notifSql);
    $notifStmt->bind_param("ii", $sender_id, $user_id);
    $notifStmt->execute();
    $notifStmt->close();

    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to mark as read"]);
}
$stmt->close();
$conn->close();
?>