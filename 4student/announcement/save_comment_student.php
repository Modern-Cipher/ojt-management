<?php
session_start();
include("../../0config/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['announcement_id'], $_POST['comments'])) {
    $announcement_id = (int)$_POST['announcement_id'];
    $comment = trim($_POST['comments']);
    $user_id = $_SESSION['user_id'];

    if (!empty($comment)) {
        // ✅ Insert comment
        $stmt = $conn->prepare("INSERT INTO comments (announcements_id, users_id, comments, created_at, notified) VALUES (?, ?, ?, NOW(), 1)");
        $stmt->bind_param("iis", $announcement_id, $user_id, $comment);
        $stmt->execute();
        $stmt->close();

        $comment_id = $conn->insert_id;

        // ✅ Get user's institute
        $instQuery = $conn->prepare("SELECT institute FROM users WHERE users_id = ?");
        $instQuery->bind_param("i", $user_id);
        $instQuery->execute();
        $instResult = $instQuery->get_result();
        $userInstitute = $instResult->fetch_assoc()['institute'] ?? null;
        $instQuery->close();

        // ✅ Get announcement's target role
        $receiverQuery = $conn->prepare("SELECT role FROM announcements WHERE announcements_id = ?");
        $receiverQuery->bind_param("i", $announcement_id);
        $receiverQuery->execute();
        $result = $receiverQuery->get_result();

        if ($row = $result->fetch_assoc()) {
            $targetRole = $row['role'];
            $message = "Commented on an announcement.";

            // ✅ Notify users in same institute based on role or all
            if ($targetRole === 'all') {
                $userListQuery = $conn->prepare("SELECT users_id FROM users WHERE institute = ? AND users_id != ?");
                $userListQuery->bind_param("si", $userInstitute, $user_id);
            } else {
                $userListQuery = $conn->prepare("SELECT users_id FROM users WHERE role = ? AND institute = ? AND users_id != ?");
                $userListQuery->bind_param("ssi", $targetRole, $userInstitute, $user_id);
            }

            $userListQuery->execute();
            $userResult = $userListQuery->get_result();

            $notif = $conn->prepare("INSERT INTO notifications (sender_id, receiver_id, type, source_id, message, is_read, created_at)
                                     VALUES (?, ?, 'comments', ?, ?, 0, NOW())");

            while ($receiver = $userResult->fetch_assoc()) {
                $receiver_id = $receiver['users_id'];
                $notif->bind_param("iiis", $user_id, $receiver_id, $comment_id, $message);
                $notif->execute();
            }

            $notif->close();
            $userListQuery->close();
        }

        $receiverQuery->close();

        // ✅ Redirect back
        echo "<script>window.location.href = '../../4student/studentdashboard.php';</script>";
    } else {
        echo "<script>alert('Comment is required.'); window.location.href = '../../4student/studentdashboard.php';</script>";
    }
} else {
    echo "<p style='color: red;'>Invalid request.</p>";
}
