<?php
include("../../0config/database.php");

if (!isset($_GET['coordinator_id'])) {
    echo json_encode(["error" => "Missing coordinator_id"]);
    exit();
}

$coordinator_id = $_GET['coordinator_id'];

$sql = "SELECT 
            users_id, fname, lname, designation, image_profile, role 
        FROM users 
        WHERE coordinator_id = ? AND role = 'trainer'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $coordinator_id);
$stmt->execute();
$result = $stmt->get_result();

$trainers = [];

while ($row = $result->fetch_assoc()) {
    $row['full_name'] = $row['fname'] . ' ' . $row['lname'];
    $row['image_profile'] = !empty($row['image_profile']) 
        ? "../../upload_profile/" . $row['image_profile']
        : "../../upload_profile/siplogo.png";

    $trainers[] = $row;
}

echo json_encode($trainers);
$stmt->close();
$conn->close();
?>
