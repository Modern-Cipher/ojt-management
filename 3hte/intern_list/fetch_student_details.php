<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo json_encode(["error" => "Unauthorized or missing student ID"]);
    exit();
}

$student_id = intval($_GET['id']);

// Fetch student info
$stmt = $conn->prepare("SELECT users_id, fname, lname, course, year_section, address, email, phone, image_profile, institute FROM users WHERE users_id = ? AND role = 'student'");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Student not found"]);
    exit();
}

$data = $result->fetch_assoc();

// Set profile image fallback
$data['image_profile'] = (!empty($data['image_profile']) && file_exists("../../upload_profile/" . $data['image_profile']))
    ? "../../upload_profile/" . $data['image_profile']
    : "../../upload_profile/siplogo.png";

$institute = $data['institute'] ?? '';
$course = $data['course'] ?? '';

// Fetch dean (admin role, same institute)
$deanStmt = $conn->prepare("SELECT fname, lname FROM users WHERE role = 'admin' AND institute = ? LIMIT 1");
$deanStmt->bind_param("s", $institute);
$deanStmt->execute();
$deanResult = $deanStmt->get_result();
$dean = $deanResult->fetch_assoc();

$data['dean'] = $dean ? $dean['fname'] . ' ' . $dean['lname'] : '';

// Fetch coordinator (same course + institute, role coordinator)
$coordStmt = $conn->prepare("SELECT fname, lname FROM users WHERE role = 'coordinator' AND institute = ? AND course = ? LIMIT 1");
$coordStmt->bind_param("ss", $institute, $course);
$coordStmt->execute();
$coordResult = $coordStmt->get_result();
$coord = $coordResult->fetch_assoc();

$data['coordinator'] = $coord ? $coord['fname'] . ' ' . $coord['lname'] : '';

$stmt->close();
$deanStmt->close();
$coordStmt->close();
$conn->close();

// Clean null values
foreach ($data as $key => $value) {
    if (is_null($value)) {
        $data[$key] = '';
    }
}

echo json_encode($data);
