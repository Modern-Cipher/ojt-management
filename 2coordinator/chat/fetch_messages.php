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

// Fetch receiver's profile image
$sql = "SELECT image_profile FROM users WHERE users_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database query preparation failed"]);
    exit();
}
$stmt->bind_param("i", $with_user_id);
$stmt->execute();
$result = $stmt->get_result();
$receiver = $result->fetch_assoc();
$stmt->close();

$receiverImagePath = !empty($receiver['image_profile']) ? realpath(__DIR__ . "/../../upload_profile/" . $receiver['image_profile']) : '';
$receiverImage = !empty($receiver['image_profile']) && file_exists($receiverImagePath)
    ? "../../upload_profile/" . $receiver['image_profile']
    : "../../upload_profile/siplogo.png";

// Debug logging
error_log("Receiver ID: $with_user_id, Image: " . ($receiver['image_profile'] ?? 'null') . ", Path: $receiverImagePath, Exists: " . (file_exists($receiverImagePath) ? "yes" : "no"));

// Fetch current user's profile image
$sql = "SELECT image_profile FROM users WHERE users_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database query preparation failed"]);
    exit();
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$current = $result->fetch_assoc();
$stmt->close();

$currentUserImagePath = !empty($current['image_profile']) ? realpath(__DIR__ . "/../../upload_profile/" . $current['image_profile']) : '';
$currentUserImage = !empty($current['image_profile']) && file_exists($currentUserImagePath)
    ? "../../upload_profile/" . $current['image_profile']
    : "../../upload_profile/siplogo.png";

// Debug logging
error_log("Current User ID: $user_id, Image: " . ($current['image_profile'] ?? 'null') . ", Path: $currentUserImagePath, Exists: " . (file_exists($currentUserImagePath) ? "yes" : "no"));

// Fetch messages
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
    // Fetch newer messages only
    $sql .= " AND message_id > ?";
    $params[] = $last_id;
    $types .= "i";
    $sql .= " ORDER BY created_at ASC";
} else {
    // Fetch latest 50 messages in DESC order for initial load
    $sql .= " ORDER BY created_at DESC LIMIT 50";
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database query preparation failed"]);
    exit();
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $date = new DateTime($row['created_at']);
    $formatted = $date->format('M j, Y - g:ia');

    $attachment = null;
    if (strpos($row['message'], 'Attachment: ') === 0) {
        $parts = explode("|", $row['message']);
        if (count($parts) >= 3) {
            $filePath = str_replace("Attachment: ", "", $parts[0]);
            $attachment = [
                'file_path' => $filePath,
                'original_name' => $parts[1],
                'file_type' => $parts[2]
            ];
        }
    }

    $messages[] = [
        "message_id" => $row['message_id'],
        "sender_id" => $row['sender_id'],
        "receiver_id" => $row['receiver_id'],
        "message" => $attachment ? "Attachment: " . $parts[1] : $row['message'],
        "created_at" => $formatted,
        "attachment" => $attachment
    ];
}
$stmt->close();
$conn->close();

// Reverse messages for initial load to display in ASC order client-side
if ($last_id == 0) {
    $messages = array_reverse($messages);
    error_log("Fetched " . count($messages) . " messages in DESC order, reversed for client");
} else {
    error_log("Fetched " . count($messages) . " messages in ASC order");
}

echo json_encode([
    "status" => "success",
    "messages" => $messages,
    "current_user" => $user_id,
    "current_user_image" => $currentUserImage,
    "receiver_image" => $receiverImage
]);
?>