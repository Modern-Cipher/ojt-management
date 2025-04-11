<?php
session_start();
include("../0config/database.php");

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Fetch the user record
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        echo json_encode(["status" => "error", "message" => "User not found!"]);
        exit;
    }

    $user = $result->fetch_assoc();
    $role = $user["role"];

    // 🔒 Account is disabled
    if ($user["users_account"] === "disabled") {
        $msg = ($role === "student") 
            ? "You need to call your coordinator." 
            : "You need to call your administrator.";

        echo json_encode(["status" => "error", "message" => $msg]);
        exit;
    }

    // 🔒 Wrong password
    if (!password_verify($password, $user["password"])) {
        echo json_encode(["status" => "error", "message" => "Incorrect password!"]);
        exit;
    }

    // 🔒 Student is not activated yet
    if ($role === "student" && $user["activate"] == 0) {
        echo json_encode([
            "status" => "activate",
            "message" => "You are not activated.",
            "user_id" => $user["users_id"],
            "guid" => $user["guid"]
        ]);
        exit;
    }

    // ✅ Update chat status and login time
    $conn->query("UPDATE users SET chat_stats = 'online', time_in = NOW() WHERE users_id = {$user['users_id']}");

    // ✅ Set PHP session variables
    $_SESSION["user_id"] = $user["users_id"];
    $_SESSION["role"] = $role;
    $_SESSION["institute"] = $user["institute"];
    $_SESSION["username"] = $user["username"];

    // ✅ Insert session record into database
    $session_id = session_id();
    $session_data = json_encode([
        "user_id" => $user["users_id"],
        "role" => $role,
        "institute" => $user["institute"],
        "username" => $user["username"]
    ]);

    $stmt = $conn->prepare("INSERT INTO sessions (sessions_id, users_id, session_data) VALUES (?, ?, ?) 
                            ON DUPLICATE KEY UPDATE session_data = VALUES(session_data), last_activity = NOW()");
    $stmt->bind_param("sis", $session_id, $user["users_id"], $session_data);
    $stmt->execute();

    // ✅ Define dashboard redirects
    $redirectMap = [
        "admin" => "../1admin/admindashboard.php",
        "coordinator" => "../2coordinator/coordinatordashboard.php",
        "trainer" => "../3hte/htedashboard.php",
        "student" => "../4student/studentdashboard.php"
    ];

    $redirect = $redirectMap[$role] ?? "../login/login.php";

    // 🔐 If using default password, show modal (but still redirect to dashboard)
    if (!empty($user["temppass"]) && in_array($role, ["admin", "coordinator", "trainer"])) {
        echo json_encode([
            "status" => "temppass",
            "user_id" => $user["users_id"],
            "role" => $role,
            "redirect" => $redirect
        ]);
        exit;
    }

    // ✅ Final successful login
    echo json_encode([
        "status" => "success",
        "role" => $role,
        "redirect" => $redirect
    ]);
    exit;
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit;
}
?>
