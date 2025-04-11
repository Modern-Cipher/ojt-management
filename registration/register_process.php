<?php
session_start();
include '../0config/database.php';

$response = ["success" => false, "errors" => []];

function generateGUID($length = 6) {
    return substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, $length);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $school_id = trim($_POST['school_id']); // VARCHAR in database
    $institute = trim($_POST['institute']);
    $course = trim($_POST['course']);
    $fname = trim($_POST['fname']);
    $mname = trim($_POST['mname']);
    $lname = trim($_POST['lname']);
    $sex = trim($_POST['sex']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate school ID
    $stmt = $conn->prepare("SELECT users_id FROM users WHERE school_id = ?");
    $stmt->bind_param("s", $school_id); // Changed from "i" to "s"
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $response['errors']['school_id'] = "ID Number already exists.";
    }
    $stmt->close();

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['errors']['email'] = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("SELECT users_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $response['errors']['email'] = "Email already exists.";
        }
        $stmt->close();
    }

    // Validate username
    $stmt = $conn->prepare("SELECT users_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $response['errors']['username'] = "Username already exists.";
    }
    $stmt->close();

    // Validate phone number
    if (!preg_match('/^\d{11}$/', $phone)) {
        $response['errors']['phone'] = "Phone number must be 11 digits.";
    }

    // Validate password
    if (strlen($password) < 5) {
        $response['errors']['password'] = "Password must be at least 5 characters.";
    }
    if ($password !== $confirm_password) {
        $response['errors']['confirm_password'] = "Passwords do not match.";
    }

    // If there are no validation errors, insert the data
    if (empty($response['errors'])) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $guid = generateGUID();
        $role = 'student';
        $activate = 0; // Default value

        $stmt = $conn->prepare("INSERT INTO users (school_id, institute, course, fname, mname, lname, sex, phone, address, email, username, password, role, guid, activate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssssssi", $school_id, $institute, $course, $fname, $mname, $lname, $sex, $phone, $address, $email, $username, $hashed_password, $role, $guid, $activate);

        if ($stmt->execute()) {
            $_SESSION['activation_email'] = $email;
            $_SESSION['activation_guid'] = $guid;
            $_SESSION['activation_status'] = $activate;

            // Send JSON response instead of redirecting
            $response["success"] = true;
            $response["redirect"] = "send_activation.php";
        } else {
            $response["errors"]["database"] = "Error inserting data: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Return JSON response
echo json_encode($response);
?>
