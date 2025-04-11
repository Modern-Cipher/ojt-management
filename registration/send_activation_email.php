<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require '../0config/email_config.php';

function sendActivationEmail($email, $guid) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;

        // Sender Info
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($email);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = 'Activate Your Account';
        $mail->Body = "<h3>Activate Your Account</h3>
                      <p>Here is your activation code:</p>
                      <h2 style='color:blue;'>$guid</h2>
                      <p>Enter this code on the activation page to activate your account.</p>";

        // Send email
        if (!$mail->send()) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
            return false;
        }
        error_log("Activation email successfully sent to: " . $email);
        return true;
    } catch (Exception $e) {
        error_log("Exception: " . $e->getMessage());
        return false;
    }
}

session_start();
include '../0config/database.php';

header('Content-Type: application/json'); // Ensures JSON response

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    // Check if email exists and is not activated
    $stmt = $conn->prepare("SELECT guid FROM users WHERE email = ? AND activate = 0");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($guid);
    $stmt->fetch();
    $stmt->close();

    if (!empty($guid)) {
        // Send activation email
        if (sendActivationEmail($email, $guid)) {
            $_SESSION['activation_email'] = $email;
            $response["success"] = true;
            $response["redirect"] = "enter_activation_code.php";
        } else {
            $response["message"] = "Error sending activation email.";
        }
    } else {
        $response["message"] = "Email not found or already activated.";
    }
}

echo json_encode($response);
exit();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Activation Email</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container text-center mt-5">
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-body">
                <h2 class="card-title text-info">Activate Your Account</h2>
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
                    url: "process_activation_email.php",
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
