<?php
session_start();
include("../../0config/database.php");

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    $query = "SELECT sex FROM users WHERE users_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($sex);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    echo json_encode(["sex" => $sex]);
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
