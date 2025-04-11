<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get current coordinator institute & course
$stmt = $conn->prepare("SELECT institute, course FROM users WHERE users_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$current = $result->fetch_assoc();
$stmt->close();

$institute = $current['institute'];
$course = $current['course'];

// Get trainers with image
$sql = "
SELECT u.users_id, u.fname, u.lname, u.designation, u.role, h.hte_name, u.image_profile
FROM users u 
LEFT JOIN hte h ON h.trainer_id = u.users_id 
WHERE u.role = 'trainer' 
AND u.institute = ? 
AND u.course = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $institute, $course);
$stmt->execute();
$result = $stmt->get_result();

$trainers = [];
while ($row = $result->fetch_assoc()) {
    // Check image
    $image = "../../upload_profile/" . $row['image_profile'];
    $defaultImage = "../../upload_profile/siplogo.png";
    $row['image_profile'] = (!empty($row['image_profile']) && file_exists(__DIR__ . "/" . $image)) ? $image : $defaultImage;

    // Safe capitalize
    $row['fname'] = ucwords(strtolower($row['fname'] ?? ''));
    $row['lname'] = ucwords(strtolower($row['lname'] ?? ''));
    $row['hte_name'] = ucwords(strtolower($row['hte_name'] ?? ''));
    $row['designation'] = ucwords(strtolower($row['designation'] ?? ''));

    $trainers[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($trainers);
?>
