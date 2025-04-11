<?php session_start(); session_destroy(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Updated</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="container text-center">
        <h2>Password Updated Successfully!</h2>
        <p>You can now login with your new password.</p>
        <a href="../login/login.php" class="btn btn-success">LOGIN NOW</a>
    </div>
</body>
</html>
