<?php
include("../../0config/database.php");

if (isset($_GET['email'])) {
    $email = trim($_GET['email']);
    
    $query = "SELECT COUNT(*) AS count FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    
    echo json_encode(["exists" => $count > 0]);
    
    $stmt->close();
    $conn->close();
}
?>
