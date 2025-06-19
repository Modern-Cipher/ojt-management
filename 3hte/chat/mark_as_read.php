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

$conn->begin_transaction();

try {
    // Update messages to read
    $sql = "UPDATE chat_messages 
            SET is_read = 'yes' 
            WHERE sender_id = ? AND receiver_id = ? AND is_read = 'no'";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Database query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("ii", $sender_id, $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to mark as read: " . $conn->error);
    }
    $stmt->close();

    // Clear notifications
    $notifSql = "DELETE FROM chat_notifications 
                 WHERE sender_id = ? AND receiver_id = ?";
    $notifStmt = $conn->prepare($notifSql);
    if (!$notifStmt) {
        throw new Exception("Notification query preparation failed: " . $conn->error);
    }
    $notifStmt->bind_param("ii", $sender_id, $user_id);
    if (!$notifStmt->execute()) {
        throw new Exception("Failed to clear notifications: " . $conn->error);
    }
    $notifStmt->close();

    $conn->commit();
    echo json_encode(["status" => "success", "message" => "Messages marked as read"]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$conn->close();
?>