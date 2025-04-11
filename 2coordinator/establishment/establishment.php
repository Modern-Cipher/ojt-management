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
    <title>Establisment</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Custom CSS (Separate Files) -->
    <link rel="icon" type="image/png" href="../../resources/siplogo.ico">
    <link rel="stylesheet" href="../../2coordinator/css/topbar.css">
    <link rel="stylesheet" href="../../2coordinator/css/leftsidebar.css">
    <link rel="stylesheet" href="../../2coordinator/establishment/css/establishment.css">
    <link rel="stylesheet" href="../../2coordinator/establishment/css/establishment_table.css">

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
                        <li><a class="dropdown-item" href="../../2coordinator/profile/profile.php">View Profile</a></li>
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
                        <a href="../../2coordinator/coordinatordashboard.php" data-bs-toggle="tooltip" title="Dashboard">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="34" height="34">
                                <path d="M9 3a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2v-6a2 2 0 0 1 2 -2zm0 12a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2v-2a2 2 0 0 1 2 -2zm10 -4a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2v-6a2 2 0 0 1 2 -2zm0 -8a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2v-2a2 2 0 0 1 2 -2z"></path>
                            </svg>
                        </a>
                    </li>
                    <ul class="sidebar-menu">
                        <!-- Main Item with Toggle -->
                        <li data-bs-toggle="tooltip" title="Host Training Establishment">
                            <a href="#" class="menu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="34" height="34" stroke-width="2">
                                    <path d="M4 21v-15c0 -1 1 -2 2 -2h5c1 0 2 1 2 2v15"></path>
                                    <path d="M16 8h2c1 0 2 1 2 2v11"></path>
                                    <path d="M3 21h18"></path>
                                    <path d="M10 12v0"></path>
                                    <path d="M10 16v0"></path>
                                    <path d="M10 8v0"></path>
                                    <path d="M7 12v0"></path>
                                    <path d="M7 16v0"></path>
                                    <path d="M7 8v0"></path>
                                    <path d="M17 12v0"></path>
                                    <path d="M17 16v0"></path>
                                </svg>
                            </a>
                            </a>
                        </li>

                        <!-- Sub-items (Initially Collapsed) -->
                        <ul id="submenu1" class="collapse sub-menu">
                            <li>
                                <a href="../../2coordinator/establishment/establishment.php" data-bs-toggle="tooltip" title="Host Establishments">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="26" height="26" stroke-width="2">
                                        <path d="M3 21h9"></path>
                                        <path d="M9 8h1"></path>
                                        <path d="M9 12h1"></path>
                                        <path d="M9 16h1"></path>
                                        <path d="M14 8h1"></path>
                                        <path d="M14 12h1"></path>
                                        <path d="M5 21v-16c0 -.53 .211 -1.039 .586 -1.414c.375 -.375 .884 -.586 1.414 -.586h10c.53 0 1.039 .211 1.414 .586c.375 .375 .586 .884 .586 1.414v7"></path>
                                        <path d="M16 19h6"></path>
                                        <path d="M19 16v6"></path>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="../../2coordinator/trainer/trainer.php" data-bs-toggle="tooltip" title="Host Trainers">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="26" height="26" stroke-width="2">
                                        <path d="M19.03 17.818a3 3 0 0 0 1.97 -2.818v-8a3 3 0 0 0 -3 -3h-12a3 3 0 0 0 -3 3v8c0 1.317 .85 2.436 2.03 2.84"></path>
                                        <path d="M10 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                        <path d="M8 21a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2"></path>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="../../2coordinator/host_record/host_record.php" data-bs-toggle="tooltip" title="Host Documents">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="26" height="26" stroke-width="2">
                                        <path d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"></path>
                                        <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2"></path>
                                        <path d="M12 12l0 .01"></path>
                                        <path d="M3 13a20 20 0 0 0 18 0"></path>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </ul>
                    <ul class="sidebar-menu">
                        <!-- Main Item with Toggle -->
                        <li data-bs-toggle="tooltip" title="Student Interns">
                            <a href="#" class="menu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="34" height="34" stroke-width="2">
                                    <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
                                </svg>
                            </a>
                            </a>
                        </li>

                        <!-- Sub-items (Initially Collapsed) -->
                        <ul id="submenu2" class="collapse sub-menu">
                            <li>
                                <a href="../../2coordinator/list_request/list_request.php" data-bs-toggle="tooltip" title="List">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="26" height="26" stroke-width="2">
                                        <path d="M9 10a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                        <path d="M6 21v-1a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v1"></path>
                                        <path d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-14z"></path>
                                    </svg>
                                </a>
                            </li>

                            <li>
                                <a href="../../2coordinator/intern_deploy/intern_deploy.php" data-bs-toggle="tooltip" title="Deployment">
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
                            <!-- 
                            <li>
                                <a href="../2coordinator/coordinatordashboard.php" data-bs-toggle="tooltip" title="Internship Status">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                                        <path d="M8 21l8 0"></path>
                                        <path d="M12 17l0 4"></path>
                                        <path d="M7 4l10 0"></path>
                                        <path d="M17 4v8a5 5 0 0 1 -10 0v-8"></path>
                                        <path d="M5 9m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                        <path d="M19 9m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                    </svg>
                                </a>
                            </li> -->
                        </ul>
                    </ul>
                    <ul class="sidebar-menu">
                        <!-- Main Item with Toggle -->
                        <li data-bs-toggle="tooltip" title="Internship Requirements">
                            <a href="#" class="menu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu3">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="34" height="34" stroke-width="2">
                                    <path d="M5 19l2.757 -7.351a1 1 0 0 1 .936 -.649h12.307a1 1 0 0 1 .986 1.164l-.996 5.211a2 2 0 0 1 -1.964 1.625h-14.026a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2h4l3 3h7a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </a>
                            </a>
                        </li>

                        <!-- Sub-items (Initially Collapsed) -->
                        <ul id="submenu3" class="collapse sub-menu">
                            <li>
                                <a href="../../2coordinator/predeploy/predeploy.php" data-bs-toggle="tooltip" title="Pre Deployment">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="26" height="26" stroke-width="2">
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                        <path d="M12 21h-5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v4.5"></path>
                                        <path d="M16.5 17.5m-2.5 0a2.5 2.5 0 1 0 5 0a2.5 2.5 0 1 0 -5 0"></path>
                                        <path d="M18.5 19.5l2.5 2.5"></path>
                                    </svg>
                                </a>
                            </li>


                            <li>
                                <a href="../../2coordinator/postdeploy/postdeploy.php" data-bs-toggle="tooltip" title="Post Deployment">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="26" height="26" stroke-width="2">
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                        <path d="M5 8v-3a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-5"></path>
                                        <path d="M6 14m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                        <path d="M4.5 17l-1.5 5l3 -1.5l3 1.5l-1.5 -5"></path>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="../../2coordinator/journal/journal.php" data-bs-toggle="tooltip" title="Student Journal">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="26" height="26" stroke-width="2">
                                        <path d="M6 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-11a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1m3 0v18"></path>
                                        <path d="M13 8l2 0"></path>
                                        <path d="M13 12l2 0"></path>
                                    </svg>
                                </a>
                            </li>


                            <!-- <li>
                                <a href="../../2coordinator/certificates/certificates.php" data-bs-toggle="tooltip" title="Certificates and Final Grades">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="26" height="26" stroke-width="2">
                                        <path d="M11 21h-5a1 1 0 0 1 -1 -1v-16a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v6"></path>
                                        <path d="M17.8 20.817l-2.172 1.138a.392 .392 0 0 1 -.568 -.41l.415 -2.411l-1.757 -1.707a.389 .389 0 0 1 .217 -.665l2.428 -.352l1.086 -2.193a.392 .392 0 0 1 .702 0l1.086 2.193l2.428 .352a.39 .39 0 0 1 .217 .665l-1.757 1.707l.414 2.41a.39 .39 0 0 1 -.567 .411l-2.172 -1.138z"></path>
                                    </svg>
                                </a>
                            </li> -->
                        </ul>
                    </ul>
                    <li>
                        <a href="../../2coordinator/chat/chat.php" data-bs-toggle="tooltip" title="Chat Room">
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
            <div><!-- ===================== Dashboard Main Layout (Ensuring Fixed Width) ===================== -->

                <div class="dashboard-container">
                    <!-- ✅ Full Width Good Day Admin -->
                    <h5 class="dashboard-header">
                        <!-- Good Day, <?php echo htmlspecialchars($username); ?> -->
                        <div class="dashboard-main">
                            <!-- Middle Content - Dashboard -->
                            <div class="dashboard-content">
                                <br>
                                <div class="d-flex justify-content-between align-items-center mb-3 announcement-header">
                                    <h5 class="announcement-title">Host Training Establishments</h5>
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control search-input" id="searchInput" placeholder="Search...">
                                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEstablishmentModal" data-tooltip="Add Establishment">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="20" height="20" stroke-width="3" stroke-linejoin="round" stroke-linecap="round" stroke="currentColor">
                                                <path d="M12 5l0 14"></path>
                                                <path d="M5 12l14 0"></path>
                                            </svg>
                                        </a>

                                    </div>
                                </div>
                                <!-- ✅Modal -->

                                <!-- Add Establishment Modal -->
                                <div class="modal fade" id="addEstablishmentModal" tabindex="-1" aria-labelledby="addEstablishmentModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addEstablishmentModalLabel">Add Establishment</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form id="addEstablishmentForm">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="establishmentName" class="form-label">Establishment Name</label>
                                                        <input type="text" class="form-control" id="establishmentName" name="establishmentName" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="establishmentAddress" class="form-label">Address</label>
                                                        <textarea class="form-control" id="establishmentAddress" name="establishmentAddress" rows="2" required></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="establishmentStatus" class="form-label">Status</label>
                                                        <select class="form-select" id="establishmentStatus" name="establishmentStatus" required>
                                                            <option value="approved">Approved</option>
                                                            <option value="pending" selected>Pending</option>
                                                            <option value="rejected">Rejected</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Add Establishment</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>




                                <!-- Users Account Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="userAccountTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th onclick="sortTable(0)">No. <i class="fa-solid fa-sort"></i></th>
                                                <th onclick="sortTable(1)">Name <i class="fa-solid fa-sort"></i></th>
                                                <th onclick="sortTable(2)">Address <i class="fa-solid fa-sort"></i></th>
                                                <th onclick="sortTable(3)">Status <i class="fa-solid fa-sort"></i></th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Fetched rows from fetch_establishment.php will be injected here -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- 🔥 Update Establishment Modal -->
                                <div class="modal fade" id="updateEstablishmentModal" tabindex="-1" aria-labelledby="updateEstablishmentModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form id="updateEstablishmentForm">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Update Establishment</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" id="update_hte_id" name="hte_id">
                                                    <input type="hidden" id="update_coordinator_id" name="coordinator_id" value="<?php echo $user_id; ?>">

                                                    <div class="mb-3">
                                                        <label for="updateName" class="form-label">Establishment Name</label>
                                                        <input type="text" class="form-control" id="updateName" name="name" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="updateAddress" class="form-label">Address</label>
                                                        <textarea class="form-control" id="updateAddress" name="address" rows="2" required></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="updateStatus" class="form-label">Status</label>
                                                        <select class="form-select" id="updateStatus" name="status" required>
                                                            <option value="approved">Approved</option>
                                                            <option value="pending">Pending</option>
                                                            <option value="rejected">Rejected</option>
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


                            </div>
                        </div>
                </div>
            </div>
        </div>



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

                element.addEventListener("click", function() {
                    var tooltipInstance = bootstrap.Tooltip.getInstance(this);
                    if (tooltipInstance) {
                        setTimeout(() => tooltipInstance.hide(), 100); // 100ms delay
                    }
                });
            });
        </script>


        <script>
            document.getElementById("addEstablishmentForm").addEventListener("submit", function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append("coordinator_id", <?php echo $user_id; ?>); // inject logged-in coordinator

                fetch("insert_establishment.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.text())
                    .then(response => {
                        if (response.trim() === "success") {
                            alert("Establishment added!");
                            document.getElementById("addEstablishmentForm").reset();
                            const modal = bootstrap.Modal.getInstance(document.getElementById('addEstablishmentModal'));
                            modal.hide();
                            loadEstablishments();
                        } else {
                            alert("Error saving data.");
                        }
                    })
                    .catch(err => console.error("Error:", err));
            });
        </script>

        <script>
            function loadEstablishments() {
                fetch("fetch_establishment.php")
                    .then(res => res.text())
                    .then(data => {
                        document.querySelector("#userAccountTable tbody").innerHTML = data;

                        // Reinitialize tooltips after load
                        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        tooltipTriggerList.map(function(tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });
                    });
            }

            document.addEventListener("DOMContentLoaded", loadEstablishments);
        </script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const updateModal = new bootstrap.Modal(document.getElementById('updateEstablishmentModal'));

                document.addEventListener("click", function(e) {
                    if (e.target.closest(".edit-btn")) {
                        const btn = e.target.closest(".edit-btn");
                        document.getElementById("update_hte_id").value = btn.getAttribute("data-id");
                        document.getElementById("updateName").value = btn.getAttribute("data-name");
                        document.getElementById("updateAddress").value = btn.getAttribute("data-address");
                        document.getElementById("updateStatus").value = btn.getAttribute("data-status");

                        updateModal.show();
                    }
                });
            });
        </script>

        <script>
            document.getElementById("updateEstablishmentForm").addEventListener("submit", function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                fetch("update_establishment.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.text())
                    .then(response => {
                        if (response.trim() === "success") {
                            alert("Establishment updated!");
                            const modal = bootstrap.Modal.getInstance(document.getElementById('updateEstablishmentModal'));
                            modal.hide();
                            loadEstablishments();
                        } else {
                            alert("Error updating data.");
                        }
                    })
                    .catch(err => console.error("Error:", err));
            });
        </script>

        <script>
            function deleteEstablishment(hte_id) {
                if (confirm("Are you sure you want to delete this establishment? This action cannot be undone.")) {
                    const formData = new FormData();
                    formData.append("hte_id", hte_id);

                    fetch("delete_establishment.php", {
                            method: "POST",
                            body: formData
                        })
                        .then(res => res.text())
                        .then(response => {
                            if (response.trim() === "success") {
                                alert("Establishment deleted!");
                                loadEstablishments();
                            } else {
                                alert("Error deleting data.");
                            }
                        })
                        .catch(err => console.error("Error:", err));
                }
            }
        </script>



        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const searchInput = document.getElementById("searchInput");
                const table = document.getElementById("userAccountTable");
                const addModal = document.getElementById("addEstablishmentModal");

                // Search bar function
                searchInput.addEventListener("keyup", function() {
                    const filter = searchInput.value.toLowerCase();
                    const rows = table.querySelectorAll("tbody tr");

                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(filter) ? "" : "none";
                    });
                });

                // Clear search when modal opens
                addModal.addEventListener('show.bs.modal', function() {
                    searchInput.value = "";
                    loadEstablishments(); // OPTIONAL → real-time refresh on modal open
                });

                // Initial table load
                loadEstablishments();

                function initializeTooltips() {
                    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                        new bootstrap.Tooltip(tooltipTriggerEl, {
                            boundary: 'window',
                            fallbackPlacements: ['top', 'bottom', 'left', 'right'],
                            html: true,
                            customClass: 'custom-tooltip' // 🔥 Apply your style
                        });
                    });
                }

            });
        </script>

        <script>
            function sortTable(columnIndex) {
                const table = document.getElementById("userAccountTable");
                const tbody = table.querySelector("tbody");
                const rows = Array.from(tbody.querySelectorAll("tr"));

                let ascending = table.getAttribute("data-sort-dir") !== "asc";

                rows.sort((a, b) => {
                    const aText = a.children[columnIndex].textContent.trim().toLowerCase();
                    const bText = b.children[columnIndex].textContent.trim().toLowerCase();

                    if (!isNaN(aText) && !isNaN(bText)) {
                        return ascending ? aText - bText : bText - aText;
                    }
                    return ascending ? aText.localeCompare(bText) : bText.localeCompare(aText);
                });

                rows.forEach(row => tbody.appendChild(row));
                table.setAttribute("data-sort-dir", ascending ? "asc" : "desc");
            }
        </script>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>