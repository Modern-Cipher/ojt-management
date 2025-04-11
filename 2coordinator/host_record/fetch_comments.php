<?php
session_start();
include("../../0config/database.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$coordinatorId = $_SESSION['user_id'];

$uploadsId = $_GET['uploads_id'] ?? null;
$uploadedbyId = $_GET['uploadedby_id'] ?? null;

if (!$uploadsId || !$uploadedbyId) {
    echo json_encode([]);
    exit();
}

// Get filename_id
$filenameQuery = $conn->prepare("SELECT filename_id FROM uploads WHERE uploads_id = ?");
$filenameQuery->bind_param("i", $uploadsId);
$filenameQuery->execute();
$filenameResult = $filenameQuery->get_result();
$filenameRow = $filenameResult->fetch_assoc();
$filenameId = $filenameRow['filename_id'];
$filenameQuery->close();

$query = "
SELECT 
    fc.*, 
    CONCAT(u.fname, ' ', u.lname) AS fullname, 
    u.image_profile
FROM file_comments fc
LEFT JOIN users u ON u.users_id = fc.commenter_id
WHERE fc.filename_id = ?
AND (
    (fc.commenter_id = ? AND fc.uploadedby_id = ?) -- coordinator reply to trainer
    OR
    (fc.commenter_id = ? AND (fc.uploadedby_id IS NULL OR fc.uploadedby_id = 0)) -- trainer comment (no uploadedby_id)
)
ORDER BY fc.created_at ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("iiii", $filenameId, $coordinatorId, $uploadedbyId, $uploadedbyId);
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
