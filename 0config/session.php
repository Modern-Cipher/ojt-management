<?php
// ✅ Prevent duplicate session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("database.php");

// ✅ Redirect to login if session is missing
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    session_unset();
    session_destroy();
    header("Location: ../login/login.php");
    exit();
}

$session_id = session_id();
$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];

// ✅ Fetch user details, including `institute`
$query = "SELECT institute FROM users WHERE users_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($institute);
$stmt->fetch();
$stmt->close();

// ✅ Store institute in session
$_SESSION["institute"] = $institute;

// ✅ Check if session still exists in the database
$query = "SELECT * FROM sessions WHERE sessions_id = ? AND users_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $session_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // ✅ If session is missing in DB, log out the user
    session_unset();
    session_destroy();
    header("Location: ../login/login.php");
    exit();
}

// ✅ Update last activity timestamp (keep session active)
$conn->query("UPDATE sessions SET last_activity = NOW() WHERE sessions_id = '$session_id'");
?>
