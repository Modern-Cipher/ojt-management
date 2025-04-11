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
    <title>Post Deployment</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <!-- Custom CSS (Separate Files) -->
    <link rel="icon" type="image/png" href="../../resources/siplogo.ico">
    <link rel="stylesheet" href="../../4student/postdeploy/css/topbar.css">
    <link rel="stylesheet" href="../../4student/postdeploy/css/leftsidebar.css">
    <link rel="stylesheet" href="../../4student/postdeploy/css/dashboard_body.css">
    <link rel="stylesheet" href="../../4student/postdeploy/css/card_design.css">

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
                        <li><a class="dropdown-item" href="../../4student/profile/profile.php">View Profile</a></li>
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
                    <!-- <li><a href="../1admin/admindashboard.php" data-bs-toggle="tooltip" title="Dashboard"><i class="fa-solid fa-th-large"></i></a></li> -->
                    <li>
                        <a href="../../4student/studentdashboard.php" data-bs-toggle="tooltip" title="Dashboard">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="34" height="34">
                                <path d="M9 3a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2v-6a2 2 0 0 1 2 -2zm0 12a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2v-2a2 2 0 0 1 2 -2zm10 -4a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2v-6a2 2 0 0 1 2 -2zm0 -8a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2v-2a2 2 0 0 1 2 -2z"></path>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="../../4student/predeploy/predeploy.php" data-bs-toggle="tooltip" title="Pre Deployment">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="34" height="34" stroke-width="2">
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                <path d="M12 21h-5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v4.5"></path>
                                <path d="M16.5 17.5m-2.5 0a2.5 2.5 0 1 0 5 0a2.5 2.5 0 1 0 -5 0"></path>
                                <path d="M18.5 19.5l2.5 2.5"></path>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="../../4student/postdeploy/postdeploy.php" data-bs-toggle="tooltip" title="Post Deployment">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="34" height="34" stroke-width="2">
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                <path d="M5 8v-3a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-5"></path>
                                <path d="M6 14m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                <path d="M4.5 17l-1.5 5l3 -1.5l3 1.5l-1.5 -5"></path>
                            </svg>
                        </a>
                    </li>

                    <li>
                        <a href="../../4student/journal/journal.php" data-bs-toggle="tooltip" title="My Journal">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="34" height="34" stroke-width="2">
                                <path d="M6 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-11a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1m3 0v18"></path>
                                <path d="M13 8l2 0"></path>
                                <path d="M13 12l2 0"></path>
                            </svg>
                        </a>
                    </li>
                    <!-- <li>
                        <a href="../../4student/studentdashboard.php" data-bs-toggle="tooltip" title="Certificates and Final Grades">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="34" height="34" stroke-width="2">
                                <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z"></path>
                            </svg>
                        </a>
                    </li> -->
                    <li>
                        <a href="../../4student/chat/chat.php" data-bs-toggle="tooltip" title="Chat Room">
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
        </div>
        <div class="dashboard-container">
            <h5 class="dashboard-header">
                <div class="dashboard-main">
                    <div class="dashboard-content">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="m-0">Post Deployment Requirements</h5>
                            <div class="search-container d-flex align-items-center gap-2">
                                <input
                                    type="text"
                                    id="searchInput"
                                    class="form-control form-control-sm no-clear"
                                    placeholder="Search Requirement..."
                                    style="max-width: 250px;" />
                                <button
                                    type="button"
                                    id="clearSearchBtn"
                                    class="btn btn-sm btn-outline-secondary"
                                    style="display: none;"
                                    title="Clear Search">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                        </div>

                        <div class="dashboard-grid"></div>

                        <!-- Toast & Loader -->
                        <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

                    </div>
                </div>
            </h5>
        </div>
    </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../4student/postdeploy/js/tooltip.js"></script>
    <script src="../../4student/postdeploy/js/search.js"></script>
    <script src="../../4student/postdeploy/js/upload.js"></script>
    <script src="../../4student/postdeploy/js/load_files.js"></script>
    <script>
        const sessionUserId = <?= $_SESSION['user_id'] ?? 'null' ?>;
    </script>
</body>

</html>