<?php
session_start();
include("../../0config/database.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../0config/logout.php");
    exit();
}

// Validate and sanitize POST inputs
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $announcement_id = $_POST['announcement_id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $role = $_POST['role'] ?? '';

    if ($announcement_id && $title && $content && $role) {
        // Update announcement
        $stmt = $conn->prepare("UPDATE announcements SET title = ?, content = ?, role = ? WHERE announcements_id = ?");
        $stmt->bind_param("sssi", $title, $content, $role, $announcement_id);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: announcement.php?updated=1");
            exit();
        } else {
            echo "❌ Error updating announcement: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "❌ Invalid input. All fields are required.";
    }
} else {
    echo "❌ Invalid request method.";
}

$conn->close();
?>
