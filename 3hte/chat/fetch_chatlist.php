<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../0config/logout.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get trainer's institute and hte_id from hte table
$sql = "
    SELECT u.institute, h.hte_id
    FROM users u
    LEFT JOIN hte h ON h.trainer_id = u.users_id
    WHERE u.users_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$trainerData = $result->fetch_assoc();
$stmt->close();

$institute = $trainerData['institute'];
$hte_id = $trainerData['hte_id'];

// Fetch only students and coordinators in same institute and hte_id
// Fetch only students and coordinators in same institute and hte_id
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
        ON (h.hte_id = u.hte_id OR h.coordinator_id = u.users_id)
    WHERE u.institute = ?
      AND h.hte_id = ?
      AND u.role IN ('student', 'coordinator')
      AND u.users_id != ?
    GROUP BY u.users_id
    ORDER BY latest_message DESC
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("isis", $user_id, $institute, $hte_id, $user_id);
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
