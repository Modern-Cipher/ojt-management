<?php
session_start();
if (!isset($_SESSION['activation_email'])) {
    header("Location: send_activation_email.php");
    exit();
}
$email = $_SESSION['activation_email'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activate Your Account</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" type="image/ico" href="../resources/siplogo.ico">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Ensure jQuery is included -->
</head>

<body>
    <div class="container text-center mt-5">
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-body">
                <h2 class="card-title text-info">Activate Your Account</h2>
                <p class="card-text">Enter the activation code sent to <strong><?= htmlspecialchars($email) ?></strong></p>
                <form id="activationForm" method="POST">
                    <div class="form-group">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>"> <!-- Hidden email input -->
                        <input type="text" name="guid" class="form-control" placeholder="Enter Activation Code" required>
                    </div>
                    <button type="submit" class="btn btn-success">Activate Account</button>
                </form>
                <p class="mt-3"><a href="../login/login.php">Back to Login</a></p>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#activationForm").submit(function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "process_activation.php", // Ensure this file exists
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.redirect;
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert("AJAX Error: " + xhr.status + " " + xhr.statusText);
                        console.error("Full Response:", xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>

</html>
