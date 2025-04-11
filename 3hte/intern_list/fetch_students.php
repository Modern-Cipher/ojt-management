<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get the trainer's hte_id from hte table
$sql = "
    SELECT h.hte_id
    FROM users u
    LEFT JOIN hte h ON h.trainer_id = u.users_id
    WHERE u.users_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

$hte_id = $data['hte_id'] ?? null;

if (!$hte_id) {
    echo json_encode(["error" => "HTE ID not found"]);
    exit();
}

// Fetch students under same hte_id with ojt_stats = 'deployed'
$sql = "
    SELECT users_id, fname, lname, image_profile, course, year_section, role
    FROM users
    WHERE hte_id = ?
      AND role = 'student'
      AND ojt_stats = 'deployed'
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hte_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $image_path = "../../upload_profile/" . $row['image_profile'];
    $row['image_profile'] = (!empty($row['image_profile']) && file_exists("../../upload_profile/" . $row['image_profile']))
        ? $image_path
        : "../../upload_profile/siplogo.png";

    $students[] = $row;
}

echo json_encode($students);
?>
