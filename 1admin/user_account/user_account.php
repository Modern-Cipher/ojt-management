<?php
session_start();
include("../../0config/database.php"); // Ensure database connection
include("../../0config/session.php"); // Ensure session is included

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../0config/logout.php");
    exit();
}

// ✅ Get user ID and institute from session
$user_id = $_SESSION['user_id'];
$admin_institute = $_SESSION['institute'] ?? "";
$username = $_SESSION["username"] ?? "Admin";

// ✅ Fetch user details (image_profile, username, fname)
$sql = "SELECT image_profile, username, fname, role FROM users WHERE users_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// ✅ Determine the profile image path
$imageProfile = $user['image_profile'] ?? ''; // Default empty if NULL
$profilePath = "../../upload_profile/" . htmlspecialchars($imageProfile);
$defaultImage = "../../upload_profile/siplogo.png"; // Placeholder image

$userImage = (!empty($imageProfile) && file_exists(__DIR__ . "/" . $profilePath)) ? $profilePath : $defaultImage;

// ✅ Determine the display name (username or first name)
$usernameDisplay = !empty($user['fname']) ? htmlspecialchars($user['fname']) : htmlspecialchars($user['username']);

// ✅ Define the allowed roles
$allowed_roles = ['student', 'coordinator', 'trainer'];

// ✅ Fix: Correcting the number of parameters in bind_param()
$sql = "SELECT users_id, school_id, fname, mname, lname, email, username, course, role, users_account 
        FROM users 
        WHERE institute = ? AND role IN (?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $admin_institute, $allowed_roles[0], $allowed_roles[1], $allowed_roles[2]);
