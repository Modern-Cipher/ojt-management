<?php
include("../../0config/database.php");

header('Content-Type: application/json');

$studentId = $_GET['student_id'] ?? null;

if (!$studentId) {
    echo json_encode(['error' => 'Missing student ID']);
    exit;
}

// Fetch student with hte and trainer info
$sql = "
SELECT 
    u.image_profile,
    u.fname, u.lname, u.school_id, u.username, u.course, u.address,
    u.email, u.phone, u.ojt_stats,
    h.hte_name, h.hte_address,
    t.fname AS trainer_fname, t.lname AS trainer_lname, 
    t.designation, t.email AS trainer_email, t.phone AS trainer_phone
FROM users u
LEFT JOIN hte h ON u.hte_id = h.hte_id
LEFT JOIN users t ON h.trainer_id = t.users_id AND t.role = 'trainer'
WHERE u.users_id = ? AND u.role = 'student'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo json_encode(['error' => 'Student not found']);
    exit;
}

// Defaults if missing
$data['hte_name'] = $data['hte_name'] ?? '-';
$data['hte_address'] = $data['hte_address'] ?? '-';
$data['trainer_fname'] = $data['trainer_fname'] ?? '-';
$data['trainer_lname'] = $data['trainer_lname'] ?? '-';
$data['designation'] = $data['designation'] ?? '-';
$data['trainer_email'] = $data['trainer_email'] ?? '-';
$data['trainer_phone'] = $data['trainer_phone'] ?? '-';

echo json_encode($data);
?>
