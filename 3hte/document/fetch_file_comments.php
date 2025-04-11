<?php
session_start();
include '../../0config/database.php';

$filename_id = $_GET['filename_id'] ?? null;
$current_user = $_SESSION['user_id'] ?? null;

if (!$filename_id || !$current_user) {
    echo json_encode(["status" => "error", "message" => "Missing data"]);
    exit;
}

$query = "
SELECT 
    fc.*, 
    CONCAT(u.fname, ' ', u.lname) AS fullname, 
    u.image_profile
FROM file_comments fc
LEFT JOIN users u ON u.users_id = fc.commenter_id
WHERE fc.filename_id = ?
AND (
    fc.commenter_id = ? -- own comment
    OR fc.uploadedby_id = ? -- reply from checker to you
)
ORDER BY fc.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $filename_id, $current_user, $current_user);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $image = $row['image_profile'];
    $profilePath = "../../upload_profile/" . $image;
    $defaultImage = "../../upload_profile/siplogo.png";

    $row['profile_image'] = (!empty($image) && file_exists(__DIR__ . "/" . $profilePath))
        ? $profilePath
        : $defaultImage;

    $comments[] = $row;
}

echo json_encode(["status" => "success", "comments" => $comments]);
?>
