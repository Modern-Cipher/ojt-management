<?php
define('ROOT_DIR', realpath(__DIR__ . '/..'));
require_once ROOT_DIR . '/0config/database.php';

$error = filter_input(INPUT_GET, 'error', FILTER_SANITIZE_SPECIAL_CHARS);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container mt-5 form-container">
        <h1 class="text-center mb-4">Attendance Form</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form id="attendanceForm" novalidate>
            <div class="row mb-3">
                <div class="col-md-4 col-12 mb-3 mb-md-0">
                    <label for="school_id" class="form-label">School ID</label>
                    <input type="text" class="form-control" id="school_id" name="school_id" required>
                    <div class="invalid-feedback">School ID is required.</div>
                </div>
                <div class="col-md-4 col-12 mb-3 mb-md-0">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                    <div class="invalid-feedback">First Name is required.</div>
                </div>
                <div class="col-md-4 col-12">
                    <label for="middle_name" class="form-label">Middle Name</label>
                    <input type="text" class="form-control" id="middle_name" name="middle_name">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 col-12 mb-3 mb-md-0">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                    <div class="invalid-feedback">Last Name is required.</div>
                </div>
                <div class="col-md-4 col-12 mb-3 mb-md-0">
                    <label for="sex" class="form-label">Sex</label>
                    <select class="form-select" id="sex" name="sex" required>
                        <option value="" disabled selected>Select</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    <div class="invalid-feedback">Sex is required.</div>
                </div>
                <div class="col-md-4 col-12">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <div class="invalid-feedback">Valid Email is required.</div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6 col-12 mb-3 mb-md-0">
                    <label for="institute" class="form-label">Institute</label>
                    <select class="form-select" id="institute" name="institute" required>
                        <option value="" disabled selected>Select Institute</option>
                        <option value="Institute of Engineering and Applied Technology">Institute of Engineering and Applied Technology</option>
                        <option value="Institute of Management">Institute of Management</option>
                    </select>
                    <div class="invalid-feedback">Institute is required.</div>
                </div>
                <div class="col-md-6 col-12">
                    <label for="course" class="form-label">Course</label>
                    <select class="form-select" id="course" name="course" required>
                        <option value="" disabled selected>Select Course</option>
                    </select>
                    <div class="invalid-feedback">Course is required.</div>
                </div>
            </div>
            <div class="next-btn mb-3">
                <button type="button" id="nextBtn" class="btn btn-primary btn-wide">Next</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>