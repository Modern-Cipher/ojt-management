<?php
include("../../0config/database.php");

$threshold = 300; // 5 minutes in seconds
$current = time();

$sql = "
    UPDATE users
    SET chat_stats = CASE
        WHEN UNIX_TIMESTAMP(COALESCE(time_out, time_in)) >= ? THEN 'online'
        ELSE 'offline'
    END
    WHERE time_in IS NOT NULL OR time_out IS NOT NULL
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Failed to prepare update_chat_stats query: " . $conn->error);
    exit();
}
$past = $current - $threshold;
$stmt->bind_param("i", $past);
if (!$stmt->execute()) {
    error_log("Failed to execute update_chat_stats query: " . $stmt->error);
}
$stmt->close();
$conn->close();

echo "Chat stats updated";
?>