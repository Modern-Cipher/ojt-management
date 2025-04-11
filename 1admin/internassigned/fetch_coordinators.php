<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch current user's institute
$stmt = $conn->prepare("SELECT institute FROM users WHERE users_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$institute = $row['institute'] ?? '';
$stmt->close();

if (empty($institute)) {
    echo json_encode(['error' => 'Institute not found']);
    exit;
}

// Fetch coordinators from same institute
$stmt = $conn->prepare("SELECT users_id, fname, lname, designation, role, image_profile FROM users WHERE role = 'coordinator' AND institute = ?");
$stmt->bind_param("s", $institute);
$stmt->execute();
$result = $stmt->get_result();

$coordinators = [];

while ($row = $result->fetch_assoc()) {
    $row['full_name'] = $row['fname'] . ' ' . $row['lname'];
    $row['image_profile'] = !empty($row['image_profile']) 
        ? "../../upload_profile/" . $row['image_profile'] 
        : "../../upload_profile/siplogo.png";
    $coordinators[] = $row;
}

echo json_encode($coordinators);
