<?php
session_start();
include("../../0config/database.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$user_id = $_SESSION['user_id']; // Coordinator ID
$id = $_POST['trainer_id'] ?? null;
$fname = trim($_POST['fname'] ?? '');
$lname = trim($_POST['lname'] ?? '');
$email = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$designation = trim($_POST['designation'] ?? '');
$sex = trim($_POST['sex'] ?? '');
$hte_id = $_POST['hte_id'] ?? null;

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'Trainer ID is required']);
    exit();
}

// Verify trainer belongs to coordinator
$checkTrainer = $conn->prepare("SELECT users_id FROM users WHERE users_id = ? AND coordinator_id = ? AND role = 'trainer'");
$checkTrainer->bind_param("ii", $id, $user_id);
$checkTrainer->execute();
$trainerResult = $checkTrainer->get_result();
if ($trainerResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Trainer not found or unauthorized']);
    exit();
}
$checkTrainer->close();

// Check if email exists and email is provided
if (!empty($email)) {
    $check = $conn->prepare("SELECT users_id FROM users WHERE email = ? AND users_id != ?");
    $check->bind_param("si", $email, $id);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit();
    }
    $check->close();
}

// Check if username exists
if (!empty($username)) {
    $checkUsername = $conn->prepare("SELECT users_id FROM users WHERE username = ? AND users_id != ?");
    $checkUsername->bind_param("si", $username, $id);
    $checkUsername->execute();
    $usernameResult = $checkUsername->get_result();

    if ($usernameResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        exit();
    }
    $checkUsername->close();
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

$success = true; // Default to true if no fields or HTE to update

if (count($fields) > 0) {
    $setFields = implode(", ", $fields);
    $params[] = $id;
    $types .= "i";

    $sql = "UPDATE users SET $setFields WHERE users_id = ? AND role = 'trainer'";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: Failed to prepare statement']);
        exit();
    }
    $stmt->bind_param($types, ...$params);
    $success = $stmt->execute();
    $stmt->close();

    if (!$success) {
        echo json_encode(['success' => false, 'message' => 'Failed to update trainer details']);
        exit();
    }
}

// Update HTE assignment
$hteStmt = $conn->prepare("UPDATE hte SET trainer_id = NULL WHERE trainer_id = ? AND coordinator_id = ?");
$hteStmt->bind_param("ii", $id, $user_id);
$success = $hteStmt->execute();
$hteStmt->close();

if (!$success) {
    echo json_encode(['success' => false, 'message' => 'Failed to clear HTE assignment']);
    exit();
}

if (!empty($hte_id)) {
    // Verify HTE belongs to coordinator
    $checkHte = $conn->prepare("SELECT hte_id FROM hte WHERE hte_id = ? AND coordinator_id = ?");
    $checkHte->bind_param("ii", $hte_id, $user_id);
    $checkHte->execute();
    $hteResult = $checkHte->get_result();
    if ($hteResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'HTE not found or unauthorized']);
        exit();
    }
    $checkHte->close();

    $hteStmt = $conn->prepare("UPDATE hte SET trainer_id = ? WHERE hte_id = ? AND coordinator_id = ?");
    $hteStmt->bind_param("iii", $id, $hte_id, $user_id);
    $success = $hteStmt->execute();
    $hteStmt->close();

    if (!$success) {
        echo json_encode(['success' => false, 'message' => 'Failed to assign HTE']);
        exit();
    }
}

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Trainer updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update trainer']);
}

$conn->close();
?>