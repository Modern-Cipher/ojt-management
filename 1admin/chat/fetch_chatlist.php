<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../0config/logout.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Get logged-in user's institute
$sql = "SELECT institute FROM users WHERE users_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Institute query preparation failed: " . $conn->error);
    echo json_encode(["status" => "error", "message" => "Database query preparation failed"]);
    exit();
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$currentUser = $result->fetch_assoc();
$institute = $currentUser['institute'];
$stmt->close();

// Fetch chatlist with latest message timestamp, time_in, time_out, and chat_stats
$sql = "
    SELECT 
        u.users_id,
        CONCAT(u.fname, ' ', u.lname) AS fullname,
        u.image_profile,
        u.chat_stats,
        u.role,
        u.time_in,
        u.time_out,
        h.hte_name,
        COUNT(CASE WHEN m.receiver_id = ? AND m.is_read = 'no' THEN 1 END) AS unread_count,
        MAX(m.created_at) AS latest_message_time
    FROM users u
    LEFT JOIN chat_messages m 
        ON (
            (m.sender_id = u.users_id AND m.receiver_id = ?)
            OR (m.sender_id = ? AND m.receiver_id = u.users_id)
        )
    LEFT JOIN hte h 
        ON 
            (u.role = 'student' AND u.hte_id = h.hte_id)
            OR (u.role = 'trainer' AND h.trainer_id = u.users_id)
    WHERE u.institute = ?
      AND u.users_id != ?
    GROUP BY u.users_id, u.fname, u.lname, u.image_profile, u.chat_stats, u.role, u.time_in, u.time_out, h.hte_name
    ORDER BY COALESCE(MAX(m.created_at), u.time_out, u.time_in) DESC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Chatlist query preparation failed: " . $conn->error);
    echo json_encode(["status" => "error", "message" => "Database query preparation failed"]);
    exit();
}
$stmt->bind_param("iiisi", $user_id, $user_id, $user_id, $institute, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
$onlineThreshold = 300; // 5 minutes in seconds

while ($row = $result->fetch_assoc()) {
    // Image path
    $imagePath = realpath(__DIR__ . "/../../upload_profile/" . $row['image_profile']);
    $image = !empty($row['image_profile']) && file_exists($imagePath)
        ? "../../upload_profile/" . $row['image_profile']
        : "../../upload_profile/siplogo.png";

    // Calculate duration and status
    $isOnline = $row['chat_stats'] === 'online';
    $duration = "";

    if ($isOnline) {
        // Online: no duration
        $duration = "";
    } elseif (!empty($row['time_out'])) {
        // Offline: calculate duration from time_out in Asia/Manila
        $lastActivity = new DateTime($row['time_out'], new DateTimeZone('Asia/Manila'));
        $current = new DateTime('now', new DateTimeZone('Asia/Manila'));
        $interval = $current->diff($lastActivity);
        $seconds = ($interval->days * 86400) + ($interval->h * 3600) + ($interval->i * 60) + $interval->s;

        if ($seconds < 60) {
            $duration = $seconds . "s ago";
        } elseif ($seconds < 3600) {
            $duration = floor($seconds / 60) . "m ago";
        } elseif ($seconds < 86400) {
            $duration = floor($seconds / 3600) . "h ago";
        } elseif ($seconds < 2592000) { // 30 days
            $duration = $interval->days . "d ago";
        } elseif ($seconds < 31536000) { // 365 days
            $duration = $interval->m . "mo ago";
        } else {
            $duration = $interval->y . "y ago";
        }
    } else {
        $duration = "No activity";
    }

    $users[] = [
        "users_id" => $row['users_id'],
        "fullname" => $row['fullname'],
        "image_profile" => $image,
        "hte_name" => $row['hte_name'] ?? "",
        "chat_stats" => $row['chat_stats'] ?? 'offline',
        "is_online" => $isOnline,
        "role" => $row['role'],
        "duration" => $duration,
        "unread_count" => $row['unread_count'] ?? 0
    ];
}

$stmt->close();
$conn->close();

error_log("Fetched " . count($users) . " users for chat list, user_id: $user_id, sorted by latest message");
header('Content-Type: application/json');
echo json_encode($users);
?>