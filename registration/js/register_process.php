<?php
include("../0config/database.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $school_id = $_POST["school_id"];
    $fname = $_POST["fname"];
    $mname = $_POST["mname"];
    $lname = $_POST["lname"];
    $sex = $_POST["sex"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $institute = $_POST["institute"];
    $course = $_POST["course"];
    $year_section = $_POST["year_section"];

    // Insert into database
    $query = "INSERT INTO users (school_id, fname, mname, lname, sex, email, username, password, phone, address, institute, course, year_section, role)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'student')";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssssssss", $school_id, $fname, $mname, $lname, $sex, $email, $username, $password, $phone, $address, $institute, $course, $year_section);

    if ($stmt->execute()) {
        echo "<script>alert('Registration Successful!'); window.location.href='../login/login.php';</script>";
    } else {
        echo "<script>alert('Error: Could not register.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
