<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get current coordinator's institute & course
$stmt = $conn->prepare("SELECT institute, course FROM users WHERE users_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$current = $result->fetch_assoc();
$stmt->close();

$institute = $current['institute'];
$course = $current['course'];

// Fetch students with same institute & course
$sql = "
SELECT u.users_id, u.fname, u.lname, u.course, u.role, u.image_profile, u.hte_id, h.hte_name
FROM users u 
LEFT JOIN hte h ON h.hte_id = u.hte_id 
WHERE u.role = 'student' 
AND u.institute = ? 
AND u.course = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $institute, $course);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    // Check image
    $image = "../../upload_profile/" . $row['image_profile'];
    $defaultImage = "../../upload_profile/siplogo.png";
    $row['image_profile'] = (!empty($row['image_profile']) && file_exists(__DIR__ . "/" . $image)) ? $image : $defaultImage;

    // Capitalize
    $row['fname'] = ucwords(strtolower($row['fname']));
    $row['lname'] = ucwords(strtolower($row['lname']));
    $row['course'] = ucwords(strtolower($row['course']));
    $row['hte_name'] = (!empty($row['hte_name'])) ? ucwords(strtolower($row['hte_name'])) : ''; // Blank if no hte_id

    $students[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($students);
?>
