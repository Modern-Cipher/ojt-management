<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get coordinator's institute
$sql = "SELECT institute FROM users WHERE users_id = ? AND role = 'coordinator'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$coordinator = $result->fetch_assoc();
$stmt->close();

if (!$coordinator) {
    echo json_encode(["error" => "Coordinator not found"]);
    exit();
}

$institute = $coordinator['institute'];

// Define allowed courses based on institute
$allowed_courses = [];

if ($institute === 'Institute of Engineering and Applied Technology') {
    $allowed_courses = [
        'BS in Agriculture and Biosystems Engineering',
        'BS in Geodetic Engineering',
        'BS in Food Technology',
        'BS in Information Technology'
    ];
} elseif ($institute === 'Institute of Management') {
    $allowed_courses = [
        'BS in Business Administration',
        'BS in Hospitality Management'
    ];
}

if (empty($allowed_courses)) {
    echo json_encode([]);
    exit();
}

// Fetch matching students
$placeholders = implode(',', array_fill(0, count($allowed_courses), '?'));
$params = array_merge([$institute], $allowed_courses);
$types = str_repeat('s', count($params));

$sql = "SELECT 
            users_id, 
            school_id, 
            CONCAT(fname, ' ', mname, ' ', lname) AS fullname, 
            attended, 
            users_account 
        FROM users 
        WHERE role = 'student' 
        AND institute = ? 
        AND course IN ($placeholders)";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
$no = 1;
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "no" => $no++,
        "users_id" => $row['users_id'],
        "student_id" => $row['school_id'], // DITO MO ILAGAY
        "fullname" => $row['fullname'],
        "attended" => $row['attended'],
        "status" => $row['users_account']
    ];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($data);
?>
