<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $attended = $_POST['attended'] ?? null;

    if ($user_id === null || $attended === null) {
        echo json_encode(["error" => "Invalid input"]);
        exit();
    }

    $attended = ($attended === 'true') ? 'yes' : 'no';

    $sql = "UPDATE users SET attended = ? WHERE users_id = ? AND role = 'student'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $attended, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Attendance updated"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update"]);
    }

    $stmt->close();
    $conn->close();
}
?>
