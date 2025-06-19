<?php
session_start();
include("../../0config/database.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit();
}

$message_id = isset($_POST['message_id']) ? intval($_POST['message_id']) : 0;

if (!$message_id) {
    echo json_encode(["status" => "error", "message" => "Invalid message ID"]);
    exit();
}

$conn->begin_transaction();

try {
    // Fetch message details
    $sql = "SELECT sender_id, receiver_id, message 
            FROM chat_messages 
            WHERE message_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Database query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $message = $result->fetch_assoc();
    $stmt->close();

    // If message not found, check if it was already deleted
    if (!$message) {
        // Check if message_id exists in chat_messages
        $sql_check = "SELECT COUNT(*) AS count FROM chat_messages WHERE message_id = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("i", $message_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row = $result_check->fetch_assoc();
        $stmt_check->close();

        if ($row['count'] == 0) {
            echo json_encode(["status" => "success", "message" => "Message already deleted"]);
            $conn->commit();
            exit();
        } else {
            throw new Exception("Message not found");
        }
    }

    // Delete attachment file if exists
    if (strpos($message['message'], 'Attachment: ') === 0) {
        $parts = explode("|", $message['message']);
        $filePath = "../../" . str_replace("Attachment: ", "", $parts[0]);
        if (file_exists($filePath)) {
            if (!unlink($filePath)) {
                throw new Exception("Failed to delete attachment file");
            }
        }
    }

    // Delete message
    $sql = "DELETE FROM chat_messages WHERE message_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Database query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("i", $message_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to delete message: " . $conn->error);
    }
    $stmt->close();

    // Delete notifications
    $notifSql = "DELETE FROM chat_notifications 
                 WHERE related_message_id = ? 
                 AND sender_id = ? 
                 AND receiver_id = ?";
    $notifStmt = $conn->prepare($notifSql);
    if (!$notifStmt) {
        throw new Exception("Database query preparation failed: " . $conn->error);
    }
    $notifStmt->bind_param("iii", $message_id, $message['sender_id'], $message['receiver_id']);
    $notifStmt->execute();
    $notifStmt->close();

    $conn->commit();
    echo json_encode(["status" => "success", "message" => "Message deleted successfully"]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$conn->close();
?>