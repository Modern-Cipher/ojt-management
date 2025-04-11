<?php
include("../../0config/database.php");

$announcement_id = $_GET['announcement_id'] ?? 0;
$announcement_id = (int)$announcement_id;

$sql = "SELECT c.comments, c.created_at, u.fname, u.username, u.role, u.image_profile, c.users_id
        FROM comments c
        JOIN users u ON c.users_id = u.users_id
        WHERE c.announcements_id = ?
        ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $announcement_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $name = !empty($row['fname']) ? $row['fname'] : $row['username'];
    $role = ucfirst($row['role']);
    $time = date("M d, Y - h:i A", strtotime($row['created_at']));
    $img = !empty($row['image_profile']) && file_exists("../../upload_profile/" . $row['image_profile']) 
           ? "../../upload_profile/" . $row['image_profile'] 
           : "../../upload_profile/siplogo.png";

    echo "
    <div class='comment-item'>
        <div class='comment-profile'>
            <img src='$img' alt='User'>
            <div class='comment-details'>
                <div>$name</div>
                <div class='comment-role'>$role</div>
                <div class='comment-time'>$time</div>
            </div>
        </div>
        <p class='comment-text'>" . nl2br(htmlspecialchars($row['comments'])) . "</p>
    </div>
    <hr class='separator'>
    ";
}

$stmt->close();
$conn->close();
?>
