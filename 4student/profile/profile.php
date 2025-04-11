<?php
session_start();
include("../../0config/session.php");
include("../../0config/database.php");

// Fetch user data from session or database
$user_id = $_SESSION['user_id'] ?? 0;
$query = "SELECT * FROM users WHERE users_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Account</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../4student/profile/css/profile.css">
</head>

<body>
    <div class="container-fluid profile-container">
        <div class="profile-header-container">
            <a href="../../4student/studentdashboard.php" class="back-btn">← Back</a>
            <h3 class="profile-header">User Profile</h3>
        </div>


        <div class="row profile-wrapper">
            <!-- Left Side: Profile Image -->
            <div class="col-lg-3 col-md-4 col-12 profile-left text-center">
                <img id="profileImage" src="<?php echo htmlspecialchars('../../upload_profile/' . ($user['image_profile'] ?? 'siplogo.png')); ?>" class="profile-image">

                <!-- Hidden file input -->
                <input type="file" id="fileInput" accept="image/png, image/jpeg" style="display: none;">

                <!-- Change Photo Button -->
                <button class="change-photo-btn" id="uploadBtn">Change Photo</button>
            </div>

            <!-- Right Side: Profile Form -->
            <div class="col-lg-9 col-md-8 col-12 profile-form">
                <form id="updateProfileForm">
                    <div class="row">
                        <div class="col-md-8 form-group">
                            <label>Student ID Number</label>
                            <input type="text" class="form-control" name="school_id" value="<?php echo htmlspecialchars($user['school_id'] ?? ''); ?>">
                            <small class="text-danger error-message" id="school_id-error"></small>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Sex</label>
                            <select class="form-control" name="sex">
                                <option value="male" <?php echo ($user['sex'] === 'male') ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo ($user['sex'] === 'female') ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>First Name</label>
                            <input type="text" class="form-control" name="fname" value="<?php echo htmlspecialchars($user['fname'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Middle Name</label>
                            <input type="text" class="form-control" name="mname" value="<?php echo htmlspecialchars($user['mname'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Last Name</label>
                            <input type="text" class="form-control" name="lname" value="<?php echo htmlspecialchars($user['lname'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Phone Number</label>
                            <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Home Address</label>
                            <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Institute</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['institute'] ?? ''); ?>" readonly>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Course</label>
                            <input type="text" class="form-control" name="course" value="<?php echo htmlspecialchars($user['course'] ?? ''); ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?>">
                            <small class="text-danger error-message" id="email-error"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>">
                            <small class="text-danger error-message" id="username-error"></small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Enter new password">
                            <small class="text-danger error-message" id="password-error"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm new password">
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">UPDATE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- ✅ Success Modal for Profile Picture Change -->
    <div class="modal fade" id="photoSuccessModal" tabindex="-1" aria-labelledby="photoSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="photoSuccessModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Profile photo updated successfully!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <!-- ✅ Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Success</h5>
                </div>
                <div class="modal-body">
                    Profile updated successfully!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("updateProfileForm").addEventListener("submit", function(event) {
            event.preventDefault();
            let formData = new FormData(this);

            fetch("update_profile.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text()) // Read response as text
                .then(text => {
                    try {
                        let data = JSON.parse(text); // Convert to JSON
                        if (data.status === "success") {
                            let successModal = new bootstrap.Modal(document.getElementById('successModal'));
                            successModal.show();
                        } else {
                            if (data.field) {
                                document.getElementById(data.field + "-error").textContent = data.message;
                            }
                        }
                    } catch (error) {
                        console.error("JSON Parse Error:", error);
                        console.log("Response from server:", text); // Show full response
                        alert("Unexpected server response. Check console.");
                    }
                })
                .catch(error => {
                    console.error("Fetch Error:", error);
                });
        });
    </script>
    <script>
        document.getElementById("uploadBtn").addEventListener("click", function() {
            document.getElementById("fileInput").click();
        });

        document.getElementById("fileInput").addEventListener("change", function(event) {
            let file = event.target.files[0];
            if (file) {
                let formData = new FormData();
                formData.append("profile_image", file);

                fetch("upload_profile.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            // ✅ Update Profile Image Instantly
                            document.getElementById("profileImage").src = data.filePath + "?t=" + new Date().getTime();

                            // ✅ Show Bootstrap Modal Instead of Alert
                            let successModal = new bootstrap.Modal(document.getElementById('photoSuccessModal'));
                            successModal.show();
                        } else {
                            alert("Error uploading image: " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Fetch Error:", error);
                    });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>