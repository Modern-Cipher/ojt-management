<?php
session_start();
include '../../0config/database.php';

// Dummy checker user_id (Palitan mo kung sino yung naka-login na checker)
$checker_id = $_SESSION['user_id'] ?? 2; // Example lang, gamitin mo yung real session mo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filename_id = $_POST['filename_id'] ?? null;
    $comment = $_POST['comment'] ?? null;

    if ($filename_id && $comment) {
        $query = "INSERT INTO file_comments (filename_id, commenter_id, comment) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $filename_id, $checker_id, $comment);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<p style='color:green;'>✅ Reply Comment Posted!</p>";
        } else {
            echo "<p style='color:red;'>❌ Failed to post comment.</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Missing data.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reply as Checker</title>
</head>
<body>
    <h3>Reply Comment as Checker</h3>
    <form method="POST">
        <label>Filename ID:</label><br>
        <input type="number" name="filename_id" required><br><br>

        <label>Comment:</label><br>
        <textarea name="comment" rows="3" cols="30" required></textarea><br><br>

        <button type="submit">Post Reply</button>
    </form>
</body>
</html>
