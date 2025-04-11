<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../resources/siplogo.ico">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap");

        body {
            font-family: "Poppins", sans-serif !important;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
            position: relative;
        }

        .container-fluid {
            height: 100%;
        }

        .row {
            height: 100%;
        }

        .col-md-4 {
            display: flex;
            align-items: start;
        }

        .back-to-login {
            position: absolute;
            top: 0;
            left: 0;
            padding: 10px;
            text-decoration: none;
            color: rgb(68, 68, 68);
            box-sizing: border-box;
        }

        .back-to-login:hover,
        .back-to-login:active,
        .back-to-login:focus {
            text-decoration: none;
            outline: none;
        }

        #forgotPasswordForm {
            width: 300px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .btn-primary {
            width: 100%;
        }

        #message {
            margin-top: 10px;
            text-align: center;
        }


        .email-not-found-alert {
            display: block !important;
            width: 100% !important;
            margin-top: 10px !important;
            text-align: center !important;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <a href="../login/login.php" class="back-to-login">← Back to Login</a>
            </div>
            <div class="col-md-4 d-flex align-items-center justify-content-center">
                <form id="forgotPasswordForm" action="send_otp.php" method="POST">
                    <h2>Forgot Password</h2>
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Send OTP</button>
                    <div id="message"></div>
                </form>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#forgotPasswordForm").submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "send_otp.php",
                    data: $(this).serialize(),
                    success: function(response) {
                        $("#message").html(response).show(); // Display the message
                        setTimeout(function() {
                            $("#message").fadeOut("slow");
                        }, 3000); // 3 seconds
                    }
                });
            });
        });
    </script>
</body>

</html>