<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'], $_GET['comment_id'])) {
    exit("Invalid access");
}

$commentId = (int) $_GET['comment_id'];
$currentUserId = $_SESSION['user_id'];

// ✅ First delete the comment (if it belongs to current user)
$commentDeleteStmt = $conn->prepare("DELETE FROM comments WHERE comments_id = ? AND users_id = ?");
$commentDeleteStmt->bind_param("ii", $commentId, $currentUserId);
$commentDeleteStmt->execute();
$commentDeleteStmt->close();

// ✅ Then delete related notifications linked to this comment
$notifDeleteStmt = $conn->prepare("DELETE FROM notifications WHERE type = 'comments' AND source_id = ?");
$notifDeleteStmt->bind_param("i", $commentId);
$notifDeleteStmt->execute();
$notifDeleteStmt->close();

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>
