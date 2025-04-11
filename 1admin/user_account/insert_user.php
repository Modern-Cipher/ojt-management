<?php
session_start();
include("../../0config/database.php"); // ✅ Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ✅ Ensure admin's institute is set
    if (!isset($_SESSION['institute']) || empty($_SESSION['institute'])) {
        echo json_encode(["error" => "Admin institute is missing! Please log in again."]);
        exit();
    }
    $admin_institute = $_SESSION['institute']; // ✅ Get admin's institute from session

    // ✅ Retrieve Form Data
    $fname = trim($_POST["fname"]);
    $lname = trim($_POST["lname"]);
    $role = trim($_POST["role"]); // Must be either "coordinator" or "trainer"
    $sex = trim($_POST["sex"]);
    $email = !empty($_POST["email"]) ? trim($_POST["email"]) : null; // Optional email
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // ✅ Conditional Fields (Based on Role)
    $course = ($role === "coordinator") ? trim($_POST["course"] ?? "") : null;
    $designation = ($role === "trainer" && !empty($_POST["designation"])) ? trim($_POST["designation"]) : null;

    // ✅ Required Fields Validation
    if (empty($fname) || empty($lname) || empty($username) || empty($password) || empty($confirm_password) || empty($role)) {
        echo json_encode(["error" => "Please fill in all required fields!"]);
        exit();
    }

    // ✅ Ensure role-based validation
    if ($role === "coordinator" && empty($course)) {
        echo json_encode(["error" => "Please select a course for Coordinator!"]);
        exit();
    }

    // ✅ Ensure Passwords Match
    if ($password !== $confirm_password) {
        echo json_encode(["error" => "Passwords do not match!"]);
        exit();
    }

    // ✅ Check if Username Already Exists
    $check_sql = "SELECT username FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $check_stmt->close();
        echo json_encode(["error" => "Username already exists! Choose a different username."]);
        exit();
    }
    $check_stmt->close();

    // ✅ Check if Email Exists (Only if provided)
    if (!empty($email)) {
        $check_email_sql = "SELECT email FROM users WHERE email = ?";
        $check_email_stmt = $conn->prepare($check_email_sql);
        $check_email_stmt->bind_param("s", $email);
        $check_email_stmt->execute();
        $check_email_stmt->store_result();

        if ($check_email_stmt->num_rows > 0) {
            $check_email_stmt->close();
            echo json_encode(["error" => "Email already exists! Please use a different email."]);
            exit();
        }
        $check_email_stmt->close();
    }

    // ✅ Hash the Password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Default Values
    $users_account = "enabled"; // Default enabled account
    $temp_pass = $hashed_password; // Store hashed password as temp password
    $chat_status = "offline";
    $image_profile = "siplogo.png"; // ✅ Correct Default Profile Image Name
    $created_on = date("Y-m-d H:i:s");

    // ✅ Insert Query (Now Includes `institute` and correct `image_profile`)
    $sql = "INSERT INTO users (fname, lname, role, sex, course, designation, institute, email, username, password, temppass, users_account, chat_stats, image_profile, created_on)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssss", $fname, $lname, $role, $sex, $course, $designation, $admin_institute, $email, $username, $hashed_password, $temp_pass, $users_account, $chat_status, $image_profile, $created_on);

    if ($stmt->execute()) {
        echo json_encode(["success" => "User account created successfully!"]);
    } else {
        echo json_encode(["error" => "Error creating account: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
