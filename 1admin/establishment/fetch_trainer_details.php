<?php
include("../../0config/database.php");

if (!isset($_GET['trainer_id'])) {
    echo json_encode(["error" => "Trainer ID missing"]);
    exit();
}

$trainer_id = $_GET['trainer_id'];

// ✅ Fetch trainer info from users table (including users_account, email, phone)
$sql = "SELECT 
            image_profile,
            fname,
            lname,
            username,
            sex,
            address,
            designation,
            email,
            phone,
            users_account,
            created_on,
            hte_id
        FROM users 
        WHERE users_id = ? AND role = 'trainer'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$result = $stmt->get_result();
$trainer = $result->fetch_assoc();

if (!$trainer) {
    echo json_encode(["error" => "Trainer not found"]);
    exit();
}

// ✅ Format response
$data = [
    "image_profile" => !empty($trainer['image_profile']) ? "../../upload_profile/" . $trainer['image_profile'] : "../../upload_profile/siplogo.png",
    "full_name" => trim(($trainer['fname'] ?? '') . " " . ($trainer['lname'] ?? '')),
    "username" => $trainer['username'] ?? '',
    "sex" => $trainer['sex'] ?? '',
    "address" => $trainer['address'] ?? '',
    "designation" => $trainer['designation'] ?? '',
    "email" => $trainer['email'] ?? '',
    "phone" => $trainer['phone'] ?? '',
    "account_status" => ($trainer['users_account'] ?? '') === 'enabled' ? "Enabled" : "Disabled",
    "created_on" => $trainer['created_on'] ? date("F d, Y - h:i A", strtotime($trainer['created_on'])) : '',
    "hte_id" => $trainer['hte_id'] ?? null,
];

echo json_encode($data);
