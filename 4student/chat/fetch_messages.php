<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$with_user_id = isset($_GET['with_user_id']) ? intval($_GET['with_user_id']) : 0;
$last_id = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;

if (!$with_user_id) {
    echo json_encode(["status" => "error", "message" => "Invalid user"]);
    exit();
}

// ===== Fetch receiver image =====
$sql = "SELECT image_profile FROM users WHERE users_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $with_user_id);
$stmt->execute();
$result = $stmt->get_result();
$receiver = $result->fetch_assoc();
$stmt->close();

$receiverImage = (!empty($receiver['image_profile']) && file_exists("../../upload_profile/" . $receiver['image_profile']))
    ? "../../upload_profile/" . $receiver['image_profile']
    : "../../upload_profile/siplogo.png";

// ===== Fetch current user image =====
$sql = "SELECT image_profile FROM users WHERE users_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$current = $result->fetch_assoc();
$stmt->close();

$currentUserImage = (!empty($current['image_profile']) && file_exists("../../upload_profile/" . $current['image_profile']))
    ? "../../upload_profile/" . $current['image_profile']
    : "../../upload_profile/siplogo.png";

// ===== Fetch messages =====
$sql = "
    SELECT message_id, sender_id, receiver_id, message, created_at
    FROM chat_messages
    WHERE (
        (sender_id = ? AND receiver_id = ?)
        OR (sender_id = ? AND receiver_id = ?)
    )
";

$params = [$user_id, $with_user_id, $with_user_id, $user_id];
$types = "iiii";

if ($last_id > 0) {
    $sql .= " AND message_id > ?";
    $params[] = $last_id;
    $types .= "i";
}

$sql .= " ORDER BY created_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $date = new DateTime($row['created_at']);
    $formatted = $date->format('M j, Y - g:ia');

    $messages[] = [
        "message_id" => $row['message_id'],
        "sender_id" => $row['sender_id'],
        "receiver_id" => $row['receiver_id'],
        "message" => $row['message'],
        "created_at" => $formatted
    ];
}
$stmt->close();
$conn->close();

// ===== Final Response =====
echo json_encode([
    "status" => "success",
    "messages" => $messages,
    "current_user" => $user_id,
    "current_user_image" => $currentUserImage,
    "receiver_image" => $receiverImage
]);
?>
