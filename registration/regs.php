<?php
// Include database connection
include_once '../0config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $fname = trim($_POST['fname']);
    $mname = trim($_POST['mname']);
    $lname = trim($_POST['lname']);
    $sex = $_POST['sex'];
    $institute = $_POST['institute'];
    $designation = trim($_POST['designation']);
    $role = $_POST['role'];
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    // Insert into database
    $sql = "INSERT INTO users (fname, mname, lname, sex, institute, designation, role, email, username, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $fname, $mname, $lname, $sex, $institute, $designation, $role, $email, $username, $password);
    
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href = '../login/login.php';</script>";
    } else {
        echo "<script>alert('Registration failed!');</script>";
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/ico" href="../resources/siplogo.ico">
    <title>Registration</title>
</head>
<body>
    <form action="regs.php" method="POST">
        <label>First Name:</label>
        <input type="text" name="fname" required><br>
        
        <label>Middle Name:</label>
        <input type="text" name="mname"><br>
        
        <label>Last Name:</label>
        <input type="text" name="lname" required><br>
        
        <label>Sex:</label>
        <select name="sex" required>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select><br>
        
        <label>Institute:</label>
        <select name="institute" required>
            <option value="Institute of Engineering and Applied Technology">Institute of Engineering and Applied Technology</option>
            <option value="Institute of Management">Institute of Management</option>
        </select><br>
        
        <label>Designation:</label>
        <input type="text" name="designation" required><br>
        
        <label>Role:</label>
        <select name="role" required>
            <option value="admin">Admin</option>
            <option value="coordinator">Coordinator</option>
            <option value="student">Student</option>
            <option value="trainer">Trainer</option>
            <option value="subtrainer">Subtrainer</option>
        </select><br>
        
        <label>Email:</label>
        <input type="email" name="email" required><br>
        
        <label>Username:</label>
        <input type="text" name="username" required><br>
        
        <label>Password:</label>
        <input type="password" name="password" required><br>
        
        <button type="submit">Register</button>
    </form>
</body>
</html>
