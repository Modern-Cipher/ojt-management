<?php
session_start();
include("../../0config/database.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$filename_id = $_POST['filename_id'] ?? null;
$file = $_FILES['pdfFile'] ?? null;

if (!$filename_id || !$file) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit();
}

// 🔐 Fetch user full name
$sql = "SELECT fname, lname FROM users WHERE users_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($fname, $lname);
$stmt->fetch();
$stmt->close();

// 🔐 Fetch filename from filename table
$sql = "SELECT filename FROM filename WHERE filename_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $filename_id);
$stmt->execute();
$stmt->bind_result($filename);
$stmt->fetch();
$stmt->close();

if (!$filename) {
    echo json_encode(['status' => 'error', 'message' => 'Filename not found']);
    exit();
}

// 📂 Check if existing upload → Delete old if exists
$sql = "SELECT file_name, filepath FROM uploads WHERE filename_id = ? AND uploadedby_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $filename_id, $user_id);
$stmt->execute();
$stmt->bind_result($oldFileName, $oldFilePath);
$stmt->fetch();
$stmt->close();

if ($oldFileName && file_exists("../../upload_journal/" . $oldFileName)) {
    unlink("../../upload_journal/" . $oldFileName); // Delete file
    // Delete record
    $sql = "DELETE FROM uploads WHERE filename_id = ? AND uploadedby_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $filename_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

// 📄 Rename file
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$random = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 6);
$newFileName = "{$fname}_{$lname}_{$filename}_{$random}.{$ext}";
$destination = "../../upload_journal/" . $newFileName;

// 📥 Move file
if (move_uploaded_file($file['tmp_name'], $destination)) {
    // Insert to DB
    $sql = "INSERT INTO uploads (filename_id, file_name, filepath, uploadedby_id, upload_status, submitted_on, updated_on) 
            VALUES (?, ?, ?, ?, 'processing', NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $filename_id, $newFileName, $destination, $user_id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['status' => 'success', 'message' => 'File uploaded']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to move file']);
}

$conn->close();
?>
