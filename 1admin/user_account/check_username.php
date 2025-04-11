<?php
include("../../0config/database.php");

if (isset($_GET['username'])) {
    $username = trim($_GET['username']);
    
    $query = "SELECT COUNT(*) AS count FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    
    echo json_encode(["exists" => $count > 0]);
    
    $stmt->close();
    $conn->close();
}
?>
