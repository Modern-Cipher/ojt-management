<?php
session_start();
include("../../0config/database.php"); // Your DB config file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../0config/logout.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get form data from POST
$title = $_POST['title'];
$content = $_POST['content'];
$role = $_POST['role'];
$status = $_POST['status'];
$notified = ($status === 'posted') ? 1 : 0;

// Step 1: Fetch the admin's institute
$institute = "";
$instituteQuery = "SELECT institute FROM users WHERE users_id = ?";
$instStmt = $conn->prepare($instituteQuery);
$instStmt->bind_param("i", $user_id);
$instStmt->execute();
$instResult = $instStmt->get_result();
if ($instRow = $instResult->fetch_assoc()) {
    $institute = $instRow['institute'];
}
$instStmt->close();

// Step 2: Insert the announcement
$insertSql = "INSERT INTO announcements (users_id, title, content, role, announce_status, notified) 
              VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insertSql);
$stmt->bind_param("issssi", $user_id, $title, $content, $role, $status, $notified);
$stmt->execute();
$announcement_id = $stmt->insert_id;
$stmt->close();

// Step 3: Send notifications if posted
if ($status === 'posted') {
    // Fetch users to notify (same institute + matching role)
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

    // Insert notifications for each matched user
    $notifSql = "INSERT INTO notifications (receiver_id, sender_id, type, source_id, message, is_read, created_at)
                 VALUES (?, ?, 'announcements', ?, ?, 0, NOW())";
    $notifStmt = $conn->prepare($notifSql);

    while ($row = $result->fetch_assoc()) {
        $receiver_id = $row['users_id'];
        $message = "New announcement: " . $title;

        $notifStmt->bind_param("iiis", $receiver_id, $user_id, $announcement_id, $message);
        $notifStmt->execute();
    }

    $notifStmt->close();
    $userStmt->close();
}

// Done – back to announcement page
$conn->close();
header("Location: announcement.php?success=1");
exit();
