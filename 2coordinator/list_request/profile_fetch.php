<?php
session_start();
include("../../0config/database.php");

header('Content-Type: application/json');

if (!isset($_GET['user_id'])) {
    echo json_encode(['error' => 'Missing user ID']);
    exit;
}

$user_id = intval($_GET['user_id']);

$sql = "SELECT 
            CONCAT(u.fname, ' ', u.lname) AS fullname,
            u.image_profile, 
            u.username, 
            u.course, 
            u.address, 
            u.email, 
            u.phone, 
            u.school_id,
            u.activate,
            DATE_FORMAT(u.created_on, '%M %d, %Y - %h:%i %p') as created_on
        FROM users u 
        WHERE u.users_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    function sanitize($value)
    {
        return !empty($value) ? $value : "-";
    }

    $image = !empty($user['image_profile']) && file_exists("../../upload_profile/" . $user['image_profile'])
        ? "../../upload_profile/" . $user['image_profile']
        : "../../resources/siplogo.png";

    echo json_encode([
        'fullname' => sanitize($user['fullname']),
        'image_profile' => $image,
        'username' => sanitize($user['username']),
        'course' => sanitize($user['course']),
        'address' => sanitize($user['address']),
        'email' => sanitize($user['email']),
        'phone' => sanitize($user['phone']),
        'school_id' => sanitize($user['school_id']),
        'activated' => $user['activate'] == 1 ? 'Activated' : 'Inactive',
        'created_on' => sanitize($user['created_on'])
    ]);
} else {
    echo json_encode(['error' => 'User not found']);
}

$stmt->close();
$conn->close();
