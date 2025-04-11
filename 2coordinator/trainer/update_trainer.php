<?php
session_start();
include("../../0config/database.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$id = $_POST['trainer_id'] ?? null;
$fname = trim($_POST['fname'] ?? '');
$lname = trim($_POST['lname'] ?? '');
$email = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$designation = trim($_POST['designation'] ?? '');
$sex = trim($_POST['sex'] ?? '');
$hte_id = $_POST['hte_id'] ?? null;

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'Trainer ID is required.']);
    exit();
}

// Check if email exists and email is provided
if (!empty($email)) {
    $check = $conn->prepare("SELECT users_id FROM users WHERE email = ? AND users_id != ?");
    $check->bind_param("si", $email, $id);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists.']);
        exit();
    }
    $check->close();
}

// Dynamic Query Builder
$fields = [];
$params = [];
$types = "";

if (!empty($fname)) {
    $fields[] = "fname = ?";
    $params[] = $fname;
    $types .= "s";
}
if (!empty($lname)) {
    $fields[] = "lname = ?";
    $params[] = $lname;
    $types .= "s";
}
if (!empty($email)) {
    $fields[] = "email = ?";
    $params[] = $email;
    $types .= "s";
}
if (!empty($username)) {
    $fields[] = "username = ?";
    $params[] = $username;
    $types .= "s";
}
if (!empty($designation)) {
    $fields[] = "designation = ?";
    $params[] = $designation;
    $types .= "s";
}
if (!empty($sex)) {
    $fields[] = "sex = ?";
    $params[] = $sex;
    $types .= "s";
}

if (count($fields) > 0) {
    $setFields = implode(", ", $fields);
    $params[] = $id;
    $types .= "i";

    $sql = "UPDATE users SET $setFields WHERE users_id = ? AND role = 'trainer'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $success = $stmt->execute();
    $stmt->close();
} else {
    $success = true; // No data to update but proceed to HTE
}

// Update HTE assignment
$conn->query("UPDATE hte SET trainer_id = NULL WHERE trainer_id = $id");

if (!empty($hte_id)) {
    $conn->query("UPDATE hte SET trainer_id = $id WHERE hte_id = $hte_id");
}

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Trainer updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update trainer.']);
}

$conn->close();
?>
