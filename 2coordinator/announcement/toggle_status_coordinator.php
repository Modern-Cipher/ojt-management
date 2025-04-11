<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$announcement_id = $_POST['id'];
$new_status = $_POST['status'];
$notified = ($new_status === 'posted') ? 1 : 0;

// Step 1: Update announcement status
$updateSql = "UPDATE announcements 
              SET announce_status = ?, notified = ? 
              WHERE announcements_id = ? AND users_id = ?";
$updateStmt = $conn->prepare($updateSql);
$updateStmt->bind_param("siii", $new_status, $notified, $announcement_id, $user_id);
$updateStmt->execute();
$updateStmt->close();

// Step 2: If status is changed to drafted → delete announcement & comment notifications + comments
if ($new_status === 'drafted') {
    // Delete announcement notifications
    $deleteNotif = $conn->prepare("DELETE FROM notifications WHERE type = 'announcements' AND source_id = ? AND sender_id = ?");
    $deleteNotif->bind_param("ii", $announcement_id, $user_id);
    $deleteNotif->execute();
    $deleteNotif->close();

    // Delete comment notifications tied to this announcement
    $deleteCommentNotifs = $conn->prepare("DELETE FROM notifications WHERE type = 'comments' AND source_id IN (SELECT comments_id FROM comments WHERE announcements_id = ?)");
    $deleteCommentNotifs->bind_param("i", $announcement_id);
    $deleteCommentNotifs->execute();
    $deleteCommentNotifs->close();

    // Delete all comments linked to this announcement
    $deleteComments = $conn->prepare("DELETE FROM comments WHERE announcements_id = ?");
    $deleteComments->bind_param("i", $announcement_id);
    $deleteComments->execute();
    $deleteComments->close();
}

// Step 3: If status is posted → insert new announcement notifications
if ($new_status === 'posted') {
    // Fetch announcement details
    $annSql = "SELECT title, role FROM announcements WHERE announcements_id = ?";
    $stmt = $conn->prepare($annSql);
    $stmt->bind_param("i", $announcement_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $announcement = $result->fetch_assoc();
    $stmt->close();

    // Get user's institute
    $instSql = "SELECT institute FROM users WHERE users_id = ?";
    $stmt = $conn->prepare($instSql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $institute = $result->fetch_assoc()['institute'];
    $stmt->close();

    // Determine recipients
    if ($announcement['role'] === 'all') {
        $targetSql = "SELECT users_id FROM users WHERE institute = ? AND users_id != ?";
        $stmt = $conn->prepare($targetSql);
        $stmt->bind_param("si", $institute, $user_id);
    } else {
        $targetSql = "SELECT users_id FROM users WHERE role = ? AND institute = ? AND users_id != ?";
        $stmt = $conn->prepare($targetSql);
        $stmt->bind_param("ssi", $announcement['role'], $institute, $user_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $notifSql = "INSERT INTO notifications (receiver_id, sender_id, type, source_id, message, is_read, created_at)
                 VALUES (?, ?, 'announcements', ?, ?, 0, NOW())";
    $notifStmt = $conn->prepare($notifSql);

    while ($row = $result->fetch_assoc()) {
        $receiver_id = $row['users_id'];
        $message = "New announcement: " . $announcement['title'];
        $notifStmt->bind_param("iiis", $receiver_id, $user_id, $announcement_id, $message);
        $notifStmt->execute();
    }

    $notifStmt->close();
    $stmt->close();
}

$conn->close();
echo "Status updated to: $new_status";
