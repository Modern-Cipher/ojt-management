<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../0config/logout.php");
    exit();
}

$user_id = $_SESSION['user_id'];
date_default_timezone_set('Asia/Manila');

$sql = "SELECT institute, hte_id, ojt_stats FROM users WHERE users_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("User query preparation failed: " . $conn->error);
    echo json_encode(["status" => "error", "message" => "Database query preparation failed"]);
    exit();
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$currentUser = $result->fetch_assoc();
$stmt->close();

$institute = $currentUser['institute'];
$hte_id = $currentUser['hte_id'];
$ojt_stats = $currentUser['ojt_stats'];

$users = [];
$onlineThreshold = 300;

// BASE QUERY
$sql = "
    SELECT 
        u.users_id,
        CONCAT(u.fname, ' ', u.lname) AS fullname,
        u.image_profile,
        u.chat_stats,
        u.role,
        u.time_in,
        u.time_out,
        u.designation,
        NULL AS hte_name,
        COUNT(CASE WHEN m.receiver_id = ? AND m.is_read = 'no' THEN 1 END) AS unread_count,
        MAX(m.created_at) AS latest_message_time
    FROM users u
    LEFT JOIN chat_messages m 
        ON (
            (m.sender_id = u.users_id AND m.receiver_id = ?)
            OR (m.sender_id = ? AND m.receiver_id = u.users_id)
        )
    WHERE u.users_id != ?
      AND u.institute = ?
      AND (
          (u.role = 'admin' AND u.users_id = (
              SELECT MIN(users_id) FROM users WHERE role = 'admin' AND institute = ? AND users_account = 'enabled'
          )) OR
          (u.role = 'coordinator' AND u.users_id = (
              SELECT MIN(users_id) FROM users 
              WHERE role = 'coordinator' AND institute = ? AND users_account = 'enabled'
          ))
      )
    GROUP BY u.users_id, u.fname, u.lname, u.image_profile, u.chat_stats, u.role, u.time_in, u.time_out, u.designation
    ORDER BY COALESCE(MAX(m.created_at), u.time_out, u.time_in) DESC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Base query preparation failed: " . $conn->error);
    echo json_encode(["status" => "error", "message" => "Database query preparation failed"]);
    exit();
}

$stmt->bind_param(
    "iiissss",
    $user_id,
    $user_id,
    $user_id,
    $user_id,
    $institute,
    $institute,
    $institute
);

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $imagePath = realpath(__DIR__ . "/../../upload_profile/" . $row['image_profile']);
    $image = !empty($row['image_profile']) && file_exists($imagePath)
        ? "../../upload_profile/" . $row['image_profile']
        : "../../upload_profile/siplogo.png";

    $isOnline = $row['chat_stats'] === 'online';
    $duration = "";

    if ($isOnline) {
        $duration = "";
    } elseif (!empty($row['time_out'])) {
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
        } elseif ($seconds < 2592000) {
            $duration = $interval->days . "d ago";
        } elseif ($seconds < 31536000) {
            $duration = $interval->m . "mo ago";
        } else {
            $duration = $interval->y . "y ago";
        }
    } else {
        $duration = "No activity";
    }

    $users[$row['users_id']] = [
        "users_id" => $row['users_id'],
        "fullname" => $row['fullname'],
        "image_profile" => $image,
        "hte_name" => "",
        "designation" => $row['designation'] ?? "",
        "chat_stats" => $row['chat_stats'] ?? 'offline',
        "is_online" => $isOnline,
        "role" => $row['role'],
        "duration" => $duration,
        "unread_count" => $row['unread_count'] ?? 0
    ];
}

$stmt->close();

// DEPLOYED QUERY
if ($ojt_stats === 'deployed' && $hte_id) {
    $sql = "
        SELECT 
            u.users_id,
            CONCAT(u.fname, ' ', u.lname) AS fullname,
            u.image_profile,
            u.chat_stats,
            u.role,
            u.time_in,
            u.time_out,
            u.designation,
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
            ON (
                (u.role = 'student' AND h.hte_id = u.hte_id AND h.hte_id = ?) OR
                (u.role = 'trainer' AND h.trainer_id = u.users_id AND h.hte_id = ?) OR
                (u.role = 'coordinator' AND h.coordinator_id = u.users_id AND h.hte_id = ?)
            )
        WHERE u.users_id != ?
          AND u.institute = ?
          AND (
              (u.role = 'coordinator' AND u.users_id IN (
                  SELECT coordinator_id FROM hte WHERE hte_id = ? AND coordinator_id IS NOT NULL
              )) OR
              (u.role = 'trainer' AND u.users_id = (
                  SELECT MIN(trainer_id) FROM hte WHERE hte_id = ? AND trainer_id IS NOT NULL
              )) OR
              (u.role = 'student' AND u.hte_id = ? AND u.institute = ? AND u.ojt_stats = 'deployed')
          )
        GROUP BY u.users_id, u.fname, u.lname, u.image_profile, u.chat_stats, u.role, u.time_in, u.time_out, u.designation
        ORDER BY COALESCE(MAX(m.created_at), u.time_out, u.time_in) DESC
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Deployed query preparation failed: " . $conn->error);
        echo json_encode(["status" => "error", "message" => "Database query preparation failed"]);
        exit();
    }

    $stmt->bind_param(
        "iiiiisssiiii",
        $user_id,
        $user_id,
        $user_id,
        $hte_id,
        $hte_id,
        $hte_id,
        $user_id,
        $institute,
        $hte_id,
        $hte_id,
        $hte_id,
        $institute
    );

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $imagePath = realpath(__DIR__ . "/../../upload_profile/" . $row['image_profile']);
        $image = !empty($row['image_profile']) && file_exists($imagePath)
            ? "../../upload_profile/" . $row['image_profile']
            : "../../upload_profile/siplogo.png";

        $isOnline = $row['chat_stats'] === 'online';
        $duration = "";

        if ($isOnline) {
            $duration = "";
        } elseif (!empty($row['time_out'])) {
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
            } elseif ($seconds < 2592000) {
                $duration = $interval->days . "d ago";
            } elseif ($seconds < 31536000) {
                $duration = $interval->m . "mo ago";
            } else {
                $duration = $interval->y . "y ago";
            }
        } else {
            $duration = "No activity";
        }

        $hte_name = ($row['role'] === 'student' || $row['role'] === 'trainer') ? ($row['hte_name'] ?? "") : "";

        $users[$row['users_id']] = [
            "users_id" => $row['users_id'],
            "fullname" => $row['fullname'],
            "image_profile" => $image,
            "hte_name" => $hte_name,
            "designation" => $row['designation'] ?? "",
            "chat_stats" => $row['chat_stats'] ?? 'offline',
            "is_online" => $isOnline,
            "role" => $row['role'],
            "duration" => $duration,
            "unread_count" => $row['unread_count'] ?? 0
        ];
    }

    $stmt->close();
}

$conn->close();

// Convert associative array to indexed array
$users = array_values($users);

error_log("Fetched " . count($users) . " users for chat list, user_id: $user_id, institute: $institute, hte_id: " . ($hte_id ?? 'null') . ", ojt_stats: $ojt_stats");
header('Content-Type: application/json');
echo json_encode($users);
?>
