<?php
session_start();
include("../../0config/database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user_id"]) && isset($_POST["new_status"])) {
    $user_id = $_POST["user_id"];
    $new_status = $_POST["new_status"];

    // Update user status in the database
    $sql = "UPDATE users SET users_account = ? WHERE users_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
}
