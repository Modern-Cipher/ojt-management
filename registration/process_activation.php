<?php
session_start();
include '../0config/database.php';

header('Content-Type: application/json');

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['email']) || !isset($_POST['guid'])) {
        $response["message"] = "Missing required data.";
        echo json_encode($response);
        exit();
    }

    $email = trim($_POST['email']);
    $guid = trim($_POST['guid']);

    if (empty($email) || empty($guid)) {
        $response["message"] = "Activation code cannot be empty.";
        echo json_encode($response);
        exit();
    }

    // Check if the GUID matches the one stored for the email
    $stmt = $conn->prepare("SELECT users_id FROM users WHERE email = ? AND guid = ? AND activate = 0");
    $stmt->bind_param("ss", $email, $guid);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Update account activation status
        $stmt = $conn->prepare("UPDATE users SET activate = 1 WHERE email = ? AND guid = ?");
        $stmt->bind_param("ss", $email, $guid);
        if ($stmt->execute()) {
            $response["success"] = true;
            $response["redirect"] = "activation_success.php";
        } else {
            $response["message"] = "Error activating account.";
        }
    } else {
        $response["message"] = "Invalid activation code.";
    }

    $stmt->close();
}

echo json_encode($response);
exit();
