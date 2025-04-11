<?php
session_start();
include("../../0config/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message_id = $_POST['message_id'];

    // Get sender and receiver for notification
    $sql = "SELECT sender_id, receiver_id FROM chat_messages WHERE message_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $message = $result->fetch_assoc();
    $stmt->close();

    if ($message) {
        // Delete message
        $sql = "DELETE FROM chat_messages WHERE message_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $message_id);
        $stmt->execute();
        $stmt->close();

        // Delete related notification
        $notifSql = "DELETE FROM chat_notifications 
                     WHERE related_message_id = ? 
                     AND sender_id = ? 
                     AND receiver_id = ?";
        $stmt = $conn->prepare($notifSql);
        $stmt->bind_param("iii", $message_id, $message['sender_id'], $message['receiver_id']);
        $stmt->execute();
        $stmt->close();

        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Message not found"]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
