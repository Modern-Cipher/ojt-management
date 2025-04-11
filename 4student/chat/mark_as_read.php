<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sender_id'])) {
    $sender_id = $_POST['sender_id'];

    $sql = "UPDATE chat_messages 
            SET is_read = 1 
            WHERE sender_id = ? 
              AND receiver_id = ? 
              AND is_read = 0";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $sender_id, $user_id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}

$conn->close();
