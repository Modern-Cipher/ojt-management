<?php
session_start();
include("../../0config/database.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $announcement_id = $_POST['announcement_id'];
    $comment = trim($_POST['comment']);

    if (!empty($comment)) {
        // Insert comment
        $stmt = $conn->prepare("INSERT INTO comments (announcements_id, users_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $announcement_id, $user_id, $comment);
        $stmt->execute();
        $stmt->close();

        // Optional: Insert into notifications table here if needed
    }

    $conn->close();
}
?>
