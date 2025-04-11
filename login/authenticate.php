<?php
session_start();
include("../0config/database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Fetch user details
    $query = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user["password"])) {
            // Generate a unique session ID
            session_regenerate_id(true);
            $session_id = session_id();
            $user_id = $user["users_id"];
            $user_role = $user["role"];
            $institute = $user["institute"];
            $username = $user["username"];

            // Store session in DB, but DON'T delete existing ones (to allow multiple users)
            $session_data = json_encode(["user_id" => $user_id, "role" => $user_role, "institute" => $institute, "username" => $username]);

            $insertSession = "INSERT INTO sessions (sessions_id, users_id, session_data) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insertSession);
            $stmt->bind_param("sis", $session_id, $user_id, $session_data);
            $stmt->execute();

            // Store session variables
            $_SESSION["user_id"] = $user_id;
            $_SESSION["role"] = $user_role;
            $_SESSION["institute"] = $institute;
            $_SESSION["username"] = $username;

            // Redirect based on role
            switch ($user_role) {
                case 'admin':
                    header("Location: ../1admin/admindashboard.php");
                    break;
                case 'coordinator':
                    header("Location: ../2coordinator/coordinatordashboard.php");
                    break;
                case 'trainer':
                    header("Location: ../3hte/htedashboard.php");
                    break;
                case 'student':
                    header("Location: ../4student/studentdashboard.php");
                    break;
                default:
                    session_unset();
                    session_destroy();
                    header("Location: ../login/login.php?error=invalid_role");
                    exit();
            }
            exit();
        } else {
            echo "<script>alert('Incorrect password!'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.location.href='login.php';</script>";
    }
}
?>
