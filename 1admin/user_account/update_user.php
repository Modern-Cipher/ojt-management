<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../../0config/database.php");
header('Content-Type: application/json'); // ✅ Ensure the response is JSON

// Check if request is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_POST["user_id"];
    $updates = [];
    $params = [];

    if (isset($_POST["school_id"]) && $_POST["school_id"] !== "") {
        $updates[] = "school_id = ?";
        $params[] = $_POST["school_id"];
    }
    if (!empty($_POST["fname"])) {
        $updates[] = "fname = ?";
        $params[] = $_POST["fname"];
    }
    if (!empty($_POST["lname"])) {
        $updates[] = "lname = ?";
        $params[] = $_POST["lname"];
    }
    if (!empty($_POST["sex"])) {
        $updates[] = "sex = ?";
        $params[] = $_POST["sex"];
    }
    if (!empty($_POST["email"])) {
        $updates[] = "email = ?";
        $params[] = $_POST["email"];
    }
    if (!empty($_POST["username"])) {
        $updates[] = "username = ?";
        $params[] = $_POST["username"];
    }

    if (empty($updates)) {
        echo json_encode(["success" => false, "message" => "No changes detected."]);
        exit();
    }

    // Execute the query
    $params[] = $userId;
    $query = "UPDATE users SET " . implode(", ", $updates) . " WHERE users_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "SQL Error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param(str_repeat("s", count($params) - 1) . "i", ...$params);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User updated successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error updating user: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
