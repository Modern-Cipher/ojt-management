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
    <title>Certificates and Final Grades</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Custom CSS (Separate Files) -->
    <link rel="stylesheet" href="../../2coordinator/css/topbar.css">
    <link rel="stylesheet" href="../../2coordinator/css/leftsidebar.css">
    <link rel="stylesheet" href="../../2coordinator/certificates/css/certificates_body.css">
    <link rel="stylesheet" href="../../2coordinator/certificates/css/certificates.css">
    <link rel="stylesheet" href="../../2coordinator/certificates/css/certificates_table.css">
    <link rel="stylesheet" href="../../2coordinator/certificates/css/details.css">

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
                <div class="notification-container" data-bs-toggle="modal" data-bs-target="#notificationModal">
                    <i class="fa-solid fa-bell text-warning fs-5 notification-icon"></i>
                    <span class="notification-badge">99</span>
                </div>

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


                            <li>
                                <a href="../../2coordinator/certificates/certificates.php" data-bs-toggle="tooltip" title="Certificates and Final Grades">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="26" height="26" stroke-width="2">
                                        <path d="M11 21h-5a1 1 0 0 1 -1 -1v-16a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v6"></path>
                                        <path d="M17.8 20.817l-2.172 1.138a.392 .392 0 0 1 -.568 -.41l.415 -2.411l-1.757 -1.707a.389 .389 0 0 1 .217 -.665l2.428 -.352l1.086 -2.193a.392 .392 0 0 1 .702 0l1.086 2.193l2.428 .352a.39 .39 0 0 1 .217 .665l-1.757 1.707l.414 2.41a.39 .39 0 0 1 -.567 .411l-2.172 -1.138z"></path>
                                    </svg>
                                </a>
                            </li>
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
        </div>
        <!-- ===================== Dashboard Main Layout (Ensuring Fixed Width) ===================== -->

        <div class="dashboard-container">
            <!-- ✅ Full Width Good Day Admin -->
            <h5 class="dashboard-header">
                <!-- Good Day, <?php echo htmlspecialchars($username); ?> -->
                <div class="dashboard-main">
                    <!-- Middle Content - Dashboard -->
                    <div class="dashboard-content">
                        <br>
                        <div class="d-flex justify-content-between align-items-center mb-3 announcement-header">
                            <h5 class="announcement-title">Certificates & Final Grades</h5>
                            <div class="d-flex align-items-center">
                                <input type="text" class="form-control search-input" id="searchInput" placeholder="Search...">

                                <!-- ✅ Previous Button with SVG Icon and Tooltip -->
                                <button id="prevPage" class="btn btn-secondary btn-sm mx-2" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="Previous">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                                        <path d="M15 6l-6 6l6 6"></path>
                                    </svg>
                                </button>

                                <select id="rowsPerPage" class="form-select form-select-sm w-auto mx-1">
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>


                                <!-- ✅ Next Button with SVG Icon and Tooltip -->
                                <button id="nextPage" class="btn btn-secondary btn-sm mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Next">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                                        <path d="M9 6l6 6l-6 6"></path>
                                    </svg>
                                </button>
                            </div>


                        </div>

                        <!-- ✅ Modal -->

                        <!-- ✅ List and Request Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="intern_deploy_Table">
                                <thead class="table-dark">
                                    <tr>
                                        <th onclick="sortTable(0)">No. <i class="fa-solid fa-sort"></i></th>
                                        <th onclick="sortTable(1)">Student ID <i class="fa-solid fa-sort"></i></th>
                                        <th onclick="sortTable(2)">Name <i class="fa-solid fa-sort"></i></th>
                                        <th onclick="sortTable(3)">Certificates <i class="fa-solid fa-sort"></i></th>
                                        <th onclick="sortTable(4)">Grades <i class="fa-solid fa-sort"></i></th>
                                        <th onclick="sortTable(5)">Grade Resquest <i class="fa-solid fa-sort"></i></th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="intern_deploy_Table">
                                    <tr>
                                        <td data-label="No.">1</td>
                                        <td data-label="Student ID">20231003</td>
                                        <td data-label="Name">Medel Bunalade</td>
                                        <td data-label="Certificates">certificates.pdf</td>
                                        <td data-label="Grade">grades.pdf</td>
                                        <td data-label="Grade Request"><span class="badge bg-danger">New Request</span></td>
                                        <td data-label="Action">
                                            <div class="action-group">
                                                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Upload Certificates / Final Grades">
                                                    <i class="fas fa-upload"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-label="No.">2</td>
                                        <td data-label="Student ID">20231003</td>
                                        <td data-label="Name">Medel Bunalade</td>
                                        <td data-label="Certificates">certificates.pdf</td>
                                        <td data-label="Grade">grades.pdf</td>
                                        <td data-label="Grade Request"><span class="badge bg-success">Granted</span></td>
                                        <td data-label="Action">
                                            <div class="action-group">
                                                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Upload Certificates / Final Grades">
                                                    <i class="fas fa-upload"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <!-- ✅ Announcements Section -->
                    <div class="announcements">

                        <div class="container mt-3">
                            <div class="card student-card">
                                <div class="card-header">Internship Details</div>
                                <div class="card-body">
                                    <!-- Profile Picture -->
                                    <img src="../../resources/siplogo.png" alt="Profile Picture" class="profile-img2">

                                    <!-- Student Name -->
                                    <p class="student-name">Medel Bunalade</p>

                                    <!-- Personal Details -->
                                    <div class="info-section">
                                        <strong>Information</strong> <br>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                                            <path d="M7 12h3v4h-3z"></path>
                                            <path d="M10 6h-6a1 1 0 0 0 -1 1v12a1 1 0 0 0 1 1h16a1 1 0 0 0 1 -1v-12a1 1 0 0 0 -1 -1h-6"></path>
                                            <path d="M10 3m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z"></path>
                                            <path d="M14 16h2"></path>
                                            <path d="M14 12h4"></path>
                                        </svg> 2025-3sdcd <br>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                            <path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                            <path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855"></path>
                                        </svg> Meds <br>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                                            <path d="M12 15m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                            <path d="M10 7h4"></path>
                                            <path d="M10 18v4l2 -1l2 1v-4"></path>
                                            <path d="M10 19h-2a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-2"></path>
                                        </svg> BS Information Technology <br>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                                            <path d="M5 12l-2 0l9 -9l9 9l-2 0"></path>
                                            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"></path>
                                            <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"></path>
                                        </svg> San Miguel Bulacan
                                    </div>

                                    <!-- Contact Information -->
                                    <div class="info-section">
                                        <strong>Contact Information</strong> <br>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                                            <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"></path>
                                            <path d="M3 7l9 6l9 -6"></path>
                                        </svg> meds@gmail.com <br>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                                            <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"></path>
                                        </svg> 09876543211
                                    </div>

                                    <div class="info-section">
                                        <strong>Host Training Establishment</strong> <br>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                                            <path d="M9 11l3 3l8 -8"></path>
                                            <path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9"></path>
                                        </svg> <span class="badge bg-success">Deployed</span> <br>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                                            <path d="M3 21l18 0"></path>
                                            <path d="M9 8l1 0"></path>
                                            <path d="M9 12l1 0"></path>
                                            <path d="M9 16l1 0"></path>
                                            <path d="M14 8l1 0"></path>
                                            <path d="M14 12l1 0"></path>
                                            <path d="M14 16l1 0"></path>
                                            <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16"></path>
                                        </svg> PLDT Corp <br>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                                            <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                            <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"></path>
                                        </svg> Makti city, Manila
                                    </div>

                                    <div class="info-section">
                                        <strong>Host Trainer</strong> <br>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                                            <path d="M19.03 17.818a3 3 0 0 0 1.97 -2.818v-8a3 3 0 0 0 -3 -3h-12a3 3 0 0 0 -3 3v8c0 1.317 .85 2.436 2.03 2.84"></path>
                                            <path d="M10 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                            <path d="M8 21a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2"></path>
                                        </svg> Maria Agnes <br>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                                            <path d="M3 9a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-9z"></path>
                                            <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg> Manager <br>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                                            <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"></path>
                                            <path d="M3 7l9 6l9 -6"></path>
                                        </svg> maria@gmail.com <br>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                                            <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"></path>
                                        </svg> 09876543211
                                    </div>

                                    <!-- Toggle Button -->
                                    <p class="link-style text-center toggle-btn" id="toggleHistory">
                                        View All Deployment History
                                        <span id="toggleIcon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                                                <path d="M6 9l6 6l6 -6"></path>
                                            </svg>
                                        </span>
                                    </p>

                                    <!-- Request History Section (Hidden by Default) -->
                                    <div id="historySection" class="hidden">
                                        <hr>

                                        <!-- Current Respond -->
                                        <div class="info-section">
                                            <strong>Current Status:</strong> <br>
                                            <strong> Status:</strong> Pending <br>
                                            <strong>Assigned:</strong> PLDT Makati City <br>
                                            <strong>Date Assigned</strong> March 10, 2025 - 8:00PM <br>
                                        </div>

                                        <hr>

                                        <!-- Last Respond -->
                                        <div class="info-section">
                                            <strong>Last Status:</strong> <br>
                                            <strong> Status:</strong> Rejected <br>
                                            <strong>Assigned:</strong> PLDT Makati City <br>
                                            <strong>Date Assigned:</strong> March 10, 2025 - 8:00PM <br>
                                            <strong>Date Rejected:</strong> March 11, 2025 - 9:00PM
                                        </div>

                                        <hr>

                                        <div class="info-section">
                                            <strong>Last Status:</strong> <br>
                                            <strong> Status:</strong> Rejected <br>
                                            <strong>Assigned:</strong> PLDT Makati City <br>
                                            <strong>Date Assigned:</strong> March 10, 2025 - 8:00PM <br>
                                            <strong>Date Deployed:</strong> March 11, 2025 - 9:00PM
                                        </div>
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
            const searchInput = document.getElementById("searchInput");
            const table = document.getElementById("intern_deploy_Table");
            const rows = Array.from(table.querySelector("tbody").rows);

            // ✅ Search Functionality
            searchInput.addEventListener("keyup", function() {
                const searchValue = searchInput.value.toLowerCase();
                rows.forEach(row => {
                    row.style.display = row.innerText.toLowerCase().includes(searchValue) ? "" : "none";
                });
            });
        });

        // ✅ Sorting Functionality (Ascending/Descending)
        let sortOrder = {}; // Track column sort order

        function sortTable(columnIndex) {
            let table = document.getElementById("intern_deploy_Table");
            let rows = Array.from(table.querySelector("tbody").rows);

            sortOrder[columnIndex] = !sortOrder[columnIndex]; // Toggle sorting order

            rows.sort((a, b) => {
                let aValue = a.cells[columnIndex].textContent.trim().toLowerCase();
                let bValue = b.cells[columnIndex].textContent.trim().toLowerCase();

                if (!isNaN(aValue) && !isNaN(bValue)) { // Check if values are numbers
                    return sortOrder[columnIndex] ? aValue - bValue : bValue - aValue;
                }

                return sortOrder[columnIndex] ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
            });

            table.querySelector("tbody").append(...rows);
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleBtn = document.getElementById("toggleHistory");
            const historySection = document.getElementById("historySection");
            const toggleIcon = document.getElementById("toggleIcon");

            toggleBtn.addEventListener("click", function() {
                // Toggle visibility
                if (historySection.classList.contains("hidden")) {
                    historySection.classList.remove("hidden");
                    // Change icon to UP
                    toggleIcon.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="24" height="24" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" stroke="currentColor">
                    <path d="M6 15l6 -6l6 6"></path>
                </svg>
            `;
                } else {
                    historySection.classList.add("hidden");
                    // Change icon to DOWN
                    toggleIcon.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                    <path d="M6 9l6 6l6 -6"></path>
                </svg>
            `;
                }
            });
        });
    </script>


    <!-- ✅ FontAwesome Icons (Make sure you include this in your project) -->
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>