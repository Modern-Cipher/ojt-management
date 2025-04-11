<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo "unauthorized";
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$trainerId = $data['trainer_id'] ?? 0;
$status = $data['status'] ?? '';

if ($trainerId > 0 && in_array($status, ['enabled', 'disabled'])) {
    $stmt = $conn->prepare("UPDATE users SET users_account = ? WHERE users_id = ?");
    $stmt->bind_param("si", $status, $trainerId);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
} else {
    echo "invalid";
}

$conn->close();
?>
