<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../0config/logout.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$announcement_id = $_POST['announcement_id'];
$title = $_POST['title'];
$content = $_POST['content'];
$role = $_POST['role'];

// Update only if the announcement belongs to the user
$sql = "UPDATE announcements 
        SET title = ?, content = ?, role = ? 
        WHERE announcements_id = ? AND users_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssii", $title, $content, $role, $announcement_id, $user_id);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: announcement.php?updated=1");
exit();
