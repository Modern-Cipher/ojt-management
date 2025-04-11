<?php
session_start();
$message = "";

if (isset($_SESSION['activation_response'])) {
    $message = $_SESSION['activation_response']['message'];
    unset($_SESSION['activation_response']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Activation Email</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" type="image/ico" href="../resources/siplogo.ico">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container text-center mt-5">
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-body">
                <h2 class="card-title text-info">Activate Your Account</h2>
                <?php if (!empty($message)): ?>
                    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <p class="card-text">Enter your email to receive an activation link.</p>
                <form id="activationForm" method="POST">
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Activation Email</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#activationForm").submit(function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "send_activation_email.php",
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
                        alert("Error processing request. Please try again.");
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>

</html>
