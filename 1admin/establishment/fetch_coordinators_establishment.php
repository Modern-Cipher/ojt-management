<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$admin_id = $_SESSION['user_id'];

// Step 1: Get the admin's institute
$instituteQuery = "SELECT institute FROM users WHERE users_id = ?";
$stmt = $conn->prepare($instituteQuery);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

if (!$userData || empty($userData['institute'])) {
    echo json_encode(["error" => "Institute not found"]);
    exit();
}

$institute = $userData['institute'];

// Step 2: Fetch coordinators from the same institute
$sql = "SELECT users_id, fname, lname, designation, role, image_profile 
        FROM users 
        WHERE role = 'coordinator' AND institute = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $institute);
$stmt->execute();
$result = $stmt->get_result();

$coordinators = [];
while ($row = $result->fetch_assoc()) {
    $row['full_name'] = $row['fname'] . ' ' . $row['lname'];
    $row['image_profile'] = !empty($row['image_profile']) 
        ? "../../upload_profile/" . $row['image_profile'] 
        : "../../upload_profile/siplogo.png";

    $coordinators[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($coordinators);
?>
