<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../../0config/database.php");

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) exit("Unauthorized");

// Get trainer's institute
$institute = '';
$userQuery = $conn->prepare("SELECT institute FROM users WHERE users_id = ?");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$result = $userQuery->get_result();
if ($data = $result->fetch_assoc()) {
    $institute = $data['institute'];
}
$userQuery->close();

// Get the latest posted announcement ID in the same institute
$latestSql = "SELECT a.announcements_id
              FROM announcements a
              JOIN users u ON a.users_id = u.users_id
              WHERE a.announce_status = 'posted'
              AND u.institute = ?
              ORDER BY a.created_at DESC LIMIT 1";
$latestStmt = $conn->prepare($latestSql);
$latestStmt->bind_param("s", $institute);
$latestStmt->execute();
$latestResult = $latestStmt->get_result();
$latestRow = $latestResult->fetch_assoc();
$latestId = $latestRow['announcements_id'] ?? 0;
$latestStmt->close();

// Fetch RECENT announcements (excluding latest)
$sql = "SELECT a.announcements_id, a.title, a.content, a.created_at, u.username, u.fname, u.role, u.image_profile
        FROM announcements a
        JOIN users u ON a.users_id = u.users_id
        WHERE a.announce_status = 'posted'
        AND u.institute = ?
        AND (
            (u.role IN ('admin', 'coordinator') AND a.role IN ('all', 'trainer'))
            OR a.users_id = ?
        )
        AND a.announcements_id != ?
        ORDER BY a.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $institute, $user_id, $latestId);
$stmt->execute();
$result = $stmt->get_result();

// Your existing rendering logic here (unchanged)

// ✅ Loop and output
if ($result->num_rows > 0) {
    while ($announcement = $result->fetch_assoc()) {
        $announcementId = $announcement['announcements_id'];
        $creatorName = $announcement['fname'] ?: $announcement['username'];
        $creatorRole = ucfirst($announcement['role']);
        $formattedDate = date("M d, Y - h:i A", strtotime($announcement['created_at']));
        $imgPath = (!empty($announcement['image_profile']) && file_exists("../../upload_profile/" . $announcement['image_profile']))
            ? "../../upload_profile/" . $announcement['image_profile']
            : "../../upload_profile/siplogo.png";

        echo "<div class='announcement-item'>
            <div class='announcement-profile'>
                <img src='$imgPath' alt='User'>
                <div class='announcement-info'>
                    <div>$creatorName</div>
                    <div class='announcement-role'>$creatorRole</div>
                    <div class='announcement-time'>$formattedDate</div>
                </div>
            </div>
            <p class='announcement-title'>" . htmlspecialchars($announcement['title']) . "</p>
            <p class='announcement-text'>" . nl2br(htmlspecialchars($announcement['content'])) . "</p>
        </div>";

        // Fetch and display comments (same as before)
        $commentQuery = $conn->prepare("SELECT c.comments_id, c.comments, c.created_at, c.users_id, u.username, u.fname, u.role, u.image_profile
                                        FROM comments c
                                        JOIN users u ON c.users_id = u.users_id
                                        WHERE c.announcements_id = ?
                                        ORDER BY c.created_at DESC");
        $commentQuery->bind_param("i", $announcementId);
        $commentQuery->execute();
        $commentResult = $commentQuery->get_result();

        echo "<hr class='separator'><h5 class='comment-label'>Comment Section</h5><div class='comment-section'>";
        while ($comment = $commentResult->fetch_assoc()) {
            $commentId = $comment['comments_id'];
            $commenterName = $comment['fname'] ?: $comment['username'];
            $commentRole = ucfirst($comment['role']);
            $commentDate = date("M d, Y - h:i A", strtotime($comment['created_at']));
            $commentImg = (!empty($comment['image_profile']) && file_exists("../../upload_profile/" . $comment['image_profile']))
                ? "../../upload_profile/" . $comment['image_profile']
                : "../../upload_profile/siplogo.png";

            echo "
            <style>
            .comment-section {
                max-height: 300px;
                overflow-y: auto;
                padding-right: 10px;
            }
            </style>
            <div class='comment-item'>
                <div class='comment-profile'>
                    <img src='$commentImg' alt='User'>
                    <div class='comment-details'>
                        <div>$commenterName</div>
                        <div class='comment-role'>$commentRole</div>
                        <div class='comment-time'>$commentDate</div>
                    </div>
                </div>
                <p class='comment-text'>" . nl2br(htmlspecialchars($comment['comments'])) .
                ($comment['users_id'] == $user_id ? "
                    <a href='delete_comment_trainer.php?comment_id=$commentId' onclick=\"return confirm('Are you sure you want to delete this comment?')\" data-bs-toggle='tooltip' title='Remove' style='margin-left:10px; color:red;'>
                        <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-linecap='round' stroke-linejoin='round' width='18' height='18' stroke-width='2'>
                            <path d='M4 7h16'></path>
                            <path d='M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12'></path>
                            <path d='M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3'></path>
                            <path d='M10 12l4 4m0 -4l-4 4'></path>
                        </svg>
                    </a>" : "") . "</p></div><hr class='separator'>";
        }

        echo "</div>
        <form class='comment-input mt-2' method='POST' action='../../3hte/announcement/save_comment_trainer.php'>
            <input type='hidden' name='announcement_id' value='$announcementId'>
            <input type='text' name='comments' placeholder='Comment here...' required>
            <button class='send-comment' type='submit'>
                <i class='fas fa-paper-plane'></i>
            </button>
        </form><hr class='separator'>";
    }
} else {
    echo "<p class='text-muted'>No additional announcements available.</p>";
}

$stmt->close();
$conn->close();
