<?php
session_start();
include("../../0config/database.php");

// Force JSON output
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set("display_errors", 1);

$response = ["status" => "error", "message" => "Something went wrong"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_image"])) {
    $user_id = $_SESSION['user_id'] ?? 0;

    if ($user_id == 0) {
        $response["message"] = "User not logged in";
        echo json_encode($response);
        exit();
    }

    // ✅ Define the correct column and upload directory
    $columnName = "image_profile";
    $uploadDir = "../../upload_profile/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $allowedTypes = ["image/jpeg", "image/png"];
    $file = $_FILES["profile_image"];

    if (!in_array($file["type"], $allowedTypes)) {
        $response["message"] = "Invalid file type. Only JPG and PNG are allowed.";
        echo json_encode($response);
        exit();
    }

    // ✅ Fetch the existing profile image from the database
    $stmt = $conn->prepare("SELECT $columnName FROM users WHERE users_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($oldFileName);
    $stmt->fetch();
    $stmt->close();

    // ✅ Delete the old file if it exists and is not the default image
    if (!empty($oldFileName) && file_exists($uploadDir . $oldFileName) && $oldFileName !== "siplogo.png") {
        unlink($uploadDir . $oldFileName);
    }

    // ✅ Generate a 6-character random filename
    $randomStr = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 6);
    $fileExtension = pathinfo($file["name"], PATHINFO_EXTENSION);
    $newFilename = $randomStr . "." . $fileExtension;

    // ✅ Full file path
    $filePath = $uploadDir . $newFilename;

    // ✅ Move uploaded file
    if (move_uploaded_file($file["tmp_name"], $filePath)) {
        $query = "UPDATE users SET $columnName = ? WHERE users_id = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            file_put_contents("error_log.txt", "DB Error: " . $conn->error . "\n", FILE_APPEND);
            $response["message"] = "Database preparation error.";
            echo json_encode($response);
            exit();
        }

        $stmt->bind_param("si", $newFilename, $user_id); // ✅ Save only the filename
        if ($stmt->execute()) {
            $response = ["status" => "success", "filePath" => "../../upload_profile/" . $newFilename];
        } else {
            file_put_contents("error_log.txt", "DB Execute Error: " . $stmt->error . "\n", FILE_APPEND);
            $response["message"] = "Database update failed.";
        }
        $stmt->close();
    } else {
        file_put_contents("error_log.txt", "Upload Error: Could not move file.\n", FILE_APPEND);
        $response["message"] = "Failed to upload image.";
    }
} else {
    $response["message"] = "No file uploaded.";
}

echo json_encode($response);
exit();
?>