$stmt->execute();
$result = $stmt->get_result();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Custom CSS (Separate Files) -->
    <link rel="icon" type="image/ico" href="../../resources/siplogo.ico">
    <link rel="stylesheet" href="../../1admin/css/topbar.css">
    <link rel="stylesheet" href="../../1admin/css/leftsidebar.css">
    <link rel="stylesheet" href="../../1admin/css/user_account.css">
    <link rel="stylesheet" href="../../1admin/css/user_account_table.css">

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
            <div><!-- ===================== Dashboard Main Layout (Ensuring Fixed Width) ===================== -->

                <div class="dashboard-container">
                    <!-- ✅ Full Width Good Day Admin -->
                    <h5 class="dashboard-header">Good Day, <?php echo htmlspecialchars($username); ?>
                        <div class="dashboard-main">
                            <!-- Middle Content - Dashboard -->
                            <div class="dashboard-content">
                                <br>
                                <div class="d-flex justify-content-between align-items-center mb-3 announcement-header">
                                    <h5 class="announcement-title">User Account</h5>
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="text" class="form-control search-input" id="searchInput" placeholder="Search...">
                                        <!-- ✅ Corrected Add User Button -->
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                            <i class="fa-solid fa-plus"></i> Add User
                                        </button>
                                    </div>
                                </div>


                                <!-- ✅ Coordinator Modal -->
                                <!-- Add User Modal -->
                                <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addUserModalLabel">Add User Account</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="addUserForm" action="insert_user.php" method="POST">

                                                    <div id="errorMessage"></div> <!-- Validation Message Container -->

                                                    <!-- Role Selection -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Role</label>
                                                        <select class="form-select" name="role" id="roleSelect" required>
                                                            <option value="">Choose...</option>
                                                            <option value="coordinator">Coordinator</option>
                                                            <option value="trainer">Host Trainer</option>
                                                        </select>
                                                    </div>

                                                    <!-- Common Fields for Both Roles -->
                                                    <div class="row mb-3">
                                                        <div class="col">
                                                            <label class="form-label">First Name</label>
                                                            <input type="text" class="form-control" name="fname" id="fname" required>
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label">Last Name</label>
                                                            <input type="text" class="form-control" name="lname" id="lname" required>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col">
                                                            <label class="form-label">Sex</label>
                                                            <select class="form-select" name="sex" required>
                                                                <option value="">Choose...</option>
                                                                <option value="male">Male</option>
                                                                <option value="female">Female</option>
                                                            </select>
                                                        </div>

                                                        <!-- Course Title (For Coordinator) -->
                                                        <div class="col" id="courseField" style="display: none;">
                                                            <label class="form-label">Course Title</label>
                                                            <select class="form-select" name="course" id="courseSelect">
                                                                <option value="">Select Course Title...</option>
                                                            </select>
                                                        </div>

                                                        <!-- Designation (For Trainer) -->
                                                        <div class="col" id="designationField" style="display: none;">
                                                            <label class="form-label">Designation</label>
                                                            <input type="text" class="form-control" name="designation" id="designation">
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col">
                                                            <label class="form-label">Email Address (Optional)</label>
                                                            <input type="email" class="form-control" name="email" id="email">
                                                            <span id="emailError" class="text-danger small"></span>
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label">Username</label>
                                                            <input type="text" class="form-control" name="username" id="username" required>
                                                            <span id="usernameError" class="text-danger small"></span>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col">
                                                            <label class="form-label">Temporary Password</label>
                                                            <input type="password" class="form-control" name="password" id="password" required>
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label">Confirm Temporary Password</label>
                                                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                                                            <span id="passwordError" class="text-danger small"></span>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Edit User Modal -->
                                <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="editUserForm">
                                                    <input type="hidden" id="editUserId" name="user_id"> <!-- Hidden User ID -->

                                                    <!-- ID Number (Optional) -->
                                                    <div class="mb-3">
                                                        <label class="form-label">ID Number (Optional)</label>
                                                        <input type="text" class="form-control" id="editSchoolId" name="school_id">
                                                    </div>

                                                    <!-- First Name & Last Name -->
                                                    <div class="row mb-3">
                                                        <div class="col">
                                                            <label class="form-label">First Name</label>
                                                            <input type="text" class="form-control" id="editFname" name="fname" required>
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label">Last Name</label>
                                                            <input type="text" class="form-control" id="editLname" name="lname" required>
                                                        </div>
                                                    </div>

                                                    <!-- Role (Disabled Field) -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Role</label>
                                                        <input type="text" class="form-control" id="editRole" name="role" disabled>
                                                    </div>

                                                    <!-- Sex -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Sex</label>
                                                        <select class="form-select" id="editSex" name="sex" required>
                                                            <option value="">Choose...</option>
                                                            <option value="male">Male</option>
                                                            <option value="female">Female</option>
                                                        </select>
                                                    </div>


                                                    <!-- Email & Username (Optional) -->
                                                    <div class="row mb-3">
                                                        <div class="col">
                                                            <label class="form-label">Email Address (Optional)</label>
                                                            <input type="email" class="form-control" id="editEmail" name="email">
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label">Username (Optional)</label>
                                                            <input type="text" class="form-control" id="editUsername" name="username">
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>




                                <!-- ✅ Users Account Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="userAccountTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th onclick="sortTable(0)">No. <i class="fa-solid fa-sort"></i></th>
                                                <th onclick="sortTable(1)">ID Number <i class="fa-solid fa-sort"></i></th>
                                                <th onclick="sortTable(2)">Full Name <i class="fa-solid fa-sort"></i></th>
                                                <th onclick="sortTable(3)">Email Address <i class="fa-solid fa-sort"></i></th>
                                                <th onclick="sortTable(4)">Username <i class="fa-solid fa-sort"></i></th>
                                                <th onclick="sortTable(5)">Role <i class="fa-solid fa-sort"></i></th>
                                                <th onclick="sortTable(6)">Status <i class="fa-solid fa-sort"></i></th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $count = 1;
                                            while ($row = $result->fetch_assoc()) :
                                            ?>
                                                <tr>
                                                    <td data-label="No."><?php echo $count++; ?></td>
                                                    <td data-label="School ID"><?php echo htmlspecialchars($row['school_id'] ?? 'N/A'); ?></td>
                                                    <td data-label="Full Name"><?php echo htmlspecialchars(ucwords($row['fname']) . " " . ucwords($row['lname'])); ?></td>
                                                    <td data-label="Email Address"><?php echo htmlspecialchars($row['email'] ?? "N/A"); ?></td>
                                                    <td data-label="Username"><?php echo htmlspecialchars($row['username']); ?></td>
                                                    <td data-label="Role"><?php echo ucfirst(htmlspecialchars($row['role'])); ?></td>
                                                    <td data-label="Status">
                                                        <span class="badge status-badge <?php echo ($row['users_account'] == 'enabled') ? 'bg-success' : 'bg-danger'; ?>">
                                                            <?php echo ucfirst($row['users_account']); ?>
                                                        </span>
                                                    </td>
                                                    <td data-label="Actions">
                                                        <div class="d-flex align-items-center justify-content-start gap-2">
                                                            <button class="btn btn-outline-secondary btn-sm editUserBtn"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editUserModal"
                                                                data-id="<?php echo $row['users_id']; ?>"
                                                                data-school_id="<?php echo htmlspecialchars($row['school_id'] ?? ''); ?>"
                                                                data-fname="<?php echo htmlspecialchars($row['fname'] ?? ''); ?>"
                                                                data-lname="<?php echo htmlspecialchars($row['lname'] ?? ''); ?>"
                                                                data-role="<?php echo htmlspecialchars($row['role'] ?? ''); ?>"
                                                                data-sex="<?php echo htmlspecialchars($row['sex'] ?? ''); ?>"
                                                                data-email="<?php echo htmlspecialchars($row['email'] ?? ''); ?>"
                                                                data-username="<?php echo htmlspecialchars($row['username'] ?? ''); ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>


                                                            <div class="form-check form-switch" data-tooltip="Enable / Disable Account">
                                                                <input class="form-check-input user-status-toggle" type="checkbox"
                                                                    data-id="<?php echo $row['users_id']; ?>"
                                                                    <?php echo ($row['users_account'] == 'enabled') ? 'checked' : ''; ?>>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                </div>
            </div>
        </div>
        <!-- ✅ Modal for Success/Error Messages -->
        <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="messageModalLabel">System Notification</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center" id="modalMessage"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="closeModalBtn">OK</button>
                    </div>
                </div>
            </div>
        </div>


        <?php
        // ✅ NOW it's safe to close the connection after using $result in the table
        $stmt->close();
        $conn->close();
        ?>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const form = document.getElementById("addUserForm");
                const roleSelect = document.getElementById("roleSelect");
                const courseField = document.getElementById("courseField");
                const designationField = document.getElementById("designationField");
                const courseSelect = document.getElementById("courseSelect");

                const usernameInput = document.getElementById("username");
                const emailInput = document.getElementById("email");
                const passwordInput = document.getElementById("password");
                const confirmPasswordInput = document.getElementById("confirm_password");

                const usernameError = document.getElementById("usernameError");
                const emailError = document.getElementById("emailError");
                const passwordError = document.getElementById("passwordError");

                const modalMessage = document.getElementById("modalMessage");
                const messageModal = new bootstrap.Modal(document.getElementById("messageModal"));
                const addUserModal = new bootstrap.Modal(document.getElementById("addUserModal"));

                // ✅ Get the admin's institute from session
                const adminInstitute = "<?php echo htmlspecialchars($_SESSION['institute']); ?>";

                // ✅ Predefined Courses by Institute
                const coursesByInstitute = {
                    "Institute of Engineering and Applied Technology": [
                        "BS in Information Technology",
                        "BS in Geodetic Engineering",
                        "BS in Food Technology"
                    ],
                    "Institute of Management": [
                        "BS in Business Administration",
                        "BS in Hospitality Management"
                    ]
                };

                // ✅ Handle Role Selection
                roleSelect.addEventListener("change", function() {
                    if (roleSelect.value === "coordinator") {
                        courseField.style.display = "block";
                        designationField.style.display = "none";

                        // Populate Course Dropdown
                        courseSelect.innerHTML = '<option value="">Select Course Title...</option>';
                        if (coursesByInstitute[adminInstitute]) {
                            coursesByInstitute[adminInstitute].forEach(course => {
                                let option = document.createElement("option");
                                option.value = course;
                                option.textContent = course;
                                courseSelect.appendChild(option);
                            });
                        }
                    } else if (roleSelect.value === "trainer") {
                        courseField.style.display = "none";
                        designationField.style.display = "block";
                    } else {
                        courseField.style.display = "none";
                        designationField.style.display = "none";
                    }
                });

                // ✅ Live Validation: Check if Username Exists
                usernameInput.addEventListener("blur", function() {
                    let username = usernameInput.value.trim();
                    if (username !== "") {
                        fetch(`check_username.php?username=${username}`)
                            .then(response => response.json())
                            .then(data => {
                                usernameError.textContent = data.exists ? "Username is already taken!" : "";
                            });
                    }
                });

                // ✅ Live Validation: Check if Email Exists
                emailInput.addEventListener("blur", function() {
                    let email = emailInput.value.trim();
                    if (email !== "") {
                        fetch(`check_email.php?email=${email}`)
                            .then(response => response.json())
                            .then(data => {
                                emailError.textContent = data.exists ? "Email is already in use!" : "";
                            });
                    }
                });

                // ✅ Password Match Validation
                confirmPasswordInput.addEventListener("keyup", function() {
                    let password = passwordInput.value;
                    let confirmPassword = confirmPasswordInput.value;
                    passwordError.textContent = (password !== confirmPassword) ? "Passwords do not match!" : "";
                });

                // ✅ Form Submission with AJAX and Bootstrap Modal
                form.addEventListener("submit", function(e) {
                    e.preventDefault(); // Prevent Default Submission

                    let formData = new FormData(form);

                    fetch("insert_user.php", { // ✅ Ensure PHP Filename is Updated
                            method: "POST",
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                modalMessage.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                            } else if (data.success) {
                                // ✅ Close the "Add User" modal first
                                addUserModal.hide();

                                // ✅ Delay showing success modal for smooth transition
                                setTimeout(() => {
                                    modalMessage.innerHTML = `<div class="alert alert-success">${data.success}</div>`;
                                    messageModal.show();
                                }, 300); // 300ms delay for smooth effect

                                // ✅ Auto-refresh page after 2 seconds on success
                                setTimeout(() => {
                                    messageModal.hide();
                                    window.location.reload();
                                }, 2000);
                            } else {
                                modalMessage.innerHTML = `<div class="alert alert-danger">An unexpected error occurred!</div>`;
                                messageModal.show();
                            }
                        })
                        .catch(error => {
                            modalMessage.innerHTML = `<div class="alert alert-danger">An unexpected error occurred!</div>`;
                            messageModal.show();
                            console.error("Error:", error);
                        });
                });

                // ✅ Close Modal on Button Click
                document.getElementById("closeModalBtn").addEventListener("click", function() {
                    messageModal.hide();
                    window.location.reload();
                });
            });
        </script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const editUserModalEl = document.getElementById("editUserModal");
                let editUserModal = new bootstrap.Modal(editUserModalEl);

                document.querySelectorAll(".editUserBtn").forEach(button => {
                    button.addEventListener("click", function() {
                        const userId = this.getAttribute("data-id");
                        const schoolId = this.getAttribute("data-school_id") || "";
                        const fname = this.getAttribute("data-fname") || "";
                        const lname = this.getAttribute("data-lname") || "";
                        const role = this.getAttribute("data-role") || "";
                        const email = this.getAttribute("data-email") === "N/A" ? "" : this.getAttribute("data-email");
                        const username = this.getAttribute("data-username") || "";

                        document.getElementById("editUserId").value = userId;
                        document.getElementById("editSchoolId").value = schoolId;
                        document.getElementById("editFname").value = fname;
                        document.getElementById("editLname").value = lname;
                        document.getElementById("editRole").value = role;
                        document.getElementById("editEmail").value = email;
                        document.getElementById("editUsername").value = username;

                        // ✅ Fetch and set the Sex field
                        fetch(`fetch_sex.php?user_id=${userId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.sex) {
                                    let sexDropdown = document.getElementById("editSex");
                                    for (let option of sexDropdown.options) {
                                        if (option.value.toLowerCase() === data.sex.toLowerCase()) {
                                            option.selected = true;
                                            break;
                                        }
                                    }
                                }
                            })
                            .catch(error => console.error("Error fetching sex:", error));

                        editUserModal.show();
                    });
                });

                editUserModalEl.addEventListener("hidden.bs.modal", function() {
                    document.getElementById("editUserForm").reset();
                });

                document.getElementById("editUserForm").addEventListener("submit", function(e) {
                    e.preventDefault();
                    let formData = new FormData(this);

                    fetch("update_user.php", {
                            method: "POST",
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert("User updated successfully!");
                                editUserModal.hide();
                                window.location.reload();
                            } else {
                                alert("Error updating user: " + data.message);
                            }
                        })
                        .catch(error => console.error("Error:", error));
                });
            });
        </script>



        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const toggles = document.querySelectorAll(".user-status-toggle");

                toggles.forEach(toggle => {
                    toggle.addEventListener("change", function() {
                        let userId = this.getAttribute("data-id");
                        let newStatus = this.checked ? "enabled" : "disabled";
                        let statusBadge = this.closest("tr").querySelector(".status-badge"); // Find the badge element

                        // Send AJAX request to update user status
                        fetch("../../1admin/user_account/update_status.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: `user_id=${userId}&new_status=${newStatus}`
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // ✅ Update the UI badge immediately
                                    statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                                    statusBadge.classList.remove("bg-success", "bg-danger"); // Remove old class
                                    statusBadge.classList.add(newStatus === "enabled" ? "bg-success" : "bg-danger");
                                } else {
                                    alert("Error updating user status!");
                                    this.checked = !this.checked; // Revert toggle if update fails
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                alert("An error occurred while updating status.");
                                this.checked = !this.checked; // Revert toggle on error
                            });
                    });
                });
            });
        </script>



        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl, {
                        placement: 'right', // Ensure tooltip shows on the right
                        container: 'body' // Prevents clipping inside sidebar
                    });
                });
            });
        </script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const searchInput = document.getElementById("searchInput");
                const table = document.getElementById("userAccountTable");
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
                let table = document.getElementById("userAccountTable");
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

        <!-- ✅ Load Bootstrap Bundle (JS) at the end -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- ✅ Modal for Success/Error Messages -->

</body>

</html>