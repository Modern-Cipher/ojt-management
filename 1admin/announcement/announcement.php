<?php
session_start();
include("../../0config/database.php"); // Ensure DB connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../0config/logout.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details (image_profile, username, fname)
$sql = "SELECT image_profile, username, fname FROM users WHERE users_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Determine the profile image path
$imageProfile = $user['image_profile'] ?? ''; // Default empty if NULL
$profilePath = "../../upload_profile/" . htmlspecialchars($imageProfile);
$defaultImage = "../../upload_profile/siplogo.png"; // Placeholder image

$userImage = (!empty($imageProfile) && file_exists(__DIR__ . "/" . $profilePath)) ? $profilePath : $defaultImage;

// Determine the display name (username or first name)
$usernameDisplay = !empty($user['fname']) ? htmlspecialchars($user['fname']) : htmlspecialchars($user['username']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcement</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Custom CSS (Separate Files) -->
    <link rel="icon" type="image/ico" href="../../resources/siplogo.ico">
    <link rel="stylesheet" href="../../announcement/css/topbar.css">
    <link rel="stylesheet" href="../../announcement/css/leftsidebar.css">
    <link rel="stylesheet" href="../../announcement/css/dashboard_body.css">
    <link rel="stylesheet" href="../../announcement/css/announcement.css">
    <link rel="stylesheet" href="../../announcement/css/announcement_table.css">

</head>

<body>

    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg topbar-container">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <img src="../../resources/siplogo.png" alt="Logo" class="logo me-2">
                <span class="system-name">Student Internship Program<br>Management System</span>
            </div>

            <div class="d-flex align-items-center topbar-right">
                <!-- <div class="notification-container" data-bs-toggle="modal" data-bs-target="#notificationModal">
                    <i class="fa-solid fa-bell text-warning fs-5 notification-icon"></i>
                    <span class="notification-badge">99</span>
                </div> -->

                <div class="dropdown">
                    <div class="d-flex align-items-center dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown">
                        <img src="<?php echo $userImage; ?>" alt="User Profile" class="profile-img me-2">
                        <span class="fw-normal"><?php echo ucfirst($usernameDisplay); ?></span>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../../1admin/profile/profile.php">View Profile</a></li>
                        <li><a class="dropdown-item text-danger" href="../../0config/logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- Notification Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-end">
            <div class="modal-content notification-modal">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Notifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="list-group">
                        <div class="list-group-item border-bottom">
                            <div class="d-flex justify-content-between">
                                <strong>Announcement</strong>
                                <small class="text-muted">03-02-2025 ; 11:53PM</small>
                            </div>
                            <p class="small text-muted m-0">New Announcement from admin</p>
                        </div>
                        <div class="list-group-item border-bottom">
                            <div class="d-flex justify-content-between">
                                <strong>Chat Room</strong>
                                <small class="text-muted">03-02-2025 ; 11:53PM</small>
                            </div>
                            <p class="small text-muted m-0">New Announcement from Medel</p>
                        </div>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <strong>Comment</strong>
                                <small class="text-muted">03-02-2025 ; 11:53PM</small>
                            </div>
                            <p class="small text-muted m-0">New Announcement from trainer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- sidebar  ####################################################################################################### -->
    <div class="container-fluid main-container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-auto sidebar">
                <ul class="sidebar-menu">
                    <li>
                        <a href="../../1admin/admindashboard.php" data-bs-toggle="tooltip" title="Dashboard">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="34" height="34">
                                <path d="M9 3a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2v-6a2 2 0 0 1 2 -2zm0 12a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2v-2a2 2 0 0 1 2 -2zm10 -4a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2v-6a2 2 0 0 1 2 -2zm0 -8a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2v-2a2 2 0 0 1 2 -2z"></path>
                            </svg>
                        </a>
                    </li>
                    <ul class="sidebar-menu">
                        <!-- Main Item with Toggle -->
                        <li data-bs-toggle="tooltip" title="Internship Reports">
                            <a href="#" class="menu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="34" height="34" stroke-width="2">
                                    <path d="M8 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5.697"></path>
                                    <path d="M18 14v4h4"></path>
                                    <path d="M18 11v-4a2 2 0 0 0 -2 -2h-2"></path>
                                    <path d="M8 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>
                                    <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                                    <path d="M8 11h4"></path>
                                    <path d="M8 15h3"></path>
                                </svg>
                            </a>
                            </a>
                        </li>

                        <!-- Sub-items (Initially Collapsed) -->
                        <ul id="submenu1" class="collapse sub-menu">
                            <li>
                                <a href="../../1admin/internassigned/internassigned.php" data-bs-toggle="tooltip" title="List of Assigned Student">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="26" height="26" stroke-width="2">
                                        <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                        <path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1"></path>
                                        <path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                        <path d="M17 10h2a2 2 0 0 1 2 2v1"></path>
                                        <path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                        <path d="M3 13v-1a2 2 0 0 1 2 -2h2"></path>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="../../1admin/establishment/establishment.php" data-bs-toggle="tooltip" title="Host Training Establishment">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="26" height="26" stroke-width="2">
                                        <path d="M3 21l18 0"></path>
                                        <path d="M9 8l1 0"></path>
                                        <path d="M9 12l1 0"></path>
                                        <path d="M9 16l1 0"></path>
                                        <path d="M14 8l1 0"></path>
                                        <path d="M14 12l1 0"></path>
                                        <path d="M14 16l1 0"></path>
                                        <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16"></path>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </ul>
                    <ul class="sidebar-menu">
                        <!-- Main Item with Toggle -->
                        <li data-bs-toggle="tooltip" title="System Management">
                            <a href="#" class="menu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="34" height="34" stroke-width="2">
                                    <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"></path>
                                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                </svg>
                            </a>
                            </a>
                        </li>

                        <!-- Sub-items (Initially Collapsed) -->
                        <ul id="submenu2" class="collapse sub-menu">
                            <li>
                                <a href="../../1admin/user_account/user_account.php" data-bs-toggle="tooltip" title="Manage Accounts">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="26" height="26" stroke-width="2">
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                        <path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                        <path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855"></path>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </ul>

                    <li>
                        <a href="../../1admin/chat/chat.php" data-bs-toggle="tooltip" title="Chat Room">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="34" height="34" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" stroke="currentColor">
                                <path d="M16.5 10c3.038 0 5.5 2.015 5.5 4.5c0 1.397 -.778 2.645 -2 3.47l0 2.03l-1.964 -1.178a6.649 6.649 0 0 1 -1.536 .178c-3.038 0 -5.5 -2.015 -5.5 -4.5s2.462 -4.5 5.5 -4.5z"></path>
                                <path d="M11.197 15.698c-.69 .196 -1.43 .302 -2.197 .302a8.008 8.008 0 0 1 -2.612 -.432l-2.388 1.432v-2.801c-1.237 -1.082 -2 -2.564 -2 -4.199c0 -3.314 3.134 -6 7 -6c3.782 0 6.863 2.57 7 5.785l0 .233"></path>
                                <path d="M10 8h.01"></path>
                                <path d="M7 8h.01"></path>
                                <path d="M15 14h.01"></path>
                                <path d="M18 14h.01"></path>
                            </svg>
                        </a>
                    </li>

                </ul>
            </div>

            <div class="dashboard-container">
                <!-- ✅ Full Width Good Day Admin -->
                <h5 class="dashboard-header">
                    <div class="dashboard-main">
                        <!-- Middle Content - Dashboard -->
                        <div class="dashboard-content">
                            <br>
                            <div class="d-flex justify-content-between align-items-center mb-3 announcement-header">
                                <h5 class="announcement-title">Announcement</h5>
                                <div class="d-flex align-items-center">
                                    <input type="text" class="form-control search-input" id="searchInput" placeholder="Search...">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal" data-tooltip="Add New Announcement">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="20" height="20" stroke-width="3" stroke-linejoin="round" stroke-linecap="round" stroke="currentColor">
                                            <path d="M12 5l0 14"></path>
                                            <path d="M5 12l14 0"></path>
                                        </svg>
                                    </button>

                                </div>
                            </div>

                            <!-- ✅ Add Announcement Modal -->
                            <div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="../../1admin/announcement/add_announcement.php" method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addAnnouncementLabel">Add New Announcement</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">
                                                <!-- Title -->
                                                <div class="mb-3">
                                                    <label for="title" class="form-label">Title</label>
                                                    <input type="text" class="form-control" name="title" id="title" required>
                                                </div>

                                                <!-- Content -->
                                                <div class="mb-3">
                                                    <label for="content" class="form-label">Content</label>
                                                    <textarea class="form-control" name="content" id="content" rows="4" required></textarea>
                                                </div>

                                                <!-- Role -->
                                                <div class="mb-3">
                                                    <label for="role" class="form-label">Visible To</label>
                                                    <select class="form-select" name="role" id="role" required>
                                                        <option value="all">All</option>
                                                        <option value="coordinator">Coordinator</option>
                                                        <option value="trainer">Host Trainer</option>
                                                        <option value="student">Student</option>
                                                    </select>
                                                </div>

                                                <!-- Status (Dropdown) -->
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Status</label>
                                                    <select class="form-select" name="status" id="status" required>
                                                        <option value="posted">Post</option>
                                                        <option value="drafted">Save as Draft</option>
                                                    </select>
                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>


                            <!-- ✅ Announcements Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="announcementTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No. <i class="fa-solid fa-sort"></i></th>
                                            <th>Title <i class="fa-solid fa-sort"></i></th>
                                            <th>Content <i class="fa-solid fa-sort"></i></th>
                                            <th>To <i class="fa-solid fa-sort"></i></th>
                                            <th>Posted <i class="fa-solid fa-sort"></i></th>
                                            <th>Status <i class="fa-solid fa-sort"></i></th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        include("../../0config/database.php");

                                        $no = 1;
                                        $user_id = $_SESSION['user_id'];

                                        $sql = "SELECT announcements_id, title, content, role, created_at, announce_status 
                                        FROM announcements 
                                        WHERE users_id = ? 
                                        ORDER BY created_at DESC";


                                        $stmt = $conn->prepare($sql);
                                        $stmt->bind_param("i", $user_id);
                                        $stmt->execute();
                                        $result = $stmt->get_result();

                                        while ($row = $result->fetch_assoc()) {
                                            $statusBadge = ($row['announce_status'] === 'posted')
                                                ? "<span class='badge bg-success'>Posted</span>"
                                                : "<span class='badge bg-danger'>Draft</span>";

                                            $postedAt = date("M d, Y - h:iA", strtotime($row['created_at']));

                                            echo "<tr>";
                                            echo "<td data-label='No.'>" . $no++ . "</td>";
                                            echo "<td data-label='Title'><strong>" . htmlspecialchars($row['title']) . "</strong></td>";
                                            echo "<td data-label='Content'>" . htmlspecialchars($row['content']) . "</td>";
                                            echo "<td data-label='To'>" . ucfirst($row['role']) . "</td>";
                                            echo "<td data-label='Posted'>" . $postedAt . "</td>";
                                            $status_class = ($row['announce_status'] === 'posted') ? 'bg-success' : 'bg-danger';
                                            $status_label = ucfirst($row['announce_status']);
                                            echo "<td data-label='Status' id='status-cell-{$row['announcements_id']}'>
                                                    <span class='badge $status_class'>$status_label</span>
                                                </td>";
                                            echo "<td data-label='Action'>
                                                    <div class='action-group'>
                                                        <button class='btn btn-outline-secondary btn-sm edit-btn' 
                                                            data-bs-toggle='modal' 
                                                            data-bs-target='#editAnnouncementModal'
                                                            data-id='" . $row['announcements_id'] . "'
                                                            data-title='" . htmlspecialchars($row['title'], ENT_QUOTES) . "'
                                                            data-content='" . htmlspecialchars($row['content'], ENT_QUOTES) . "'
                                                            data-role='" . $row['role'] . "'
                                                            data-tooltip='Edit Post'>
                                                            <i class='fas fa-edit'></i>
                                                        </button>

                                                        <div class='form-check form-switch' data-bs-toggle='tooltip' title='Post / Draft Response'>
                                                                <input 
                                                                class='form-check-input status-toggle' 
                                                                type='checkbox' 
                                                                data-id='" . $row['announcements_id'] . "' 
                                                                " . ($row['announce_status'] === 'posted' ? "checked" : "") . ">

                                                        </div>
                                                    </div>
                                                </td>";
                                            echo "</tr>";
                                        }

                                        $stmt->close();
                                        $conn->close();
                                        ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>

                        <!-- ✅ Announcements Section -->
                        <div id="announcementContainer" class="announcements">
                            <div class="announcement-header" style=" margin-top:30px;">
                                <h5>Announcement</h5>
                            </div>

                            <!-- ✅ Announcement Item -->
                            <div id="latestAnnouncement">
                                <?php include("../../1admin/announcement/fetch_latest_announcement.php"); ?>
                            </div>

                            <!-- ✅ View All History -->
                            <a href="#" class="view-history" onclick="toggleAnnouncements(event)">
                                View all history <i class="fa-solid fa-arrow-right"></i>
                            </a>

                            <!-- ✅ Hidden Additional Announcements -->
                            <div id="extra-announcements" class="extra-announcements" style="display: none;">
                                <div class="announcement-header">
                                    <h5>Recent Announcements</h5>
                                </div>
                                <div id="recentAnnouncements">
                                    <?php include("../../1admin/announcement/fetch_recent_announcements.php"); ?>
                                </div>
                            </div>
                        </div>

                    </div>
            </div>
        </div>
    </div>

    <!-- ✅ Edit Announcement Modal -->
    <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="../../1admin/announcement/update_announcement.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAnnouncementLabel">Edit Announcement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Hidden ID -->
                        <input type="hidden" name="announcement_id" id="edit_announcement_id">

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" id="edit_title" required>
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label for="edit_content" class="form-label">Content</label>
                            <textarea class="form-control" name="content" id="edit_content" rows="4" required></textarea>
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label for="edit_role" class="form-label">Visible To</label>
                            <select class="form-select" name="role" id="edit_role" required>
                                <option value="all">All</option>
                                <option value="coordinator">Coordinator</option>
                                <option value="trainer">Host Trainer</option>
                                <option value="student">Student</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const editButtons = document.querySelectorAll(".edit-btn");

            editButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const id = this.getAttribute("data-id");
                    const title = this.getAttribute("data-title");
                    const content = this.getAttribute("data-content");
                    const role = this.getAttribute("data-role");

                    document.getElementById("edit_announcement_id").value = id;
                    document.getElementById("edit_title").value = title;
                    document.getElementById("edit_content").value = content;
                    document.getElementById("edit_role").value = role;
                });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".status-toggle").forEach(toggle => {
                toggle.addEventListener("change", function() {
                    const announcementId = this.getAttribute("data-id");
                    const newStatus = this.checked ? "posted" : "drafted";

                    fetch("../../1admin/announcement/toggle_status.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: `id=${announcementId}&status=${newStatus}`
                        })
                        .then(res => res.text())
                        .then(response => {
                            console.log(response);

                            // 🔁 Update the badge instantly
                            const badgeCell = document.getElementById(`status-cell-${announcementId}`);
                            if (badgeCell) {
                                const badge = document.createElement("span");
                                badge.className = "badge " + (newStatus === "posted" ? "bg-success" : "bg-danger");
                                badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                                badgeCell.innerHTML = ""; // clear current badge
                                badgeCell.appendChild(badge);
                            }
                        })
                        .catch(err => {
                            alert("Error updating status.");
                            console.error(err);
                        });
                });
            });
        });
    </script>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const commentForm = document.getElementById("commentForm");
            const commentInput = document.getElementById("commentText");
            const commentSection = document.querySelector(".comment-section");
            const commentMessage = document.getElementById("commentMessage");

            if (commentForm && commentInput && commentSection) {
                commentForm.addEventListener("submit", function(e) {
                    e.preventDefault();

                    const formData = new FormData(commentForm);
                    const announcementId = commentForm.querySelector("input[name='announcement_id']").value;

                    fetch("../../1admin/announcement/save_comment.php", {
                            method: "POST",
                            body: formData
                        })
                        .then(response => response.text())
                        .then(data => {
                            if (data.trim() === "success") {
                                commentInput.value = "";
                                commentMessage.innerHTML = "<span style='color: green;'>Comment posted successfully!</span>";
                                loadComments(announcementId);
                            } else {
                                commentMessage.innerHTML = "<span style='color: red;'>" + data + "</span>";
                            }
                        })
                        .catch(error => {
                            commentMessage.innerHTML = "<span style='color: red;'>Something went wrong.</span>";
                            console.error("Error:", error);
                        });
                });

                function loadComments(announcementId) {
                    fetch("../../1admin/announcement/fetch_comments.php?announcement_id=" + announcementId)
                        .then(response => response.text())
                        .then(data => {
                            commentSection.innerHTML = data;
                        });
                }
            }
        });
    </script>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".status-toggle").forEach(toggle => {
                toggle.addEventListener("change", function() {
                    const announcementId = this.getAttribute("data-id");
                    const newStatus = this.checked ? "posted" : "drafted";

                    fetch("../../1admin/announcement/toggle_status.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: `id=${announcementId}&status=${newStatus}`
                        })
                        .then(res => res.text())
                        .then(response => {
                            console.log("Status toggled:", response);

                            // ✅ Reload the latest and recent announcements
                            reloadAnnouncementSection();
                        })
                        .catch(err => {
                            console.error("Toggle error:", err);
                        });
                });
            });
        });

        function reloadAnnouncementSection() {
            // Reload latest
            fetch("../../1admin/announcement/fetch_latest_announcement.php")
                .then(res => res.text())
                .then(data => {
                    document.getElementById("latestAnnouncement").innerHTML = data;
                });

            // Reload recent
            fetch("../../1admin/announcement/fetch_recent_announcements.php")
                .then(res => res.text())
                .then(data => {
                    document.getElementById("recentAnnouncements").innerHTML = data;
                });
        }
    </script>




    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize Bootstrap tooltips and auto-adjust placement
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    boundary: 'window', // Prevents tooltip from going off-screen
                    fallbackPlacements: ['top', 'bottom', 'left', 'right'], // Auto-adjust
                    html: true // Allows <br/> tags inside tooltips
                });
            });

            // Hide Bootstrap tooltip on click
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(element) {
                element.addEventListener("click", function() {
                    var tooltipInstance = bootstrap.Tooltip.getInstance(this);
                    if (tooltipInstance) {
                        tooltipInstance.hide();
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll("[data-tooltip]").forEach(element => {
                const originalTooltip = element.getAttribute("data-tooltip"); // Store original text

                // ✅ Detect tooltip length and add `data-tooltip-length` attribute
                if (originalTooltip && originalTooltip.length > 25) {
                    element.setAttribute("data-tooltip-length", "long"); // Mark long tooltips
                }

                // ✅ Hide tooltip on click
                element.addEventListener("click", function() {
                    this.removeAttribute("data-tooltip"); // Remove only for clicked element

                    // Restore tooltip after 500ms
                    setTimeout(() => {
                        if (!this.getAttribute("data-tooltip")) { // Restore only if still missing
                            this.setAttribute("data-tooltip", originalTooltip);

                            // Reapply length detection
                            if (originalTooltip.length > 25) {
                                this.setAttribute("data-tooltip-length", "long");
                            }
                        }
                    }, 500); // Adjust delay as needed
                });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let searchInput = document.getElementById("searchInput");
            let table = document.getElementById("announcementTable");
            let tbody = table.getElementsByTagName("tbody")[0];

            searchInput.addEventListener("keyup", function() {
                let filter = searchInput.value.toLowerCase();
                let rows = tbody.getElementsByTagName("tr");

                for (let i = 0; i < rows.length; i++) {
                    let cells = rows[i].getElementsByTagName("td");
                    let match = false;

                    for (let j = 0; j < cells.length; j++) {
                        if (cells[j].textContent.toLowerCase().includes(filter)) {
                            match = true;
                            break;
                        }
                    }

                    rows[i].style.display = match ? "" : "none";
                }
            });
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let table = document.getElementById("announcementTable");
            let headers = table.querySelectorAll("thead th");
            let tbody = table.querySelector("tbody");
            let sortOrder = {};

            headers.forEach((header, columnIndex) => {
                header.addEventListener("click", function() {
                    let rows = Array.from(tbody.rows);
                    sortOrder[columnIndex] = !sortOrder[columnIndex];

                    rows.sort((a, b) => {
                        let aValue = a.cells[columnIndex].textContent.trim().toLowerCase();
                        let bValue = b.cells[columnIndex].textContent.trim().toLowerCase();

                        if (!isNaN(aValue) && !isNaN(bValue)) {
                            return sortOrder[columnIndex] ? aValue - bValue : bValue - aValue;
                        }

                        return sortOrder[columnIndex] ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
                    });

                    tbody.append(...rows);
                });
            });
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        function toggleAnnouncements(event) {
            event.preventDefault();
            let extraAnnouncements = document.getElementById("extra-announcements");
            let toggleText = document.querySelector(".view-history");

            if (extraAnnouncements.style.display === "none" || extraAnnouncements.style.display === "") {
                extraAnnouncements.style.display = "block";
                toggleText.innerHTML = "See less <i class='fa-solid fa-arrow-up'></i>";
            } else {
                extraAnnouncements.style.display = "none";
                toggleText.innerHTML = "View all history <i class='fa-solid fa-arrow-right'></i>";
            }
        }
    </script>
</body>

</html>