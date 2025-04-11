<?php
session_start();
include("../0config/database.php");

if (isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
    $session_id = session_id();

    // Delete session record
    $conn->query("DELETE FROM sessions WHERE sessions_id = '$session_id'");

    // Update user chat status and time_out
    $update = $conn->prepare("UPDATE users SET chat_stats = 'offline', time_out = NOW() WHERE users_id = ?");
    $update->bind_param("i", $user_id);
    $update->execute();
    $update->close();
}

// Destroy PHP session
session_unset();
session_destroy();

// Redirect to login
header("Location: ../login/login.php");
exit();
?>
