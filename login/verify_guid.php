<?php
include("../0config/database.php");

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST["user_id"] ?? null;
    $entered_guid = trim($_POST["guid"] ?? "");

    if (empty($user_id) || empty($entered_guid)) {
        echo json_encode(["status" => "error", "message" => "Missing fields."]);
        exit;
    }

    // Fetch actual guid from DB
    $stmt = $conn->prepare("SELECT guid FROM users WHERE users_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($actual_guid);
    $stmt->fetch();
    $stmt->close();

    // Compare
    if ($entered_guid === $actual_guid) {
        // ✅ Update activation status
        $stmt = $conn->prepare("UPDATE users SET activate = 1 WHERE users_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(["status" => "success", "message" => "Account activated."]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid GUID."]);
        exit;
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
    exit;
}
?>
