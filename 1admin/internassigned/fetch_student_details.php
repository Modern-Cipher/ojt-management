<?php
include("../../0config/database.php");

if (!isset($_GET['student_id'])) {
    echo json_encode(["error" => "Student ID missing"]);
    exit();
}

$student_id = $_GET['student_id'];

$sql = "SELECT 
            users.image_profile, users.fname, users.lname, users.username, users.sex, users.address, 
            users.email, users.phone, users.institute, users.course, users.year_section, 
            users.activate, users.created_on, 
            hte.hte_status AS hte_status, hte.hte_name, hte.hte_address,
            trainer.fname AS trainer_fname, trainer.lname AS trainer_lname
        FROM users
        LEFT JOIN hte ON users.hte_id = hte.hte_id
        LEFT JOIN users AS trainer ON hte.trainer_id = trainer.users_id
        WHERE users.users_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data) {
    // Safe defaults for all fields
    $data = array_map(function ($value) {
        return $value ?? "";
    }, $data);

    $data['image_profile'] = !empty($data['image_profile']) 
        ? "../../upload_profile/" . $data['image_profile']
        : "../../upload_profile/siplogo.png";

    $data['full_name'] = trim($data['fname'] . " " . $data['lname']);
    $data['account_status'] = $data['activate'] == 1 ? "Activated" : "Inactive";
    $data['created_on'] = !empty($data['created_on']) 
        ? date("F d, Y - h:i A", strtotime($data['created_on']))
        : "";

    $data['trainer_name'] = trim($data['trainer_fname'] . " " . $data['trainer_lname']);

    // Remove individual name fields if not needed
    unset($data['trainer_fname'], $data['trainer_lname']);

    echo json_encode($data);
} else {
    echo json_encode(["error" => "Student not found"]);
}

$stmt->close();
$conn->close();
?>
