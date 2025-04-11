<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../0config/logout.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get current student's institute and hte_id
$sql = "SELECT institute, hte_id FROM users WHERE users_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$currentUser = $result->fetch_assoc();
$stmt->close();

$institute = $currentUser['institute'];
$hte_id = $currentUser['hte_id'];

// Fetch chatlist
$sql = "
    SELECT 
        u.users_id,
        CONCAT(u.fname, ' ', u.lname) AS fullname,
        u.image_profile,
        u.chat_stats,
        u.role,
        h.hte_name,
        MAX(m.created_at) AS latest_message,
        SUM(CASE WHEN m.receiver_id = ? AND m.is_read = 0 THEN 1 ELSE 0 END) AS unread_count
    FROM users u
    LEFT JOIN chat_messages m 
        ON (m.sender_id = u.users_id OR m.receiver_id = u.users_id)
    LEFT JOIN hte h 
        ON (
            (u.role = 'student' AND h.hte_id = u.hte_id) OR
            (u.role = 'trainer' AND h.trainer_id = u.users_id) OR
            (u.role = 'coordinator' AND u.institute = ?)
        )
    WHERE u.users_id != ?
      AND (
          (u.role = 'student' AND u.hte_id = ?) OR
          (u.role = 'coordinator' AND u.institute = ?) OR
          (u.role = 'trainer' AND h.hte_id = ?)
      )
    GROUP BY u.users_id
    ORDER BY latest_message DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isissi", $user_id, $institute, $user_id, $hte_id, $institute, $hte_id);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $image = !empty($row['image_profile']) && file_exists("../../upload_profile/" . $row['image_profile'])
        ? "../../upload_profile/" . $row['image_profile']
        : "../../upload_profile/siplogo.png";

    $duration = "";
    if (!empty($row['latest_message'])) {
        $current = new DateTime();
        $last = new DateTime($row['latest_message']);
        $interval = $current->diff($last);

        if ($interval->d > 0) {
            $duration = $interval->d . "d ago";
        } elseif ($interval->h > 0) {
            $duration = $interval->h . "h ago";
        } elseif ($interval->i > 0) {
            $duration = $interval->i . "m ago";
        } else {
            $duration = "Just now";
        }
    }

    $users[] = [
        "users_id" => $row['users_id'],
        "fullname" => $row['fullname'],
        "image_profile" => $image,
        "hte_name" => $row['hte_name'],
        "chat_stats" => $row['chat_stats'],
        "role" => $row['role'],
        "duration" => $duration,
        "unread_count" => $row['unread_count'] ?? 0
    ];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($users);
?>
