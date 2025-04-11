<?php
include("../../0config/database.php");

if (!isset($_GET['trainer_id'])) {
    echo json_encode(["error" => "Trainer ID missing"]);
    exit();
}

$trainer_id = $_GET['trainer_id'];

// ✅ Fetch students where the HTE's trainer_id = given trainer_id
$sql = "SELECT u.fname, u.lname, u.course, u.year_section
        FROM users u
        JOIN hte h ON u.hte_id = h.hte_id
        WHERE h.trainer_id = ? AND u.role = 'student'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];

while ($row = $result->fetch_assoc()) {
    $students[] = [
        "full_name" => $row['fname'] . ' ' . $row['lname'],
        "course_year" => $row['course'] . '  ' . $row['year_section']
    ];
}

echo json_encode($students);
?>
