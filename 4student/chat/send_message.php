<?php
session_start();
include("../../0config/database.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$currentUser = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

$receiverId = isset($data['receiver_id']) ? intval($data['receiver_id']) : 0;
$message = isset($data['message']) ? trim($data['message']) : '';

if (empty($receiverId) || empty($message)) {
    echo json_encode(["status" => "error", "message" => "Receiver ID or message is empty"]);
    exit();
}

$conn->begin_transaction();

try {
    // Insert message
    $sql = "INSERT INTO chat_messages (sender_id, receiver_id, message, is_read, created_at) 
            VALUES (?, ?, ?, 'no', NOW())";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Database query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("iis", $currentUser, $receiverId, $message);
    if (!$stmt->execute()) {
        throw new Exception("Failed to send message: " . $stmt->error);
    }
    $messageId = $stmt->insert_id;
    $stmt->close();

    // Insert notification
    $notifSql = "INSERT INTO chat_notifications 
                 (receiver_id, sender_id, message, is_read, related_message_id, created_at) 
                 VALUES (?, ?, ?, 'no', ?, NOW())";
    $notifStmt = $conn->prepare($notifSql);
    if (!$notifStmt) {
        throw new Exception("Database query preparation failed: " . $conn->error);
    }
    $notifMsg = substr($message, 0, 50) . (strlen($message) > 50 ? '...' : '');
    $notifStmt->bind_param("iisi", $receiverId, $currentUser, $notifMsg, $messageId);
    $notifStmt->execute();
    $notifStmt->close();

    $conn->commit();
    echo json_encode(["status" => "success", "message_id" => $messageId]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$conn->close();
?>