<?php
session_start();
include '../0config/database.php';

$institutes = [];
$sql = "SELECT DISTINCT institute FROM users";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $institutes[] = $row['institute'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../registration/css/registration.css">
    <link rel="icon" type="image/ico" href="../resources/siplogo.ico">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="header">
            <a href="../login/login.php" class="back-btn">← Back to Login</a>
            <span class="system-title">Student Internship Program <br> Management System</span>
        </div>

        <h2 class="title text-center">Student Registration Form</h2>

        <form id="registrationForm" method="POST">
            <div class="row justify-content-center">
                <div class="col-md-3 mb-3">
                    <label>School ID Number</label>
                    <input type="text" name="school_id" class="form-control" required>
                    <div id="school_id_error" class="text-danger"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Institute</label>
                    <select name="institute" id="institute" class="form-control" required>
                        <option value="">Select Institute..</option>
                        <?php foreach ($institutes as $institute) : ?>
                            <option value="<?= $institute ?>"><?= $institute ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Course</label>
                    <select name="course" id="course" class="form-control" required>
                        <option value="">Select Course..</option>
                    </select>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-3 mb-3"><label>First Name</label><input type="text" name="fname" class="form-control" required></div>
                <div class="col-md-3 mb-3"><label>Middle Name</label><input type="text" name="mname" class="form-control"></div>
                <div class="col-md-3 mb-3"><label>Last Name</label><input type="text" name="lname" class="form-control" required></div>
                <div class="col-md-3 mb-3"><label>Sex</label>
                    <select name="sex" class="form-control" required>
                        <option value="">Choose..</option>
                        <option value="female">Female</option>
                        <option value="male">Male</option>
                    </select>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-3 mb-3"><label>Phone Number</label><input type="text" name="phone" class="form-control" required>
                    <div id="phone_error" class="text-danger"></div>
                </div>
                <div class="col-md-9 mb-3"><label>Address</label><input type="text" name="address" class="form-control" required></div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-6 mb-3"><label>Email Address</label><input type="email" name="email" class="form-control" required>
                    <div id="email_error" class="text-danger"></div>
                </div>
                <div class="col-md-6 mb-3"><label>Username</label><input type="text" name="username" class="form-control" required>
                    <div id="username_error" class="text-danger"></div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-6 mb-3"><label>Password</label><input type="password" name="password" class="form-control" required>
                    <div id="password_error" class="text-danger"></div>
                </div>
                <div class="col-md-6 mb-3"><label>Confirm Password</label><input type="password" name="confirm_password" class="form-control" required>
                    <div id="confirm_password_error" class="text-danger"></div>
                </div>
            </div>

            <div class="row justify-content-end mt-4">
                <div class="col-md-3 text-right">
                    <button type="submit" class="register-btn btn btn-success w-100">REGISTER</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('#institute').change(function() {
                var institute = $(this).val();
                if (institute) {
                    $.ajax({
                        type: 'POST',
                        url: 'get_courses.php',
                        data: {
                            institute: institute
                        },
                        success: function(data) {
                            $('#course').html(data);
                        }
                    });
                } else {
                    $('#course').html('<option value="">Select Course..</option>');
                }
            });

            $(document).ready(function() {
                $('#registrationForm').submit(function(e) {
                    e.preventDefault();

                    $.ajax({
                        type: 'POST',
                        url: 'register_process.php',
                        data: $(this).serialize(),
                        dataType: "json", // Ensures response is treated as JSON
                        success: function(response) {
                            if (response.success) {
                                window.location.href = response.redirect; // Redirect correctly
                            } else {
                                // Display validation errors
                                if (response.errors) {
                                    for (var key in response.errors) {
                                        $('#' + key + '_error').html(response.errors[key]);
                                    }
                                } else {
                                    alert(response.message || "An unexpected error occurred.");
                                }
                            }
                        },
                        error: function(xhr) {
                            alert("Error processing request. Please try again.");
                            console.error(xhr.responseText);
                        }
                    });
                });
            });

        });
    </script>

    </script>
</body>

</html>