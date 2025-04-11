<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized access."]);
    exit();
}

if (!isset($_GET['coordinator_id'])) {
    echo json_encode(["error" => "Coordinator ID missing."]);
    exit();
}

$coordinator_id = $_GET['coordinator_id'];

// Get coordinator's course and institute
$sql = "SELECT course, institute FROM users WHERE users_id = ? AND role = 'coordinator'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $coordinator_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([]);
    exit();
}

$coordinator = $result->fetch_assoc();
$coordinator_course = $coordinator['course'];
$coordinator_institute = $coordinator['institute'];
$stmt->close();

// Now fetch students with the same course and institute
$studentSql = "SELECT users_id, fname, lname, course, year_section, image_profile, role 
               FROM users 
               WHERE course = ? AND institute = ? AND role = 'student'";

$stmt = $conn->prepare($studentSql);
$stmt->bind_param("ss", $coordinator_course, $coordinator_institute);
$stmt->execute();
$result = $stmt->get_result();

$students = [];

while ($row = $result->fetch_assoc()) {
    $row['image_profile'] = !empty($row['image_profile']) 
        ? "../../upload_profile/" . $row['image_profile'] 
        : "../../upload_profile/siplogo.png";

    $row['full_name'] = $row['fname'] . ' ' . $row['lname'];
    $row['course_year'] = $row['course'] . ' | ' . $row['year_section'];

    $students[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($students);
