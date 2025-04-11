<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../0config/logout.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get form inputs
$title = trim($_POST['title']);
$content = trim($_POST['content']);
$role = $_POST['role'];
$status = $_POST['status'];
$notified = ($status === 'posted') ? 1 : 0;

// ✅ Fetch coordinator's institute
$institute = '';
$stmt = $conn->prepare("SELECT institute FROM users WHERE users_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($institute);
$stmt->fetch();
$stmt->close();

// ✅ Insert announcement
$stmt = $conn->prepare("INSERT INTO announcements (users_id, title, content, role, announce_status, notified) 
                        VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssi", $user_id, $title, $content, $role, $status, $notified);
$stmt->execute();
$announcement_id = $stmt->insert_id;
$stmt->close();

// ✅ Send notifications (if posted)
if ($status === 'posted') {
    if ($role === 'all') {
        $userQuery = "SELECT users_id FROM users WHERE institute = ? AND users_id != ?";
        $userStmt = $conn->prepare($userQuery);
        $userStmt->bind_param("si", $institute, $user_id);
    } else {
        $userQuery = "SELECT users_id FROM users WHERE role = ? AND institute = ? AND users_id != ?";
        $userStmt = $conn->prepare($userQuery);
        $userStmt->bind_param("ssi", $role, $institute, $user_id);
    }

    $userStmt->execute();
    $result = $userStmt->get_result();

    $notifInsert = "INSERT INTO notifications (receiver_id, sender_id, type, source_id, message, is_read, created_at)
                    VALUES (?, ?, 'announcements', ?, ?, 0, NOW())";
    $notifStmt = $conn->prepare($notifInsert);

    while ($row = $result->fetch_assoc()) {
        $receiver_id = $row['users_id'];
        $message = "New announcement: " . $title;
        $notifStmt->bind_param("iiis", $receiver_id, $user_id, $announcement_id, $message);
        $notifStmt->execute();
    }

    $notifStmt->close();
    $userStmt->close();
}

$conn->close();
header("Location: announcement.php?success=1");
exit();
?>
