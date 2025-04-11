<?php
session_start();
include("../0config/database.php"); // Ensure DB connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../0config/logout.php");
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
$profilePath = "../upload_profile/" . htmlspecialchars($imageProfile);
$defaultImage = "../upload_profile/siplogo.png"; // Placeholder image

$userImage = (!empty($imageProfile) && file_exists(__DIR__ . "/" . $profilePath)) ? $profilePath : $defaultImage;

// Determine the display name (username or first name)
$usernameDisplay = !empty($user['fname']) ? htmlspecialchars($user['fname']) : htmlspecialchars($user['username']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host Trainer Dashboard</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Custom CSS (Separate Files) -->
    <link rel="icon" type="image/ico" href="../resources/siplogo.ico">
    <link rel="stylesheet" href="../3hte/css/topbar.css">
    <link rel="stylesheet" href="../3hte/css/leftsidebar.css">
    <link rel="stylesheet" href="../3hte/css/dashboard_body.css">
    <link rel="stylesheet" href="../3hte/css/announcement.css">
    <link rel="stylesheet" href="../3hte/css/dashboard_graphs.css">

</head>

<body>

    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg topbar-container">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <img src="../resources/siplogo.png" alt="Logo" class="logo me-2">
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
                        <li><a class="dropdown-item" href="../3hte/profile/profile.php">View Profile</a></li>
                        <li><a class="dropdown-item text-danger" href="../0config/logout.php">Logout</a></li>
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
                    <!-- <li><a href="../1admin/admindashboard.php" data-bs-toggle="tooltip" title="Dashboard"><i class="fa-solid fa-th-large"></i></a></li> -->
                    <li>
                        <a href="../3hte/htedashboard.php" data-bs-toggle="tooltip" title="Dashboard">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="34" height="34">
                                <path d="M9 3a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2v-6a2 2 0 0 1 2 -2zm0 12a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2v-2a2 2 0 0 1 2 -2zm10 -4a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2v-6a2 2 0 0 1 2 -2zm0 -8a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2v-2a2 2 0 0 1 2 -2z"></path>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="../3hte/document/document.php" data-bs-toggle="tooltip" title="Document Requirements">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="34" height="34" stroke-width="2">
                                <path d="M13 19h-8a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2h4l3 3h7a2 2 0 0 1 2 2v4"></path>
                                <path d="M16 22l5 -5"></path>
                                <path d="M21 21.5v-4.5h-4.5"></path>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="../3hte/intern_list/intern_list.php" data-bs-toggle="tooltip" title="List of Assign Interns">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="34" height="34" stroke-width="2">
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
                        <a href="../3hte/chat/chat.php" data-bs-toggle="tooltip" title="Chat Room">
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
                <h5 class="dashboard-header">Good Day, <?php echo ucfirst($usernameDisplay); ?>
                    <div class="dashboard-main">
                        <!-- Middle Content - Dashboard -->
                        <div class="dashboard-content">
                            <br>
                            <h5>Dashboard</h5>
                            <div class="dashboard-grid">

                                <!-- Total Interns -->
                                <div class="dashboard-card">
                                    <div class="dashboard-left">
                                        <i class="fa-solid fa-users dashboard-icon"></i>
                                        <p>Assigned Interns</p>
                                    </div>
                                    <div class="dashboard-right">
                                        <h3>0</h3>
                                    </div>
                                </div>

                                <!-- Documents Submitted -->
                                <div class="dashboard-card">
                                    <div class="dashboard-left">
                                        <i class="fa-solid fa-file-alt dashboard-icon"></i>
                                        <p>Documents Submitted</p>
                                    </div>
                                    <div class="dashboard-right">
                                        <h3>0</h3>
                                    </div>
                                </div>

                            </div>

                            <!-- Removed Performance Overview and Graphs -->

                        </div>


                        <div id="announcementContainer" class="announcements">
                            <div class="announcement-header" style=" margin-top:30px;">
                                <h5>Announcement</h5>
                                <a href="../3hte/announcement/announcement.php" data-bs-toggle="tooltip" title="Create New Announcement">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="34" height="34" stroke-width="2">
                                        <path d="M18 8a3 3 0 0 1 0 6"></path>
                                        <path d="M10 8v11a1 1 0 0 1 -1 1h-1a1 1 0 0 1 -1 -1v-5"></path>
                                        <path d="M12 8h0l4.524 -3.77a.9 .9 0 0 1 1.476 .692v12.156a.9 .9 0 0 1 -1.476 .692l-4.524 -3.77h-8a1 1 0 0 1 -1 -1v-4a1 1 0 0 1 1 -1h8"></path>
                                    </svg>
                                </a>
                            </div>

                            <!-- ✅ Announcement Item -->
                            <div id="latestAnnouncement">
                                <?php include("../3hte/announcement/fetch_latest_announcement_trainer1.php"); ?>
                            </div>

                            <!-- ✅ View All History -->
                            <a href="#" class="view-history" onclick="toggleAnnouncements(event)">
                                View all history <i class="fa-solid fa-square-caret-down"></i>
                            </a>

                            <!-- ✅ Hidden Additional Announcements -->
                            <div id="extra-announcements" class="extra-announcements" style="display: none;">
                                <div class="announcement-header">
                                    <h5>Recent Announcements</h5>
                                </div>
                                <div id="recentAnnouncements">
                                    <?php include("../3hte/announcement/fetch_recent_announcement_trainer1.php"); ?>
                                </div>
                                <a href="#" class="view-history" onclick="toggleAnnouncements(event)">
                                    Hide <i class="fa-solid fa-square-caret-up"></i>
                                </a>
                            </div>
                        </div>
                    </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../3hte/js/announce.js"></script>
    <script src="../3hte/js/toggle.js"></script>
    <script src="../3hte/js/dashboard_trainer.js"></script>


</body>

</html>