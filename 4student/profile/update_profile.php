<?php
session_start();
include("../../0config/database.php");

// Force JSON output
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set("display_errors", 1);
ob_start(); // Start output buffering to prevent extra output

$response = ["status" => "error", "message" => "Something went wrong", "field" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'] ?? 0;

    if ($user_id == 0) {
        ob_end_clean();
        echo json_encode(["status" => "error", "message" => "User not logged in"]);
        exit();
    }

    // Fetch form data safely
    $school_id = $_POST['school_id'] ?? "";
    $sex = $_POST['sex'] ?? "";
    $fname = $_POST['fname'] ?? "";
    $mname = $_POST['mname'] ?? "";
    $lname = $_POST['lname'] ?? "";
    $phone = $_POST['phone'] ?? "";
    $address = $_POST['address'] ?? "";
    $email = $_POST['email'] ?? "";
    $username = $_POST['username'] ?? "";
    $course = $_POST['course'] ?? "";
    $password = $_POST['password'] ?? "";
    $confirm_password = $_POST['confirm_password'] ?? "";

    // ✅ Validate Student ID
    $stmt = $conn->prepare("SELECT users_id FROM users WHERE school_id = ? AND users_id != ?");
    $stmt->bind_param("si", $school_id, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        ob_end_clean();
        echo json_encode(["status" => "error", "message" => "Student ID is already in use.", "field" => "school_id"]);
        $stmt->close();
        exit();
    }
    $stmt->close(); // Close only once

    // ✅ Validate Email
    $stmt = $conn->prepare("SELECT users_id FROM users WHERE email = ? AND users_id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        ob_end_clean();
        echo json_encode(["status" => "error", "message" => "Email is already in use.", "field" => "email"]);
        $stmt->close();
        exit();
    }
    $stmt->close(); 

    // ✅ Validate Username
    $stmt = $conn->prepare("SELECT users_id FROM users WHERE username = ? AND users_id != ?");
    $stmt->bind_param("si", $username, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        ob_end_clean();
        echo json_encode(["status" => "error", "message" => "Username is already taken.", "field" => "username"]);
        $stmt->close();
        exit();
    }
    $stmt->close();

    // ✅ Password validation (only update if passwords match)
    $update_password = "";
    if (!empty($password)) {
        if ($password !== $confirm_password) {
            ob_end_clean();
            echo json_encode(["status" => "error", "message" => "Passwords do not match.", "field" => "password"]);
            exit();
        }
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $update_password = ", password = ?, temppass = NULL"; // Remove temp pass
    }

    // ✅ Fetch current session data from database
    $stmt = $conn->prepare("SELECT role, institute FROM users WHERE users_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    $stmt->close();

    // ✅ Update user profile
    $query = "UPDATE users SET 
        school_id = ?, sex = ?, fname = ?, mname = ?, lname = ?, 
        phone = ?, address = ?, email = ?, username = ?, course = ?, updated_on = CURRENT_TIMESTAMP 
        $update_password WHERE users_id = ?";

    if (!empty($password)) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssssssssi", $school_id, $sex, $fname, $mname, $lname, $phone, $address, $email, $username, $course, $hashed_password, $user_id);
    } else {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssssssi", $school_id, $sex, $fname, $mname, $lname, $phone, $address, $email, $username, $course, $user_id);
    }

    if ($stmt->execute()) {
        // ✅ Update session data in database
        $sessionData = json_encode([
            "user_id" => $user_id,
            "role" => $userData['role'],
            "institute" => $userData['institute'],
            "username" => $username
        ]);

        $stmt = $conn->prepare("UPDATE sessions SET session_data = ? WHERE users_id = ?");
        $stmt->bind_param("si", $sessionData, $user_id);
        $stmt->execute();
        $stmt->close(); // Close properly

        // ✅ Update session variable
        $_SESSION['username'] = $username;

        ob_end_clean(); // Clean output before returning JSON
        echo json_encode(["status" => "success", "message" => "Profile updated successfully!"]);
    } else {
        ob_end_clean();
        echo json_encode(["status" => "error", "message" => "Failed to update profile."]);
    }
}

exit();
