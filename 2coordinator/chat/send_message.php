<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$currentUser = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

$receiverId = $data['receiver_id'];
$message = $data['message'];

if (empty($receiverId) || empty($message)) {
    echo json_encode(["status" => "error", "message" => "Empty fields"]);
    exit();
}

// Insert message
$sql = "INSERT INTO chat_messages (sender_id, receiver_id, message, is_read, created_at) 
        VALUES (?, ?, ?, 'no', NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $currentUser, $receiverId, $message);

if ($stmt->execute()) {
    $messageId = $stmt->insert_id;
    $stmt->close();

    // Insert notification
    $notifSql = "INSERT INTO chat_notifications 
        (receiver_id, sender_id, message, is_read, related_message_id, created_at) 
        VALUES (?, ?, ?, 'no', ?, NOW())";
    $notifStmt = $conn->prepare($notifSql);
    $notifMsg = $message; // Optional, pwede mo palitan ng "You have a new message"
    $notifStmt->bind_param("iisi", $receiverId, $currentUser, $notifMsg, $messageId);
    $notifStmt->execute();
    $notifStmt->close();

    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to send"]);
}

$conn->close();
?>
