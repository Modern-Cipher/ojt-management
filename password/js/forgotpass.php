<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <a href="../login/login.php" class="btn btn-link">← Back to Login</a>

                <div class="card shadow p-4">
                    <h2 class="text-center">Forgot Password</h2>
                    <p class="text-center text-muted">Enter your email to receive a one-time OTP.</p>

                    <!-- Alert Box -->
                    <div id="alertBox" class="alert d-none"></div>

                    <form id="forgotForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <input type="email" id="email" class="form-control" required>
                                <button type="button" id="sendOTP" class="btn btn-success">SEND</button>
                            </div>
                        </div>
                        <p id="resendText" class="text-muted text-center">Click Resend After: 5:00 minutes</p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function () {
        $("#sendOTP").click(function (e) {
            e.preventDefault();
            var email = $("#email").val();

            if (email === "") {
                showAlert("⚠️ Email field is required.", "danger");
                return;
            }

            $.ajax({
                type: "POST",
                url: "forgot_process.php",
                data: { email: email },
                dataType: "json",
                success: function (response) {
                    showAlert(response.message, response.status === "success" ? "success" : "danger");
                    if (response.status === "success") {
                        $("#sendOTP").prop("disabled", true); 
                        startTimer(300);
                    }
                },
                error: function () {
                    showAlert("❌ Error sending request.", "danger");
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

    function startTimer(duration) {
        var timer = duration, minutes, seconds;
        var resendText = $("#resendText");

        var interval = setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);
            resendText.text("Click Resend After: " + minutes + ":" + (seconds < 10 ? "0" : "") + seconds + " minutes");

            if (--timer < 0) {
                clearInterval(interval);
                $("#sendOTP").prop("disabled", false);
                resendText.text("You can now request a new OTP.");
            }
        }, 1000);
    }
    </script>
</body>
</html>
