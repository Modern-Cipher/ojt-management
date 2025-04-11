<?php
session_start();
include("../../0config/database.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $hte_id = $_POST['hte_id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $status = $_POST['status'];
    $coordinator_id = $_POST['coordinator_id'];

    $sql = "UPDATE hte 
            SET hte_name = ?, hte_address = ?, hte_status = ?, coordinator_id = ? 
            WHERE hte_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $name, $address, $status, $coordinator_id, $hte_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
    $conn->close();
}
?>
