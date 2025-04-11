<?php
// ✅ Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("database.php");

// ✅ Basic session check — this powers all your logic
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    session_unset();
    session_destroy();
    header("Location: ../login/login.php");
    exit();
}

// ✅ Grab needed session values
$session_id = session_id();
$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"] ?? '';
$role = $_SESSION["role"] ?? '';

// ✅ Fetch institute again to ensure sync with DB (optional but safe)
$query = "SELECT institute FROM users WHERE users_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($institute);
$stmt->fetch();
$stmt->close();
$_SESSION["institute"] = $institute;

// ✅ Check if session is in DB (but do NOT logout if missing)
$query = "SELECT * FROM sessions WHERE sessions_id = ? AND users_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $session_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// ⚠ Don't log out, just log internally if missing
if ($result === false || $result->num_rows == 0) {
    error_log("⚠ No session record in DB for user_id {$user_id}, but PHP session is valid.");
} else {
    // ✅ Update session timestamp to keep it active
    $conn->query("UPDATE sessions SET last_activity = NOW() WHERE sessions_id = '$session_id'");
}
?>
