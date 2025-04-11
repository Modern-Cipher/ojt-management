<?php
session_start();
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgotpass.php"); // Prevent access without OTP verification
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow p-4">
                    <h2 class="text-center">Reset Your Password</h2>
                    <p class="text-center text-muted">Enter your new password below.</p>

                    <div id="alertBox" class="alert d-none"></div>

                    <form id="resetForm">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" id="new_password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" id="confirm_password" class="form-control" required>
                        </div>

                        <div class="d-grid">
                            <button type="button" id="resetPassword" class="btn btn-success">UPDATE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function () {
        $("#resetPassword").click(function (e) {
            e.preventDefault();
            var newPassword = $("#new_password").val();
            var confirmPassword = $("#confirm_password").val();

            if (newPassword === "" || confirmPassword === "") {
                showAlert("⚠️ All fields are required.", "danger");
                return;
            }
            if (newPassword !== confirmPassword) {
                showAlert("❌ Passwords do not match.", "danger");
                return;
            }

            $.ajax({
                type: "POST",
                url: "reset_password_process.php",
                data: { new_password: newPassword },
                dataType: "json",
                success: function (response) {
                    showAlert(response.message, response.status === "success" ? "success" : "danger");
                    if (response.status === "success") {
                        setTimeout(function () {
                            window.location.href = "successpass.php";
                        }, 3000);
                    }
                },
                error: function () {
                    showAlert("❌ Error resetting password.", "danger");
                }
            });
        });
    });

    function showAlert(message, type) {
        $("#alertBox").removeClass("d-none alert-success alert-danger").addClass("alert-" + type).html(message).fadeIn();
        setTimeout(function () {
            $("#alertBox").fadeOut();
        }, 3000);
    }
    </script>
</body>
</html>
