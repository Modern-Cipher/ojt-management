<?php
session_start();

// If user is already logged in, redirect them to their respective dashboard
if (isset($_SESSION["user_id"]) && isset($_SESSION["role"])) {
    switch ($_SESSION["role"]) {
        case 'admin':
            header("Location: ../1admin/admindashboard.php");
            exit();
        case 'coordinator':
            header("Location: ../2coordinator/coordinatordashboard.php");
            exit();
        case 'trainer':
            header("Location: ../3hte/htedashboard.php");
            exit();
        case 'student':
            header("Location: ../4student/studentdashboard.php");
            exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Internship Program</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" type="image/ico" href="../resources/siplogo.ico">
    <link rel="stylesheet" href="../login/css/login.css">
    <style>
        .col-md-6.bg-image {
            background-image: url("../resources/loginpage.jpg");
            background-size: cover;
            background-position: center;
        }

        .form-group input {
            font-size: 16px;
            /* border-radius: 8px;  Remove rounded corners */
            padding: 10px;
            border: 1px solid #ccc;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .btn-primary {
            background-color: green !important;
            border-color: green !important;
            /* border-radius: 8px; Remove rounded corners */
            padding: 12px 20px;
            font-size: 15px;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row no-gutter">
            <div class="col-md-6 bg-light d-flex align-items-center justify-content-center">
                <div class="login-left">
                    <img src="../resources/siplogo.png" class="logo" alt="User Icon">
                    <h2>Student Internship Program Management System</h2>
                    <p class="text-muted mb-4">Admin / Coordinator / Trainer / Student Login</p>
                    <form action="authenticate.php" method="POST">
                        <div class="form-group mb-3">
                            <input id="username" type="text" name="username" placeholder="Username / Email Address"
                                class="form-control border-0 shadow-sm px-4" required>
                        </div>
                        <div class="form-group mb-3">
                            <input id="password" type="password" name="password" placeholder="Password"
                                class="form-control border-0 shadow-sm px-4 text" required>
                        </div>
                        <div class="text-center d-flex justify-content-between mt-4">
                            <a href="../registration/registration.php" class="register">Student Registration</a>
                            <a href="../password/forgot_password.php" class="forgot">Forgot Password!</a>
                        </div>
                        <br><br>
                        <div>
                            <button type="submit" class="btn btn-primary btn-block text-uppercase mb-2 shadow-sm">LOGIN</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6 bg-image"></div>
            <div class="vertical-line"></div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>