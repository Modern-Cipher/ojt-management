<?php
session_start();
include("../../0config/database.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $hte_id = $_POST['hte_id'];

    $sql = "DELETE FROM hte WHERE hte_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hte_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
    $conn->close();
}
?>
