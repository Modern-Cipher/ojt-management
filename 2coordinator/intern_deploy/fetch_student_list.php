<?php
session_start();
include("../../0config/database.php");
header('Content-Type: application/json');

// Check Session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$coordinator_id = $_SESSION['user_id'];

// Get Coordinator Institute & Course
$sql = "SELECT institute, course FROM users WHERE users_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $coordinator_id);
$stmt->execute();
$result = $stmt->get_result();
$coordinator = $result->fetch_assoc();
$stmt->close();

if (!$coordinator) {
    echo json_encode(['error' => 'Coordinator not found']);
    exit();
}

// Fetch Students with HTE & Trainer (Activated Only)
$sql_students = "
    SELECT 
        u.users_id, 
        u.school_id, 
        CONCAT(u.fname, ' ', u.lname) AS student_fullname,
        u.ojt_stats,
        h.hte_name,
        CONCAT(t.fname, ' ', t.lname) AS trainer_fullname
    FROM users u
    LEFT JOIN hte h ON u.hte_id = h.hte_id
    LEFT JOIN users t ON h.trainer_id = t.users_id AND t.role = 'trainer'
    WHERE u.role = 'student' 
    AND u.institute = ? 
    AND u.course = ?
    AND u.activate = 1
";


$stmt = $conn->prepare($sql_students);
$stmt->bind_param("ss", $coordinator['institute'], $coordinator['course']);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
$count = 1;
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'no' => $count++,
        'users_id' => $row['users_id'],
        'school_id' => $row['school_id'] ?? '-',
        'fullname' => $row['student_fullname'] ?? '-',
        'hte_name' => $row['hte_name'] ?? '-',
        'trainer_fullname' => $row['trainer_fullname'] ?? '-',
        'ojt_stats' => $row['ojt_stats'] ?? '-'
    ];
}
$stmt->close();
$conn->close();

echo json_encode($data);
?>
