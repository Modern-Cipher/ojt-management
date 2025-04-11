<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($user_id === null || $status === null) {
        echo json_encode(["success" => false, "message" => "Invalid input"]);
        exit();
    }

    $sql = "UPDATE users SET users_account = ? WHERE users_id = ? AND role = 'student'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Account status updated"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update"]);
    }

    $stmt->close();
    $conn->close();
}
?>
