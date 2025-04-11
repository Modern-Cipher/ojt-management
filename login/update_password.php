<?php
include("../0config/database.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST['user_id'] ?? null;
    $new_password = $_POST['new_password'] ?? null;

    if (empty($user_id) || empty($new_password)) {
        echo "error";
        exit();
    }

    // Hash the new password
    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

    // Update user password and clear temppass
    $sql = "UPDATE users SET password = ?, temppass = NULL WHERE users_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashedPassword, $user_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "invalid";
}
?>
