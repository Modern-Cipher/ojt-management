<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
        }

        #resetPasswordForm {
            width: 300px;
            /* Adjust as needed */
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .btn-primary {
            width: 100%;
        }
    </style>
</head>

<body>

    <form id="resetPasswordForm">
        <h2>Reset Password</h2>
        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
    </form>
    <div id="message" class="text-center mt-3"></div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#resetPasswordForm").submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "update_password.php",
                    data: $(this).serialize(),
                    success: function(response) {
                        $("#message").html(response).show(); // Make sure the message is shown
                        setTimeout(function() {
                            $("#message").fadeOut("slow");
                        }, 3000); // 3000 milliseconds = 3 seconds
                    }
                });
            });
        });
    </script>
</body>

</html>